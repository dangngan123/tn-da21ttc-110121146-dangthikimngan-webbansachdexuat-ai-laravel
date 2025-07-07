<?php

namespace App\Livewire;

use Livewire\Component;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ChatInteraction;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ChatbotComponent extends Component
{
    public $name;
    public $email;
    public $phone;
    public $supportOption;
    public $showForm = true;
    public $messages = [];
    public $userMessage;
    public $loading = false;
    public $sessionKey;
    public $guestToken;
    public $isEditing = false;
    public $suggestions = [];
    public $language = 'vi';
    public $showSuggestions = false;
    public $messageCount = 0;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'phone' => 'required|regex:/^\d{10}$/',
        'supportOption' => 'required|string',
    ];

    public function getSupportOptionLabel()
    {
        return match ($this->supportOption) {
            'tu_van_mua_sach' => 'Tư vấn mua sách',
            'ho_tro_don_hang' => 'Hỗ trợ đơn hàng',
            'doi_tra' => 'Đổi trả',
            default => 'Hỗ trợ chung',
        };
    }

    public function mount()
    {
        if (!session()->has('guest_token')) {
            $this->guestToken = uniqid('guest_');
            session(['guest_token' => $this->guestToken]);
        } else {
            $this->guestToken = session('guest_token');
        }

        if (session()->has('chat_user_name')) {
            $this->name = session('chat_user_name');
            $this->email = session('chat_user_email');
            $this->phone = session('chat_user_phone');
            $this->supportOption = session('chat_support_option');
            $this->sessionKey = session('chat_session_key');
            $this->showForm = false;
            $this->messages = session('chat_messages_' . $this->sessionKey, []);

            foreach ($this->messages as &$message) {
                if (!isset($message['timestamp'])) {
                    $message['timestamp'] = 'Unknown';
                }
            }
            $this->messageCount = session('chat_message_count_' . $this->sessionKey, count($this->messages));
            $this->showSuggestions = session('chat_show_suggestions_' . $this->sessionKey, ($this->messageCount == 1));
            session(['chat_messages_' . $this->sessionKey => $this->messages]);

            if (Auth::check()) {
                ChatInteraction::where('guest_token', $this->guestToken)
                    ->whereNull('user_id')
                    ->update(['user_id' => Auth::id()]);
            }
        } elseif (Auth::check()) {
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
            $this->showForm = true;
            ChatInteraction::where('guest_token', $this->guestToken)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        } else {
            $this->showForm = true;
        }

        $this->language = session('language', 'vi');
        $this->updateSuggestions();

        try {
            if (!Schema::hasTable('products') || Product::count() === 0) {
                $this->messages[] = [
                    'user' => '',
                    'bot' => 'Hiện tại kho sách đang trống. Vui lòng liên hệ iamkimngan197@gmail.com để đặt sách.',
                    'timestamp' => now()->format('H:i')
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error checking products table', ['error' => $e->getMessage()]);
        }
    }

    public function startChat()
    {
        $this->validate();

        $this->sessionKey = uniqid('chat_');
        session(['chat_session_key' => $this->sessionKey]);

        session([
            'chat_user_name' => $this->name,
            'chat_user_email' => $this->email,
            'chat_user_phone' => $this->phone,
            'chat_support_option' => $this->supportOption,
            'show_form' => false,
        ]);

        $this->showForm = false;

        $welcomeMessage = "Chào {$this->name}! Tôi là trợ lý sách. Bạn cần hỗ trợ gì về {$this->getSupportOptionLabel()}?";
        $this->messages[] = [
            'user' => '',
            'bot' => $welcomeMessage,
            'timestamp' => now()->format('H:i')
        ];

        $this->messageCount = 1;
        $this->showSuggestions = true;

        session(['chat_messages_' . $this->sessionKey => $this->messages]);
        session(['chat_message_count_' . $this->sessionKey => $this->messageCount]);
        session(['chat_show_suggestions_' . $this->sessionKey => $this->showSuggestions]);
        $this->updateSuggestions();
        $this->dispatch('scroll-to-bottom');
        $this->dispatch('updateSuggestions');
    }

    public function updateSuggestions()
    {
        $this->suggestions = match ($this->supportOption) {
            'tu_van_mua_sach' => ['#SáchMới', '#KhuyếnMãi', '#TiểuThuyết', '#SáchThiếuNhi'],
            'ho_tro_don_hang' => ['#TrạngTháiĐơn', '#HủyĐơn', '#ThanhToán', '#VậnChuyển'],
            'doi_tra' => ['#ChínhSáchĐổi', '#HoànTiền', '#ĐổiSách', '#ThờiGianĐổi', '#HỗTrợĐổi', '#LiênHệ'],
            default => ['#HỗTrợ', '#LiênHệ', '#Khác'],
        };
    }

    public function setSuggestion($tag)
    {
        $this->userMessage = $tag;
        $this->dispatch('focusInput');
    }

    public function sendMessage()
    {
        if (empty($this->userMessage)) {
            return;
        }

        $this->loading = true;
        $this->dispatch('newMessage');

        $messageTime = now()->format('H:i');
        $trimmedMessage = trim($this->userMessage);

        if (strlen($trimmedMessage) < 5 || in_array(strtolower($trimmedMessage), ['hi', 'hello', 'alo', '?'])) {
            $responseMessage = match ($this->supportOption) {
                'doi_tra' => 'Xin lỗi, tôi chưa hiểu rõ câu hỏi của bạn. Bạn có thể nói chi tiết hơn không? Ví dụ: "Chính sách đổi sách thế nào?" hoặc "Tôi muốn hoàn tiền."',
                default => 'Xin lỗi, tôi chưa hiểu rõ câu hỏi của bạn. Bạn có thể nói chi tiết hơn không? Ví dụ: "Tôi muốn tìm sách cho trẻ 10 tuổi" hoặc "Đơn hàng của tôi đang ở đâu?"',
            };
            $this->messages[] = [
                'user' => $this->userMessage,
                'bot' => $responseMessage,
                'timestamp' => $messageTime
            ];
            $this->messageCount++;
            $this->showSuggestions = ($this->messageCount < 2);
            $this->userMessage = '';
            $this->loading = false;
            session(['chat_messages_' . $this->sessionKey => $this->messages]);
            session(['chat_message_count_' . $this->sessionKey => $this->messageCount]);
            session(['chat_show_suggestions_' . $this->sessionKey => $this->showSuggestions]);
            $this->dispatch('newMessage');
            $this->dispatch('scroll-to-bottom');
            $this->dispatch('updateSuggestions');
            return;
        }

        // Trích xuất từ khóa và độ tuổi từ câu hỏi
        $extracted = $this->extractKeywords($trimmedMessage);
        $keywords = $extracted['keywords'];
        $age = $extracted['age'];

        $searchResult = null;
        if ($this->supportOption === 'tu_van_mua_sach') {
            $query = Product::with('category')->where('quantity', '>', 0);
            if ($age) {
                $query->where(function ($q) use ($age) {
                    $q->where('age', 'LIKE', "%{$age}%")
                      ->orWhere('age', 'LIKE', '%trẻ em%')
                      ->orWhere('age', 'LIKE', '%thiếu nhi%')
                      ->orWhere('age', 'LIKE', '%'.($age-2).'-'.($age+2).'%')
                      ->orWhere('age', 'LIKE', '%'.$age.'+%');
                });
            }
            foreach ($keywords as $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"])
                      ->orWhereRaw('LOWER(author) LIKE ?', ["%{$keyword}%"])
                      ->orWhereRaw('LOWER(publisher) LIKE ?', ["%{$keyword}%"]);
                });
            }
            $products = $query->take(3)->get();
            if (!$products->isEmpty()) {
                $searchResult = "Gợi ý sách:<ol>";
                foreach ($products as $product) {
                    $slug = $product->slug ?? \Illuminate\Support\Str::slug($product->name);
                    $price = number_format($product->sale_price ?: $product->regular_price, 0, ',', '.');
                    $link = route('details', ['slug' => $slug]);
                    $searchResult .= "<li>Sách \"{$product->name}\" (Tác giả: " . ($product->author ?? 'Không rõ') . ", Nhà xuất bản: " . ($product->publisher ?? 'Không rõ') . ", Độ tuổi: " . ($product->age ?? 'Không xác định') . ", Số lượng: {$product->quantity}) - Giá: {$price} VNĐ. <a href=\"{$link}\">Xem chi tiết</a></li>";
                }
                $searchResult .= "</ol>";

                // Sử dụng trực tiếp $searchResult làm câu trả lời để giữ đường link
                $answer = $searchResult;

                // Ghi log để debug
                Log::info('Search result', ['searchResult' => $searchResult]);

                // Lưu vào chat_interactions
                ChatInteraction::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'support_option' => $this->supportOption,
                    'session_key' => $this->sessionKey,
                    'guest_token' => $this->guestToken,
                    'question' => $this->userMessage,
                    'answer' => $answer,
                    'status' => 'new',
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'messages' => json_encode([
                        [
                            'user' => $this->userMessage,
                            'bot' => $answer,
                            'timestamp' => now()->toDateTimeString(),
                            'replied_by' => 'Chatbot',
                        ],
                    ]),
                ]);

                $this->messages[] = [
                    'user' => $this->userMessage,
                    'bot' => $answer,
                    'timestamp' => $messageTime
                ];
                $this->messageCount++;
                $this->showSuggestions = ($this->messageCount < 2);
                $this->userMessage = '';
                $this->loading = false;

                session(['chat_messages_' . $this->sessionKey => $this->messages]);
                session(['chat_message_count_' . $this->sessionKey => $this->messageCount]);
                session(['chat_show_suggestions_' . $this->sessionKey => $this->showSuggestions]);
                $this->updateSuggestions();
                $this->dispatch('newMessage');
                $this->dispatch('scroll-to-bottom');
                $this->dispatch('updateSuggestions');
                return;
            } else {
                $searchResult = $age
                    ? "Không tìm thấy sách phù hợp cho độ tuổi {$age}. Vui lòng thử lại với tên sách, tác giả, nhà xuất bản hoặc độ tuổi khác."
                    : "Không tìm thấy sách phù hợp trong kho. Vui lòng thử lại với tên sách, tác giả, nhà xuất bản hoặc độ tuổi.";
            }
        }

        // Xây dựng ngữ cảnh chỉ khi không tìm thấy sách hoặc không phải tư vấn mua sách
        $context = "Bạn là trợ lý hữu ích cho website bán sách. Trả lời bằng ngôn ngữ {$this->language} (Tiếng Việt). Hãy trả lời ngắn gọn, chính xác, và tự nhiên dựa trên thông tin tôi cung cấp và lịch sử trò chuyện. Nếu có kết quả tìm kiếm sách, sử dụng kết quả đó và giữ nguyên đường link chi tiết trong câu trả lời (dùng định dạng HTML <a href=\"...\">Xem chi tiết</a>). Chỉ đề xuất sách có số lượng trong kho lớn hơn 0. Nếu câu hỏi liên quan đến độ tuổi, ưu tiên sách có độ tuổi phù hợp.\n";

        if ($searchResult) {
            $context .= "Kết quả tìm kiếm sách:\n{$searchResult}\n\n";
        }

        $recentMessages = array_slice($this->messages, -5);
        if (!empty($recentMessages)) {
            $context .= "Lịch sử trò chuyện:\n";
            foreach ($recentMessages as $msg) {
                if (!empty($msg['user'])) {
                    $context .= "Người dùng: {$msg['user']}\n";
                }
                $context .= "Bot: {$msg['bot']}\n";
            }
            $context .= "\n";
        }

        if ($this->supportOption === 'ho_tro_don_hang') {
            $userId = Auth::check() ? Auth::id() : null;
            $orders = Order::where('user_id', $userId)->get();

            if ($orders->isEmpty()) {
                $context .= "Người dùng này chưa có đơn hàng nào.\n";
            } else {
                $context .= "Thông tin đơn hàng:\n";
                foreach ($orders as $order) {
                    $context .= "Mã đơn: {$order->id}\n";
                    $context .= "Tổng tiền: " . number_format($order->total, 3, ',', '.') . " VNĐ\n";
                    $context .= "Tên người nhận: {$order->name}\n";
                    $context .= "Số điện thoại: {$order->phone}\n";
                    $context .= "Tỉnh/Thành phố: {$order->province}\n\n";
                }
            }
        } elseif ($this->supportOption === 'doi_tra') {
            $context .= "Chính sách đổi trả:\n";
            $context .= "- Sách được đổi trong vòng 7 ngày kể từ ngày nhận hàng.\n";
            $context .= "- Sách phải còn nguyên vẹn, không bị hư hỏng.\n";
            $context .= "- Liên hệ qua email: iamkimngan197@gmail.com hoặc phone: 0795405536 để được hỗ trợ.\n\n";
        } else {
            // Lấy danh sách sách theo độ tuổi nếu có, nếu không lấy sách phổ biến
            $query = Product::with('category')->where('quantity', '>', 0);
            if ($age) {
                $query->where(function ($q) use ($age) {
                    $q->where('age', 'LIKE', "%{$age}%")
                      ->orWhere('age', 'LIKE', '%trẻ em%')
                      ->orWhere('age', 'LIKE', '%thiếu nhi%')
                      ->orWhere('age', 'LIKE', '%'.($age-2).'-'.($age+2).'%')
                      ->orWhere('age', 'LIKE', '%'.$age.'+%');
                });
            }
            $products = $query->take(3)->get();
            $context .= "Danh sách sách hiện có trong kho (số lượng > 0):\n";
            foreach ($products as $product) {
                $slug = $product->slug ?? \Illuminate\Support\Str::slug($product->name);
                $link = route('details', ['slug' => $slug]);
                $context .= "Tên sách: {$product->name}\n";
                $context .= "Tác giả: {$product->author}\n";
                $context .= "Nhà xuất bản: " . ($product->publisher ?? 'Không rõ') . "\n";
                $context .= "Giá: " . number_format($product->sale_price ?: $product->regular_price, 3, ',', '.') . " VNĐ\n";
                $context .= "Danh mục: " . ($product->category->name ?? 'Không xác định') . "\n";
                $context .= "Độ tuổi: " . ($product->age ?? 'Không xác định') . "\n";
                $context .= "Số lượng trong kho: {$product->quantity}\n";
                $context .= "Link chi tiết: <a href=\"{$link}\">Xem chi tiết</a>\n\n";
            }
        }

        $existing = ChatInteraction::where('question', $this->userMessage)->first();
        if ($existing) {
            $answer = $existing->answer;
        } else {
            $response = Prism::text()
                ->using(Provider::Gemini, 'gemini-1.5-flash')
                ->withSystemPrompt('Bạn là trợ lý hữu ích cho website bán sách. Trả lời ngắn gọn, chính xác, tự nhiên bằng tiếng Việt. Dựa trên lịch sử trò chuyện và dữ liệu được cung cấp. Nếu có kết quả tìm kiếm sách, sử dụng kết quả đó và giữ nguyên đường link chi tiết trong câu trả lời (dùng định dạng HTML <a href=\"...\">Xem chi tiết</a>). Chỉ đề xuất tối đa 3 sách có số lượng trong kho lớn hơn 0. Nếu câu hỏi liên quan đến độ tuổi, ưu tiên sách có độ tuổi phù hợp.')
                ->withPrompt($context . "\nCâu hỏi hiện tại: " . $this->userMessage)
                ->asText();
            $answer = $response->text;

            // Ghi log để debug
            Log::info('Prism AI answer', ['answer' => $answer]);

            ChatInteraction::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'support_option' => $this->supportOption,
                'session_key' => $this->sessionKey,
                'guest_token' => $this->guestToken,
                'question' => $this->userMessage,
                'answer' => $answer,
                'status' => 'new',
                'user_id' => Auth::check() ? Auth::id() : null,
                'messages' => json_encode([
                    [
                        'user' => $this->userMessage,
                        'bot' => $answer,
                        'timestamp' => now()->toDateTimeString(),
                        'replied_by' => 'Chatbot',
                    ],
                ]),
            ]);
        }

        $this->messages[] = [
            'user' => $this->userMessage,
            'bot' => $answer,
            'timestamp' => $messageTime
        ];
        $this->messageCount++;
        $this->showSuggestions = ($this->messageCount < 2);
        $this->userMessage = '';
        $this->loading = false;

        session(['chat_messages_' . $this->sessionKey => $this->messages]);
        session(['chat_message_count_' . $this->sessionKey => $this->messageCount]);
        session(['chat_show_suggestions_' . $this->sessionKey => $this->showSuggestions]);
        $this->updateSuggestions();
        $this->dispatch('newMessage');
        $this->dispatch('scroll-to-bottom');
        $this->dispatch('updateSuggestions');
    }

    protected function extractKeywords($message)
    {
        $stopWords = ['tôi', 'muốn', 'có', 'không', 'là', 'về', 'gì', 'nào', 'mua', 'tìm', 'sách', 'cho', 'gợi', 'ý', 'danh', 'mục', 'thể', 'loại'];
        $message = trim(strtolower($message));
        $message = preg_replace('/[.,!?]/', '', $message);
        $words = explode(' ', $message);

        $age = null;
        if (preg_match('/(\d+)\s*tuổi/', $message, $matches)) {
            $age = (int)$matches[1];
        }

        $keywords = array_filter($words, fn($word) => strlen($word) > 2 && !in_array($word, $stopWords));

        return [
            'keywords' => $keywords,
            'age' => $age
        ];
    }

    public function updateInfo()
    {
        $this->validate();

        try {
            session([
                'chat_user_name' => $this->name,
                'chat_user_email' => $this->email,
                'chat_user_phone' => $this->phone,
                'chat_support_option' => $this->supportOption,
            ]);

            ChatInteraction::where('session_key', $this->sessionKey)
                ->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'support_option' => $this->supportOption,
                ]);

            Log::info('User updated chat info', ['session_key' => $this->sessionKey]);
            flash()->success('Thông tin của bạn đã được cập nhật thành công.');
            $this->isEditing = false;
            $this->updateSuggestions();
        } catch (\Exception $e) {
            Log::error('Error updating chat info', ['error' => $e->getMessage()]);
            session()->flash('error', 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    }

    public function loadFullHistory()
    {
        try {
            $interactions = ChatInteraction::where('session_key', $this->sessionKey)
                ->orWhere('guest_token', $this->guestToken)
                ->orderBy('created_at', 'asc')
                ->get();

            $this->messages = [];
            foreach ($interactions as $interaction) {
                $messages = json_decode($interaction->messages, true);
                if ($messages) {
                    foreach ($messages as $msg) {
                        $this->messages[] = [
                            'user' => $msg['user'],
                            'bot' => $msg['bot'],
                            'timestamp' => \Carbon\Carbon::parse($msg['timestamp'])->format('H:i'),
                        ];
                    }
                }
            }
            session(['chat_messages_' . $this->sessionKey => $this->messages]);
            $this->messageCount = count($this->messages);
            session(['chat_message_count_' . $this->sessionKey => $this->messageCount]);
            Log::info('Loaded full chat history', ['session_key' => $this->sessionKey, 'message_count' => $this->messageCount]);
            $this->dispatch('scroll-to-bottom');
        } catch (\Exception $e) {
            Log::error('Error loading full history', ['error' => $e->getMessage()]);
            session()->flash('error', 'Có lỗi khi tải lịch sử hội thoại.');
        }
    }

    public function clearHistory()
    {
        try {
            session()->forget('chat_messages_' . $this->sessionKey);
            session()->forget('chat_message_count_' . $this->sessionKey);
            session()->forget('chat_show_suggestions_' . $this->sessionKey);

            ChatInteraction::where('session_key', $this->sessionKey)
                ->orWhere('guest_token', $this->guestToken)
                ->delete();

            $this->messages = [];
            $this->messageCount = 0;
            $this->showSuggestions = false;
            $this->showForm = true;
            session()->forget([
                'chat_user_name',
                'chat_user_email',
                'chat_user_phone',
                'chat_support_option',
                'chat_session_key'
            ]);

            Log::info('Chat history cleared', ['session_key' => $this->sessionKey, 'guest_token' => $this->guestToken]);
            flash()->success('Lịch sử hội thoại đã được xóa.');
        } catch (\Exception $e) {
            Log::error('Error clearing chat history', ['error' => $e->getMessage()]);
            session()->flash('error', 'Có lỗi khi xóa lịch sử hội thoại.');
        }
    }

    public function endChat()
    {
        try {
            $this->showForm = true;
            session()->forget([
                'chat_user_name',
                'chat_user_email',
                'chat_user_phone',
                'chat_support_option',
                'chat_session_key'
            ]);
            Log::info('Chat session ended', ['session_key' => $this->sessionKey]);
            flash()->success('Phiên trò chuyện đã kết thúc.');
        } catch (\Exception $e) {
            Log::error('Error ending chat session', ['error' => $e->getMessage()]);
            session()->flash('error', 'Có lỗi khi kết thúc phiên trò chuyện.');
        }
    }

    public function render()
    {
        return view('livewire.chatbot-component');
    }
}