<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notifications;
use Illuminate\Support\Facades\Auth;


class NotificationIconComponent extends Component
{
    public $unreadCount = 0;
    public $notifications = [];
    public $couponCode;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (Auth::check()) {
            $this->notifications = Notifications::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->take(5) // Limit to 5 recent notifications
                ->get();
            $this->unreadCount = Notifications::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Notifications::where('user_id', Auth::id())->find($notificationId);
        if ($notification) {
            $notification->is_read = true;
            $notification->read_at = now();
            $notification->save();
            $this->loadNotifications(); // Refresh the notifications
        }
    }
    // In CartComponent.php
    public function applyCoupon($code)
    {
        $this->couponCode = $code;
        $this->applyCouponCode();

        // After successful coupon application
        if (session()->has('success_message')) {
            Notifications::create([
                'user_id' => Auth::id(),
                'title' => 'Áp dụng mã giảm giá',
                'message' => 'Bạn đã áp dụng mã giảm giá ' . $code . ' thành công!',
                'type' => 'coupon',
                'is_read' => false,
                'created_at' => now(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.notification-icon-component');
    }
}
