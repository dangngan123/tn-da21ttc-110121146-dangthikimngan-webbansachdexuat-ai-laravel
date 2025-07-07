<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Review;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class ManageReviewComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pagesize = 5;
    public $search = '';
    public $selectAll;
    public $selectedItems = [];
    public $replyText = [];
    public $currentReviewId;
    public $currentReplyText = '';
    public $statusFilter = '';

    public function changepageSize($size)
    {
        $this->pagesize = $size;
        $this->resetPage();
    }

    public function updateOrderStatus($id, $status)
    {
        $review = Review::find($id);
        if ($review) {
            $review->status = $status;
            $review->save();
            flash('Trạng thái đánh giá của khách hàng đã được cập nhật thành công');
        } else {
            flash('error', 'Review not found.');
        }
    }

    public function openReplyModal($reviewId)
    {
        $this->currentReviewId = $reviewId;
        $review = Review::find($reviewId);
        $this->currentReplyText = $review ? $review->admin_reply : '';
        $this->dispatch('openReplyModal', reviewId: $reviewId, replyText: $this->currentReplyText);
    }

    public function replyReview()
    {
        Log::info('replyReview called', [
            'currentReviewId' => $this->currentReviewId,
            'currentReplyText' => $this->currentReplyText,
        ]);

        if (!$this->currentReviewId) {
            flash('Không tìm thấy ID đánh giá.');
            Log::error('currentReviewId is null in replyReview');
            return;
        }

        $review = Review::find($this->currentReviewId);
        if (!$review) {
            flash('Đánh giá không tồn tại.');
            Log::error('Review not found', ['currentReviewId' => $this->currentReviewId]);
            return;
        }

        $reply = trim($this->currentReplyText);
        Log::info('Reply value', ['reply' => $reply]);
        if (empty($reply)) {
            flash('Vui lòng nhập nội dung phản hồi.');
            Log::error('Reply is empty', ['currentReplyText' => $this->currentReplyText]);
            return;
        }

        $review->update([
            'admin_reply' => $reply,
            'admin_reply_at' => now(),
        ]);

        flash('Phản hồi đã được gửi thành công');
        Log::info('Admin replied to review', [
            'review_id' => $this->currentReviewId,
            'reply' => $reply,
        ]);

        $this->currentReplyText = '';
        $this->currentReviewId = null;
    }

    public function deleteReply()
    {
        $review = Review::find($this->currentReviewId);
        if ($review) {
            $review->update([
                'admin_reply' => null,
                'admin_reply_at' => null,
            ]);

            flash('Phản hồi đã được xóa thành công');
            Log::info('Admin deleted reply', [
                'review_id' => $this->currentReviewId,
            ]);

            $this->currentReplyText = '';
            $this->currentReviewId = null;
        } else {
            flash('Đánh giá không tồn tại.');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = Review::pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        if (count($this->selectedItems) === count(Review::pluck('id'))) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false;
        }
    }

    public function selecteDelete()
    {
        foreach ($this->selectedItems as $item) {
            $review = Review::find($item);
            if ($review) {
                $image_path = public_path('admin/review/' . $review->image);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                $review->delete();
            }
        }
        $this->selectAll = false;
        $this->selectedItems = [];
        flash('Đánh giá đã được xóa.');
    }

    public function selecteActive()
    {
        foreach ($this->selectedItems as $item) {
            $review = Review::find($item);
            if ($review) {
                $review->status = 'approved';
                $review->save();
            }
        }
        $this->selectedItems = [];
        $this->selectAll = false;
        flash('Đánh giá đã được xác nhận.');
    }

    public function selecteInactive()
    {
        foreach ($this->selectedItems as $item) {
            $review = Review::find($item);
            if ($review) {
                $review->status = 'rejected';
                $review->save();
            }
        }
        $this->selectedItems = [];
        $this->selectAll = false;
        flash('Đánh giá đã bị hủy.');
    }

    public function isColor($reviewId)
    {
        if ($this->selectAll == false) {
            if (in_array($reviewId, $this->selectedItems)) {
                return 'bg-1';
            } else {
                return '';
            }
        } else {
            return 'bg-1';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Review::with(['product', 'user']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        } else {
            $query->where('status', '!=', 'rejected');
        }

        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery->where('rating', 'like', '%' . $this->search . '%')
                    ->orWhere('comment', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('id', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('product', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate($this->pagesize);

        return view('livewire.admin.manage-review-component', ['reviews' => $reviews]);
    }
}