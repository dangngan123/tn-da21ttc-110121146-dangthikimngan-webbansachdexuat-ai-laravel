<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Contact;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ManageContactComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pagesize = 5;
    public $processing = false;
    public $replyMessage = '';
    public $showReplyModal = false;
    public $selectedContactId; // For Detail Modal
    public $replyContactId; // For Reply Modal
    public $search = '';
    public $filter_status = '';

    protected $rules = [
        'replyMessage' => 'required|min:10'
    ];

    public function openReplyModal($contactId)
    {
        $this->replyContactId = $contactId; // Use separate variable for Reply Modal
        $this->replyMessage = '';
        $this->showReplyModal = true;
    }

    public function closeReplyModal()
    {
        Log::info('Closing reply modal', [
            'showReplyModal' => $this->showReplyModal,
        ]);
        $this->showReplyModal = false;
        $this->replyMessage = '';
        $this->replyContactId = null;
    }

    public function openDetailModal($contactId)
    {
        $this->selectedContactId = $contactId;
        $this->dispatch('openDetailModal', $contactId);
    }

    public function closeDetailModal()
    {
        $this->selectedContactId = null;
        $this->dispatch('closeDetailModal');
    }

    public function sendEmail($contactId)
    {
        $this->validate();

        $this->processing = true;

        try {
            $contact = Contact::find($contactId);

            if (!$contact) {
                session()->flash('error', 'Không tìm thấy thông tin liên hệ. Vui lòng kiểm tra lại.');
                return;
            }

            $emailContent = '
            <!DOCTYPE html>
            <html lang="vi">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Phản hồi từ Nhà sách Panda</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
                    .header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee; }
                    .header h2 { color: #e74c3c; margin: 0; font-size: 24px; }
                    .content { padding: 20px 0; }
                    .content p { margin: 10px 0; }
                    .reply-section { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #e74c3c; margin: 15px 0; border-radius: 4px; }
                    .reply-section p { margin: 0; }
                    .footer { text-align: center; padding-top: 20px; border-top: 1px solid #eee; color: #777; }
                    .footer p { margin: 5px 0; }
                    .footer a { color: #e74c3c; text-decoration: none; }
                    .footer a:hover { text-decoration: underline; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Phản hồi từ Nhà sách Panda</h2>
                    </div>
                    <div class="content">
                        <p>Kính gửi Anh/Chị <strong>' . htmlspecialchars($contact->name) . '</strong>,</p>
                        <p>Chúng tôi xin chân thành cảm ơn Anh/Chị đã dành thời gian liên hệ với Nhà sách Panda. Sự quan tâm và ý kiến của Anh/Chị là nguồn động lực quý giá để chúng tôi không ngừng cải thiện và mang đến trải nghiệm tốt nhất.</p>
                        <p>Để giải đáp thắc mắc của Anh/Chị, chúng tôi xin phản hồi như sau:</p>
                        <div class="reply-section">
                            <p>' . nl2br(htmlspecialchars($this->replyMessage)) . '</p>
                        </div>
                        <p>Nếu Anh/Chị có bất kỳ thắc mắc nào khác hoặc cần thêm thông tin, xin vui lòng liên hệ với chúng tôi qua email <a href="mailto:iamkimngan197@gmail.com">iamkimngan197@gmail.com</a> hoặc số điện thoại <strong>0795405536</strong>. Chúng tôi luôn sẵn sàng hỗ trợ!</p>
                        <p>Mời Anh/Chị ghé thăm <a href="' . url('/') . '">website của chúng tôi</a> để khám phá thêm nhiều tựa sách hấp dẫn.</p>
                    </div>
                    <div class="footer">
                        <p>Trân trọng,</p>
                        <p>Đội ngũ Hỗ trợ Khách hàng</p>
                        <p>Nhà sách Panda</p>
                    </div>
                </div>
            </body>
            </html>';

            Mail::html($emailContent, function ($message) use ($contact) {
                $message->to($contact->email)
                    ->subject('Phản hồi từ Nhà sách Panda');
            });

            $contact->status = 1;
            $success = $contact->save();
            Log::info('Contact status updated', [
                'contact_id' => $contact->id,
                'status' => $contact->status,
                'success' => $success,
            ]);

            if (!$success) {
                throw new \Exception('Không thể cập nhật trạng thái liên hệ trong cơ sở dữ liệu.');
            }

            session()->flash('success', 'Email phản hồi đã được gửi thành công! Chúng tôi sẽ tiếp tục hỗ trợ bạn trong thời gian sớm nhất.');
            $this->closeReplyModal();
            $this->resetPage();
        } catch (\Exception $e) {
            Log::error('Error in sendEmail', [
                'contact_id' => $contactId,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Có lỗi xảy ra khi gửi email: ' . $e->getMessage());
        } finally {
            $this->processing = false;
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filter_status = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Contact::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter_status !== '', function ($query) {
                $query->where('status', $this->filter_status);
            })
            ->orderBy('created_at', 'desc');

        $contacts = $query->paginate($this->pagesize);

        Log::info('Rendering ManageContactComponent', [
            'contacts' => $contacts->pluck('id', 'status')->toArray(),
        ]);

        return view('livewire.admin.manage-contact-component', ['contacts' => $contacts]);
    }
}