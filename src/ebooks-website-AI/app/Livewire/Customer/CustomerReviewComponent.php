<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\OrderItem;
use App\Models\Review;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerReviewComponent extends Component
{
    use WithFileUploads;

    public $order_item_id;
    public $rating;
    public $comment;
    public $new_image;
    public $new_images = [];
    public $review;
    public $orderItem;

    public function mount($order_item_id)
    {
        // Fetch the OrderItem
        $this->orderItem = OrderItem::find($order_item_id);

        // Check if OrderItem exists and belongs to the user
        if (!$this->orderItem || $this->orderItem->order->user_id !== Auth::id()) {
            abort(404, 'Không tìm thấy sản phẩm hoặc bạn không có quyền truy cập.');
        }

        // Check if a review already exists for this OrderItem by the user
        $this->review = Review::where('order_item_id', $order_item_id)
            ->where('user_id', Auth::id())
            ->first();

        // If a review exists, pre-fill the form fields
        if ($this->review) {
            $this->rating = $this->review->rating;
            $this->comment = $this->review->comment;
        }

        $this->order_item_id = $order_item_id;
    }

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'new_image' => 'nullable|image|max:1024', // Validate single image
        ]);
    }

    public function updatedNewImage()
    {
        // Validate the new image
        $this->validate([
            'new_image' => 'nullable|image|max:1024',
        ]);

        // Add the new image to the images array if less than 5 images
        if ($this->new_image && count($this->new_images) < 5) {
            $this->new_images[] = $this->new_image;
        } elseif (count($this->new_images) >= 5) {
            $this->addError('new_image', 'Bạn chỉ có thể tải lên tối đa 5 ảnh.');
        }

        // Reset the new_image field to allow selecting another image
        $this->new_image = null;
    }

    public function addReview()
    {
        // Validate all fields before submission
        $this->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'new_image' => 'nullable|image|max:1024',
        ]);

        // Check if a review already exists; if not, create a new one
        if ($this->review) {
            $review = $this->review;
        } else {
            $review = new Review();
            $review->user_id = Auth::id();
            $review->order_item_id = $this->order_item_id;
            $review->status = 'pending';
        }

        $review->rating = $this->rating;
        $review->comment = $this->comment;

        // Handle image uploads if any new images are provided
        if (!empty($this->new_images)) {
            $path = public_path('admin/review');
            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }

            $imagesname = $review->images ? explode(',', $review->images) : [];
            foreach ($this->new_images as $key => $image) {
                $imgName = Carbon::now()->timestamp . $key . '.' . $image->getClientOriginalExtension();
                $manager = new ImageManager(new Driver());
                $img = $manager->read($image->getRealPath());
                $img->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save($path . '/' . $imgName);
                $imagesname[] = $imgName;
            }

            $review->images = implode(',', $imagesname);
        }

        $review->save();

        // Update OrderItem to mark it as reviewed (if necessary)
        // $this->orderItem->status = 'reviewed'; // Assuming 'status' field exists and 'reviewed' is a valid status
        $this->orderItem->save();

        $this->resetForm();
        flash()->success('Cảm ơn bạn đã đánh giá sản phẩm!');
        return redirect()->route('customer.orders');
    }

    public function removeImage($index)
    {
        if (isset($this->new_images[$index])) {
            unset($this->new_images[$index]);
            $this->new_images = array_values($this->new_images);
        }
    }

    public function resetForm()
    {
        $this->rating = null;
        $this->comment = '';
        $this->new_image = null;
        $this->new_images = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.customer.customer-review-component', [
            'orderItem' => $this->orderItem,
        ]);
    }
}