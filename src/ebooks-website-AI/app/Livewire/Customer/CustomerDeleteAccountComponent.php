<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerDeleteAccountComponent extends Component
{
    public $password;
    public $showConfirmation = false;

    protected $rules = [
        'password' => 'required'
    ];

    protected $messages = [
        'password.required' => 'Vui lòng nhập mật khẩu để xác nhận.',
    ];

    public function showDeleteConfirmation()
    {
        $this->showConfirmation = true;
    }

    public function deleteAccount()
    {
        $this->validate();

        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'Không tìm thấy người dùng.');
            return;
        }

        // Kiểm tra mật khẩu
        if (!Hash::check($this->password, $user->password)) {
            session()->flash('error', 'Mật khẩu không chính xác.');
            return;
        }

        try {
            DB::beginTransaction();

            // Lưu ID người dùng để sử dụng sau này
            $userId = $user->id;

            // Tạo email mới độc nhất
            $newEmail = 'deleted_' . $userId . '_' . time() . '@deleted.com';

            // Cập nhật thông tin người dùng trước
            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'email' => $newEmail,
                    'status' => 0,
                    'name' => 'Deleted User',
                    'avatar' => null,
                    'additional_info' => null
                ]);

            // Xóa các dữ liệu liên quan nếu cần
            // Ví dụ: DB::table('user_profiles')->where('user_id', $userId)->delete();

            // Xóa người dùng
            DB::table('users')->where('id', $userId)->delete();

            DB::commit();

            // Đăng xuất người dùng
            Auth::logout();

           flash()->Success('account_deleted', 'Tài khoản của bạn đã được xóa thành công.');
            return redirect()->route('home');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->Error('Có lỗi xảy ra khi xóa tài khoản: ' . $e->getMessage());
            return;
        }
    }

    public function render()
    {
        return view('livewire.customer.customer-delete-account-component');
    }
}
