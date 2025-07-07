<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ContactComponent extends Component
{
    public $name;
    public $email;
    public $telephone;
    public $subject;
    public $message;
    public $isAuthenticated = false;

    public function mount()
    {
        // Kiểm tra trạng thái đăng nhập
        $this->isAuthenticated = Auth::check();

        // Nếu đã đăng nhập, tự động điền thông tin người dùng
        if ($this->isAuthenticated) {
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->telephone = $user->phone ?? ''; // Giả sử bạn có trường telephone trong bảng users
        }
    }

    public function submit()
    {
        try {
            Log::info('Form Data:', [
                'name' => $this->name,
                'email' => $this->email,
                'telephone' => $this->telephone,
                'subject' => $this->subject,
                'message' => $this->message,
                'user_id' => $this->isAuthenticated ? Auth::id() : null,
            ]);

            $this->validate([
                'name' => 'required|min:3',
                'email' => 'required|email',
                'telephone' => 'required|regex:/^[0-9]{10,11}$/',
                'subject' => 'required|min:5',
                'message' => 'required|min:10|max:255',
            ], [
                'message.max' => 'Nội dung tin nhắn không được vượt quá 255 ký tự.',
            ]);

            Log::info('Validation passed');

            $contact = Contact::create([
                'name' => $this->name,
                'email' => $this->email,
                'telephone' => $this->telephone,
                'subject' => $this->subject,
                'message' => $this->message,
                'user_id' => $this->isAuthenticated ? Auth::id() : null, // Lưu user_id nếu đã đăng nhập
            ]);

            Log::info('Contact created:', ['contact_id' => $contact->id]);

            session()->flash('success', 'Gửi tin nhắn thành công!');
            $this->reset();
            $this->dispatch('contactSubmitted');
        } catch (\Exception $e) {
            Log::error('Contact Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            session()->flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.contact-component', [
            'isAuthenticated' => $this->isAuthenticated,
        ]);
    }
}