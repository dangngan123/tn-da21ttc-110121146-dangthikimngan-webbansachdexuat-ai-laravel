<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class CustomerDashboardComponent extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $current_password;
    public $password;
    public $password_confirmation;
    public $avatar;
    public $new_avatar;
    public $user;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'current_password' => 'required_with:password',
        'password' => 'nullable|min:8|confirmed',
        'new_avatar' => 'nullable|image|max:2048',
    ];

    protected $messages = [
        'name.required' => 'Vui lòng nhập họ tên',
        'name.min' => 'Họ tên phải có ít nhất 3 ký tự',
        'email.required' => 'Vui lòng nhập email',
        'email.email' => 'Email không hợp lệ',
        'new_avatar.image' => 'File phải là hình ảnh (jpg, png, gif, bmp)',
        'new_avatar.max' => 'Hình ảnh không được lớn hơn 2MB',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->avatar = $this->user->avatar;
    }

    public function updateAvatar()
    {
        $this->validate([
            'new_avatar' => 'required|image|max:2048',
        ]);

        Log::info('Updating avatar for user ' . $this->user->id);

        if ($this->new_avatar) {
            // Xóa avatar cũ nếu có
            if ($this->user->avatar && file_exists(public_path($this->user->avatar))) {
                Log::info('Deleting old avatar: ' . $this->user->avatar);
                unlink(public_path($this->user->avatar));
            }

            // Tạo tên file duy nhất
            $imageName = Carbon::now()->timestamp . '.' . $this->new_avatar->getClientOriginalExtension();

            // Đảm bảo thư mục tồn tại
            $directory = public_path('customer/avatars');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                Log::info('Created directory: ' . $directory);
            }

            // Resize và lưu ảnh
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->new_avatar->getRealPath());
            $image->resize(150, 150); // Resize ảnh thành 150x150px
            $image->toPng()->save($directory . '/' . $imageName);

            Log::info('New avatar saved: ' . 'customer/avatars/' . $imageName);

            // Cập nhật đường dẫn avatar (loại bỏ "public" trong đường dẫn lưu trữ)
            $this->user->avatar = 'customer/avatars/' . $imageName;
            $this->user->save();

            Log::info('User avatar updated in database: ' . $this->user->avatar);

            // Cập nhật avatar trong giao diện
            $this->avatar = $this->user->avatar;
            $this->new_avatar = null; // Reset input file

            flash()->success('Cập nhật ảnh đại diện thành công!');
        } else {
            Log::error('Không có tệp hình đại diện mới nào được cung cấp');
        }
    }

    public function updateProfile()
    {
        $this->validate();

        // Kiểm tra email đã tồn tại
        if ($this->email !== $this->user->email) {
            $this->validate([
                'email' => 'unique:users,email,' . $this->user->id,
            ]);
        }

        // Xử lý upload avatar nếu có (từ form)
        if ($this->new_avatar) {
            // Xóa avatar cũ nếu có
            if ($this->user->avatar && file_exists(public_path($this->user->avatar))) {
                unlink(public_path($this->user->avatar));
            }

            // Tạo tên file duy nhất
            $imageName = Carbon::now()->timestamp . '.' . $this->new_avatar->getClientOriginalExtension();

            // Đảm bảo thư mục tồn tại
            $directory = public_path('customer/avatars');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                Log::info('Created directory: ' . $directory);
            }

            // Resize và lưu ảnh
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->new_avatar->getRealPath());
            $image->resize(150, 150); // Resize ảnh thành 150x150px
            $image->toPng()->save($directory . '/' . $imageName);

            // Cập nhật đường dẫn avatar (loại bỏ "public" trong đường dẫn lưu trữ)
            $this->user->avatar = 'customer/avatars/' . $imageName;
        }

        // Cập nhật thông tin cơ bản
        $this->user->name = $this->name;
        $this->user->email = $this->email; // Sửa lỗi từ $this->user->email thành $this->email
        $this->user->save();

        flash()->success('Cập nhật thông tin thành công!');

        // Cập nhật avatar trong giao diện
        $this->avatar = $this->user->avatar;
        $this->new_avatar = null; // Reset input file
    }

    public function render()
    {
        return view('livewire.customer.customer-dashboard-component');
    }
}
