<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, bạn cần xác thực email của mình. Chúng tôi đã gửi một liên kết xác thực đến địa chỉ email bạn đã cung cấp. Vui lòng kiểm tra hộp thư đến (và thư rác nếu cần).') }}
    </div>

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Gửi lại email xác thực') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                {{ __('Đăng xuất') }}
            </button>
        </form>
    </div>
</x-guest-layout>