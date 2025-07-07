<div x-data="{ open: false, isEditing: false, menuOpen: false, name: @entangle('name'), email: @entangle('email'), phone: @entangle('phone'), showSuggestions: @entangle('showSuggestions').defer }"
    x-init="
         $watch('open', (value) => {
             const sliders = document.querySelectorAll('.slider-arrow.slider-arrow-2 .slider-btn');
             sliders.forEach(slider => {
                 if (value) {
                     slider.classList.add('hidden');
                 } else {
                     slider.classList.remove('hidden');
                 }
             });
         });
         console.log('Initial showForm:', @js($showForm));
     "
    class="fixed bottom-4 right-4" style="z-index: 99999;">
    <!-- Nút bật chatbot -->
    <button x-show="!open" x-on:click="open = true" x-cloak class="bg-white rounded-full w-[60px] h-[60px] shadow-lg flex items-center justify-center transition duration-300 hover:scale-105">
        <img src="{{ asset('assets/imgs/logo/logochat.png') }}" alt="Chatbot Logo" style="width: 40px; height: 40px">
    </button>

    <!-- Khung chat -->
    <div x-show="open" x-cloak class="mt-2 w-80 bg-white border rounded-lg shadow-lg p-4">
        <!-- Header -->
        <div class="relative mb-2">
            <div class="flex items-center justify-between">
                <button x-on:click="open = false" class="close-button">
                    <i class="fas fa-times text-lg"></i>
                </button>
                <!-- Nút hamburger chỉ hiển thị khi $showForm là false -->
                <button x-show="!@js($showForm)" x-on:click="menuOpen = !menuOpen" class="hamburger-button" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <i class="fas fa-bars text-lg text-gray-600"></i>
                </button>
            </div>
            <div class="mt-3 flex items-center space-x-2">
                <h3 class="text-lg font-semibold text-gray-800" style="font-size: 20px; margin-top: -10px;">HỖ TRỢ TRỰC TUYẾN</h3>
                <div x-show="@js($showForm)" x-transition.opacity x-cloak style="display: flex; align-items: center; background-color: #C12530; padding: 8px; border-radius: 4px;">
                    <img src="{{ asset('assets/imgs/logo/logochat2.png') }}" alt="Icon nhỏ" style="width: 50px; height: 50px;">
                    <span class="text-sm text-white" style="margin-left: 8px; font-size: 13px;">
                        Sẵn lòng giải đáp mọi thắc mắc.
                    </span>
                </div>
            </div>
            <div x-show="name && email && phone" class="relative">
                <div x-show="menuOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="absolute right-0 top-[42px] w-64 bg-gradient-to-b from-white via-gray-50 to-red-50 border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden pointer-events-auto">
                    <a href="#"
                        x-on:click.prevent="isEditing = true; menuOpen = false"
                        class="w-full text-left px-6 py-3.5 text-sm text-gray-800 hover:bg-red-200 hover:text-red-800 flex items-center transition-all duration-200 border-b border-gray-100">
                        <i class="fas fa-edit mr-4 text-red-600" style="margin-left: 20px;"></i> Chỉnh sửa thông tin
                    </a>
                    <br>
                    <a href="#"
                        wire:click.prevent="clearHistory"
                        x-on:click="menuOpen = false"
                        class="w-full text-left px-6 py-3.5 text-sm text-gray-800 hover:bg-red-200 hover:text-red-800 flex items-center transition-all duration-200 border-b border-gray-100">
                        <i class="fas fa-trash mr-4 text-red-600" style="margin-left: 20px;"></i> Xóa lịch sử
                    </a>
                    <br>
                    <a href="#"
                        wire:click.prevent="endChat"
                        x-on:click="menuOpen = false"
                        class="w-full text-left px-6 py-3.5 text-sm text-gray-800 hover:bg-red-200 hover:text-red-800 flex items-center transition-all duration-200">
                        <i class="fas fa-sign-out-alt mr-4 text-red-600" style="margin-left: 20px;"></i> Kết thúc
                    </a>
                </div>
            </div>
        </div>

        <!-- Nội dung chat hoặc form -->
        @if($showForm)
        <span>Thông tin cơ bản</span>
        <div class="form-scroll-container">
            <div class="mb-2">
                <input type="text" wire:model="name" x-model="name" placeholder="Nhập tên của bạn *" class="input-field" required>
                @error('name') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <input type="email" wire:model="email" x-model="email" placeholder="Nhập email của bạn *" class="input-field" required>
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <input type="tel" wire:model="phone" x-model="phone" placeholder="Nhập số điện thoại của bạn *" class="input-field" required>
                @error('phone') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <select wire:model="supportOption" class="input-field">
                    <option value="">-- Chọn 1 dịch vụ hỗ trợ --</option>
                    <option value="tu_van_mua_sach">Tư vấn mua sách</option>
                    <option value="ho_tro_don_hang">Hỗ trợ đơn hàng</option>
                    <option value="doi_tra">Đổi trả</option>
                </select>
            </div>
            <button wire:click="startChat" class="submit-button">BẮT ĐẦU TRÒ CHUYỆN</button>
        </div>
        @else
        <div class="chat-wrapper">
            <div id="chatMessages" class="chat-body space-y-2 overflow-y-auto custom-scrollbar">
                @foreach($messages as $message)
                @if($message['user'])
                <div class="flex justify-end" style="margin-right: 8px;">
                    <div class="user-message">
                        {{ $message['user'] }}
                        <div class="text-xs text-gray-500 mt-1">{{ $message['timestamp'] }}</div>
                    </div>
                </div>
                @endif
                <div class="flex justify-start">
                    <div class="flex flex-col items-start space-y-1">
                        <div class="p-2 rounded-lg text-sm max-w-xs bot-message-box">
                            <strong style="font-size: 15px;">Panda:</strong> {!! $message['bot'] !!}
                            <div class="text-xs text-gray-500 mt-1">{{ $message['timestamp'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div x-show="$wire.loading" class="flex justify-start text-sm text-gray-500 italic px-2" x-transition.opacity>
                    <span>Panda đang gõ</span>
                    <span class="typing-dots">...</span>
                </div>
            </div>
            <br>
            <div class="input-container">
                <input type="text" wire:model="userMessage" wire:keydown.enter="sendMessage" placeholder="Nhập điều bạn cần..." />
                <i wire:click="sendMessage" class="fas fa-paper-plane"></i>
            </div>
            <div x-show="showSuggestions" class="suggestion-tags mt-2 flex flex-wrap gap-2">
                @foreach($suggestions as $tag)
                <span class="tag" wire:click="setSuggestion('{{ $tag }}')">{{ $tag }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Menu chỉnh sửa thông tin -->
    <div x-show="isEditing" x-cloak class="edit-menu fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
        <div class="w-80 bg-white border rounded-lg shadow-lg p-4 relative">
            <button x-on:click="isEditing = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-lg"></i>
            </button>
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Chỉnh sửa thông tin</h4>
            <div class="mb-2">
                <input type="text" wire:model="name" placeholder="Nhập tên của bạn *" class="input-field w-full" required>
                @error('name') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <input type="email" wire:model="email" placeholder="Nhập email của bạn *" class="input-field w-full" required>
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <input type="tel" wire:model="phone" placeholder="Nhập số điện thoại của bạn *" class="input-field w-full" required>
                @error('phone') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <select wire:model="supportOption" class="input-field w-full">
                    <option value="">-- Chọn 1 dịch vụ hỗ trợ --</option>
                    <option value="tu_van_mua_sach">Tư vấn mua sách</option>
                    <option value="ho_tro_don_hang">Hỗ trợ đơn hàng</option>
                    <option value="doi_tra">Đổi trả</option>
                </select>
            </div>
            <button wire:click="updateInfo" class="submit-button w-full">Cập nhật</button>
            <button x-on:click="isEditing = false" class="submit-button w-full mt-2">Đóng</button>
            @if (session('message'))
            <div class="alert alert-success mt-2">{{ session('message') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    <!-- Script -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('focusInput', () => {
                const input = document.querySelector('.input-container input');
                if (input) {
                    input.focus();
                }
            });
            Livewire.on('scroll-to-bottom', () => {
                const chatMessages = document.getElementById('chatMessages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            });
            Livewire.on('update-form-state', () => {
                console.log('update-form-state received');
                document.querySelectorAll('[x-show]').forEach(el => {
                    el.__x.$data.__updateElements();
                });
            });
        });
    </script>

    <style>
        .chat-wrapper {
            display: flex;
            flex-direction: column;
            height: 400px;
            border-radius: 8px;
            overflow: hidden;
        }
        .chat-body {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background-color: #f9f9f9;
        }
        .input-container {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-top: 1px solid #e5e7eb;
            background-color: #fff;
        }
        .input-container input {
            width: 100%;
            padding: 8px 36px 8px 12px;
            border: 1px solid #ccc;
            border-radius: 9999px;
            font-size: 14px;
            outline: none;
            height: 40px;
            box-sizing: border-box;
        }
        .input-container i {
            position: absolute;
            right: 24px;
            font-size: 16px;
            color: #3b82f6;
            cursor: pointer;
        }
        .suggestion-tags {
            padding: 0 10px;
        }
        .tag {
            display: inline-block;
            background-color: #e5e7eb;
            color: #374151;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .tag:hover {
            background-color: #d1d5db;
        }
        .typing-dots::after {
            content: '';
            animation: dots 1.5s infinite;
        }
        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
        }
        .user-message {
            background-color: #C12530;
            color: white;
            padding: 8px 12px;
            border-radius: 12px;
            max-width: 70%;
        }
        .bot-message-box {
            background-color: #e5e7eb;
            color: #374151;
            padding: 8px 12px;
            border-radius: 12px;
        }
        .bot-message-box a {
            color: #3b82f6;
            text-decoration: underline;
            cursor: pointer;
        }
        .alert {
            padding: 8px;
            margin-top: 8px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .submit-button {
            background-color: #C12530;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
            width: 100%;
            text-align: center;
        }
        .submit-button:hover {
            background-color: #a11f28;
        }
        .hamburger-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 50%;
            transition: background-color 0.2s;
        }
        .hamburger-button:hover {
            background-color: #f1f1f1;
        }
        .edit-menu {
            transition: opacity 0.2s ease-in-out;
        }
        .input-field {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .error-text {
            color: #721c24;
            font-size: 12px;
        }
    </style>
</div>