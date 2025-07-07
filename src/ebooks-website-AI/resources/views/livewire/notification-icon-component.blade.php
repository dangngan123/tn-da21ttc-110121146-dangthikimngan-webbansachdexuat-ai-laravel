<div>
    <div class="header-action-icon-2">
        <a class="mini-cart-icon" href="#">
            <img alt="Surfside Media" src="{{ asset('/') }}assets/imgs/theme/icons/bell.svg">
            @if($unreadCount > 0)
            <span class="pro-count blue" style="background-color: #ff4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; position: absolute; top: -5px; right: -5px;">
                {{ $unreadCount }}
            </span>
            @endif
        </a>

        <div class="cart-dropdown-wrap cart-dropdown-hm2" style="width: 300px;">
            <div class="shopping-cart-footer border rounded p-2" style="border-color: #ffc107; background-color: #fffbea;">
                @auth
                @if($notifications->isEmpty())
                <div class="text-muted">
                    👋 Chào {{ Auth::user()->name }}! Không có thông báo mới.
                </div>
                @else
                <ul class="notifications-list" style="list-style: none; padding: 0; max-height: 300px; overflow-y: auto;">
                    @foreach($notifications as $notification)
                    <li class="notification-item" style="padding: 8px 0; border-bottom: 1px solid #eee;" wire:key="notification-{{ $notification->id }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $notification->title }}</strong>
                                <p style="font-size: 14px; margin: 0;">
                                    @if($notification->type === 'order')
                                    <a href="{{ route('customer.orderdetails', ['order_id' => \App\Models\Order::where('user_id', Auth::id())->where('id', (int) filter_var($notification->message, FILTER_SANITIZE_NUMBER_INT))->first()->id ?? 0]) }}" style="color: #007bff; text-decoration: none;">
                                        {{ $notification->message }}
                                    </a>
                                    @else
                                    {{ $notification->message }}
                                    @endif
                                </p>
                                <small class="text-muted" style="font-size: 12px;">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                            @if(!$notification->is_read)
                            <button wire:click="markAsRead({{ $notification->id }})" class="btn btn-sm btn-primary" style="font-size: 10px; padding: 2px 6px;">
                                Đánh dấu đã đọc
                            </button>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
                @else
                <div class="text-danger">
                    🚫 Vui lòng <a href="{{ route('login') }}">đăng nhập</a> hoặc <a href="{{ route('register') }}">đăng ký</a> để xem thông báo.
                </div>
                @endauth
            </div>
        </div>
    </div>
</div>