<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CustomerChangePasswordComponent extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function updated($fields)
    {
        // Động quy tắc xác thực
        $rules = [
            'password' => 'required|min:8|confirmed|different:current_password',
        ];

        // Yêu cầu current_password nếu người dùng không đăng ký bằng Google HOẶC đã có mật khẩu cục bộ
        if (!$this->user->google_id || $this->user->password) {
            $rules['current_password'] = 'required';
        }

        $this->validateOnly($fields, $rules);
    }

    public function changePassword()
    {
        // Động quy tắc xác thực
        $rules = [
            'password' => 'required|min:8|confirmed|different:current_password',
        ];

        // Yêu cầu current_password nếu người dùng không đăng ký bằng Google HOẶC đã có mật khẩu cục bộ
        if (!$this->user->google_id || $this->user->password) {
            $rules['current_password'] = 'required';
        }

        $this->validate($rules);

        // Nếu người dùng không đăng ký bằng Google HOẶC đã có mật khẩu cục bộ, kiểm tra mật khẩu hiện tại
        if (!$this->user->google_id || $this->user->password) {
            if (!Hash::check($this->current_password, $this->user->password)) {
                session()->flash('password_error', 'Mật khẩu hiện tại không đúng!');
                return;
            }
        }

        try {
            $user = User::findOrFail($this->user->id);
            $user->password = Hash::make($this->password);
            $user->save();
            session()->flash('password_success', 'Mật khẩu đã được đổi thành công!');
        } catch (\Exception $e) {
            session()->flash('password_error', 'Có lỗi xảy ra khi đổi mật khẩu. Vui lòng thử lại sau!');
            Log::error('Lỗi khi đổi mật khẩu: ' . $e->getMessage());
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function render()
    {
        return view('livewire.customer.customer-change-password-component');
    }
}
