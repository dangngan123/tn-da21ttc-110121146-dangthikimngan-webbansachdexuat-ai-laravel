<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CustomerAccountComponent extends Component
{
    use WithFileUploads;
    public $name;
    public $email;
    public $phone;
    public $current_password;
    public $password;
    public $password_confirmation;
    public $avatar;
    public $additional_info;
    public $user;
    public $new_avatar;
    public $gender;
    public $day;
    public $month;
    public $year;
    public $new_email;
    public $otp;
    public $otp_sent = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'nullable|email',
        'phone' => 'nullable|numeric',
        'current_password' => 'required_with:password',
        'password' => 'nullable|min:8|confirmed',
        'gender' => 'required|in:male,female,other',
        'day' => 'required|numeric|between:1,31',
        'month' => 'required|numeric|between:1,12',
        'year' => 'required|numeric|between:1900,2025',
        'new_email' => 'nullable|email|unique:users,email',
        'otp' => 'nullable|numeric|digits:6',
    ];

    protected $messages = [
        'name.required' => 'Vui lòng nhập họ và tên',
        'name.min' => 'Họ và tên phải có ít nhất 3 ký tự',
        'email.email' => 'Email không hợp lệ',
        'phone.numeric' => 'Số điện thoại không hợp lệ',
        'gender.required' => 'Vui lòng chọn giới tính',
        'gender.in' => 'Giới tính không hợp lệ',
        'day.required' => 'Vui lòng nhập ngày sinh',
        'day.between' => 'Ngày phải từ 1 đến 31',
        'month.required' => 'Vui lòng nhập tháng sinh',
        'month.between' => 'Tháng phải từ 1 đến 12',
        'year.required' => 'Vui lòng nhập năm sinh',
        'year.between' => 'Năm phải từ 1900 đến 2025',
        'new_email.email' => 'Email mới không hợp lệ',
        'new_email.unique' => 'Email mới đã được sử dụng',
        'otp.numeric' => 'Mã OTP phải là số',
        'otp.digits' => 'Mã OTP phải có 6 chữ số',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->gender = $this->user->gender;
        if ($this->user->date_of_birth) {
            $date = $this->user->date_of_birth;
            $this->day = $date->format('d');
            $this->month = $date->format('m');
            $this->year = $date->format('Y');
        }
    }

    public function changeEmail()
    {
        $this->resetErrorBag();
        $this->otp_sent = false;

        // Xác thực new_email, nhưng chỉ nếu người dùng nhập giá trị
        if (!$this->new_email) {
            $this->addError('new_email', 'Vui lòng nhập email mới.');
            return;
        }

        $this->validateOnly('new_email');

        $otp = rand(100000, 999999);

        try {
            Session::put('email_change_otp', [
                'otp' => $otp,
                'new_email' => $this->new_email,
                'expires_at' => Carbon::now()->addHours(24),
            ]);

            Mail::to($this->new_email)->send(new EmailVerificationMail($otp, $this->user->name));

            $this->otp_sent = true;
            flash()->success('Mã OTP đã được gửi đến email mới. Vui lòng kiểm tra!');
        } catch (\Exception $e) {
            $this->addError('new_email', 'Không thể gửi mã OTP. Vui lòng kiểm tra lại email hoặc thử lại sau.');
            Log::error('Error sending OTP email: ' . $e->getMessage());
        }
    }

    public function verifyOtpAndUpdateEmail()
    {
        // Xác thực otp, nhưng chỉ nếu người dùng nhập giá trị
        if (!$this->otp) {
            $this->addError('otp', 'Vui lòng nhập mã OTP.');
            return;
        }

        $this->validateOnly('otp');

        $otpData = Session::get('email_change_otp');

        if (!$otpData) {
            $this->addError('otp', 'Mã OTP không tồn tại hoặc đã hết hạn.');
            return;
        }

        if (Carbon::now()->greaterThan($otpData['expires_at'])) {
            $this->addError('otp', 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.');
            Session::forget('email_change_otp');
            return;
        }

        if ($this->otp == $otpData['otp']) {
            $this->user->email = $otpData['new_email'];
            $this->user->save();

            $this->email = $otpData['new_email'];
            $this->otp_sent = false;
            $this->otp = null;
            $this->new_email = null;

            Session::forget('email_change_otp');

            if ($this->user->google_id) {
                flash()->warning('Email của bạn đã được cập nhật. Lưu ý: Nếu email này không khớp với tài khoản Google, bạn có thể gặp vấn đề khi đăng nhập bằng Google. Vui lòng cập nhật email trong tài khoản Google hoặc liên kết lại.');
            } else {
                flash()->success('Email đã được cập nhật thành công!');
            }
        } else {
            $this->addError('otp', 'Mã OTP không đúng.');
        }
    }

    public function cancelOtp()
    {
        $this->otp_sent = false;
        $this->otp = null;
        $this->new_email = null;
        Session::forget('email_change_otp');
        flash()->success('Đã hủy thay đổi email.');
    }

    public function updateProfile()
    {
        $this->resetErrorBag();

        // Debug dữ liệu gửi lên
        Log::info('Dữ liệu gửi lên updateProfile:', $this->all());

        // Đặt lại trạng thái thay đổi email
        $this->otp_sent = false;
        $this->otp = null;
        $this->new_email = null;
        Session::forget('email_change_otp');

        // Xác thực dữ liệu
        $this->validate();

        // Kiểm tra ngày sinh hợp lệ
        try {
            $date_of_birth = Carbon::create($this->year, $this->month, $this->day);
            if (!$date_of_birth || $date_of_birth->isFuture()) {
                $this->addError('year', 'Ngày sinh không hợp lệ hoặc nằm trong tương lai.');
                Log::error('Ngày sinh không hợp lệ hoặc trong tương lai: ' . $this->year . '-' . $this->month . '-' . $this->day);
                return;
            }
        } catch (\Exception $e) {
            $this->addError('day', 'Ngày sinh không hợp lệ (ví dụ: ngày không tồn tại như 31/02).');
            Log::error('Lỗi ngày sinh: ' . $e->getMessage());
            return;
        }

        // So sánh dữ liệu trước khi lưu
        $changes = [];
        if ($this->user->name !== trim($this->name)) {
            $changes['name'] = ['old' => $this->user->name, 'new' => trim($this->name)];
        }
        if ($this->user->phone !== $this->phone) {
            $changes['phone'] = ['old' => $this->user->phone, 'new' => $this->phone];
        }
        if ($this->user->gender !== $this->gender) {
            $changes['gender'] = ['old' => $this->user->gender, 'new' => $this->gender];
        }
        if ($this->user->date_of_birth != $date_of_birth) {
            $changes['date_of_birth'] = ['old' => $this->user->date_of_birth, 'new' => $date_of_birth];
        }

        // Cập nhật thông tin người dùng
        try {
            $this->user->name = trim($this->name);
            $this->user->phone = $this->phone;
            $this->user->gender = $this->gender;
            $this->user->date_of_birth = $date_of_birth;
            $this->user->save();

            if (empty($changes)) {
                flash()->success('Không có thay đổi nào được thực hiện.');
            } else {
                flash()->success('Cập nhật thông tin thành công!');
                Log::info('Cập nhật hồ sơ thành công cho user ID: ' . $this->user->id, ['changes' => $changes]);
            }
        } catch (\Exception $e) {
            $this->addError('general', 'Có lỗi xảy ra khi lưu thông tin. Vui lòng thử lại sau.');
            Log::error('Lỗi khi lưu thông tin người dùng: ' . $e->getMessage());
        }
    }

    public function addPhone()
    {
        $this->phone = '';
    }

    public function render()
    {
        return view('livewire.customer.customer-account-component');
    }
}
