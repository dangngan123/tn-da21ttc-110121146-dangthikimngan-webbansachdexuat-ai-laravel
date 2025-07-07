<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;

class VoucherIconComponent extends Component
{
    public $coupons;

    public function mount()
    {
        $this->coupons = Coupon::where('is_active', true)
            ->where('end_date', '>=', now())
            ->where(function ($query) {
                $user = Auth::user();
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user ? $user->id : null);
            })
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereColumn('used', '<', 'max_uses');
            })
            ->whereNotNull('coupon_code')
            ->where('coupon_code', '!=', '')
            ->get();
    }

    public function copyCode($code)
    {
        $code = (string) $code;

        // Gửi sự kiện sang client để xử lý hiển thị thông báo
        $this->dispatch('codeCopied', $code);
    }

    public function render()
    {
        return view('livewire.voucher-icon-component');
    }
}
