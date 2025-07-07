<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ManageCustomersComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pagesize = 5;
    public $search = ''; // Gộp thành 1 khung tìm kiếm
    public $searchStatus = '';
    public $searchGender = '';

    // Thêm các biến thống kê
    public $totalUsers = 0;      // Tổng người dùng
    public $activeUsers = 0;     // Hoạt động
    public $blockedUsers = 0;    // Bị khóa
    public $newUsersToday = 0;   // Mới hôm nay

    public function mount()
    {
        $this->updateUserStats();
    }

    public function updateUserStats()
    {
        $today = now()->toDateString();
        $this->totalUsers = User::where('utype', '!=', 'admin')->count();
        $this->activeUsers = User::where('utype', '!=', 'admin')->where('status', 1)->count();
        $this->blockedUsers = User::where('utype', '!=', 'admin')->where('status', 0)->count();
        $this->newUsersToday = User::where('utype', '!=', 'admin')
            ->whereDate('created_at', $today)
            ->count();
    }

    public function changepageSize($size)
    {
        $this->pagesize = $size;
        $this->resetPage();
    }

    public function blockUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->status = 0; // Chặn người dùng
            $user->save();

            // Xóa session của người dùng để đăng xuất ngay lập tức
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();

            // Cập nhật thống kê
            $this->updateUserStats();

            // Thông báo cho admin
            flash()->success('Đã chặn tài khoản ' . $user->name);
        }
    }

    public function unblockUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->status = 1; // Mở khóa người dùng
            $user->save();

            // Cập nhật thống kê
            $this->updateUserStats();

            flash()->success('Đã mở khóa tài khoản ' . $user->name);
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->searchStatus = '';
        $this->searchGender = '';
        $this->pagesize = 5;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->updateUserStats();
        $query = User::where('utype', '!=', 'admin');

        // Áp dụng bộ lọc
        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('email', 'like', '%' . $this->search . '%')
                         ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->searchStatus !== '') {
            $query->where('status', $this->searchStatus);
        }
        if ($this->searchGender) {
            $query->where('gender', $this->searchGender);
        }

        $users = $query->paginate($this->pagesize);

        return view('livewire.admin.manage-customers-component', [
            'users' => $users,
            'totalUsers' => $this->totalUsers,
            'activeUsers' => $this->activeUsers,
            'blockedUsers' => $this->blockedUsers,
            'newUsersToday' => $this->newUsersToday,
        ]);
    }
}