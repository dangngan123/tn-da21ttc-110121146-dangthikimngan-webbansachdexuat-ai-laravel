<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Coupon;
use App\Models\CouponProduct;
use App\Models\Order;
use App\Models\SaleTimer;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ManageCouponsComponent extends Component
{
    use WithPagination;

    public $pagesize = 6;
    protected $paginationTheme = 'bootstrap';
    public $coupon_code;
    public $coupon_type;
    public $coupon_value;
    public $cart_value;
    public $start_date;
    public $end_date;
    public $max_uses;
    public $is_active = true;
    public $description;
    public $category_ids = [];
    public $product_ids = [];
    public $user_id = ''; // Thay đổi từ null sang chuỗi rỗng để phù hợp với input
    public $delete_id;
    public $editForm = false;
    public $titleForm = "Thêm mã giảm giá";
    public $sid;
    public $search = '';
    public $filter_type = '';
    public $selectedCoupon;

    // Flash Sale properties
    public $editingId;
    public $sale_timer;
    public $status;
    public $highlightedId;
    public $selectedSale;

    public function changepageSize($size)
    {
        $this->pagesize = $size;
        $this->resetPage();
    }

    private function isCouponUsed($couponId)
    {
        return Order::where('coupon_id', $couponId)->exists();
    }

    protected $listeners = [
        'deleteConfirmed' => 'delete',
        'refreshComponent' => '$refresh',
    ];

    public function deleteConfirmation($id)
    {
        $this->delete_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        try {
            $coupon = Coupon::find($this->delete_id);
            if ($coupon) {
                if ($this->isCouponUsed($coupon->id)) {
                    flash()->addError('Không thể xóa mã giảm giá này vì đã được sử dụng trong đơn hàng.');
                    return;
                }
                $coupon->delete();
                flash()->addSuccess('Mã giảm giá đã được xóa thành công.');
            } else {
                flash()->addError('Mã giảm giá không tồn tại.');
            }
        } catch (\Exception $e) {
            flash()->addError('Có lỗi xảy ra khi xóa mã giảm giá: ' . $e->getMessage());
        }
    }

    public function showCouponModal()
    {
        $this->dispatch('coupon-modal');
    }

    public function showCouponDetail($id)
    {
        $this->selectedCoupon = Coupon::with(['couponProducts.category', 'couponProducts.product'])->find($id);
        if ($this->selectedCoupon) {
            $user_id = Auth::id() ?? 1;
            $this->selectedCoupon->user_usage_count = Order::where('coupon_id', $id)
                ->where('user_id', $user_id)
                ->count();
            $this->selectedCoupon->categories = $this->selectedCoupon->couponProducts
                ->whereNotNull('category_id')
                ->pluck('category.name')
                ->implode(', ') ?: 'N/A';
            $this->selectedCoupon->products = $this->selectedCoupon->couponProducts
                ->whereNotNull('product_id')
                ->pluck('product.name')
                ->implode(', ') ?: 'N/A';
            $this->dispatch('show-coupon-detail-modal');
        } else {
            flash()->addError('Mã giảm giá không tồn tại.');
        }
    }

    public function updated($field)
    {
        $this->validateOnly($field, $this->validationRules());
    }

    private function validationRules()
    {
        $rules = [
            'coupon_code' => ['required', 'max:50'],
            'coupon_type' => ['required', 'in:fixed,percent'],
            'coupon_value' => ['required', 'numeric', 'min:0'],
            'cart_value' => ['required', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['exists:products,id'],
            'user_id' => ['nullable', 'exists:users,id'], // Giữ nguyên rule để kiểm tra ID hợp lệ
        ];

        if (!$this->editForm) {
            $rules['coupon_code'][] = 'unique:coupons,coupon_code';
        } else {
            $rules['coupon_code'][] = 'unique:coupons,coupon_code,' . $this->sid;
        }

        return $rules;
    }

    public function addCoupon()
    {
        $this->validate($this->validationRules());

        $coupon = new Coupon();
        $this->fillCouponData($coupon);
        $coupon->save();

        $this->saveCouponProducts($coupon->id);

        $this->dispatch('close-coupon-modal');
        sleep(0.1);
        $this->resetForm();
        $this->dispatch('refreshComponent');
        flash()->addSuccess('Mã giảm giá đã được thêm thành công.');
    }

    public function showEditCoupon($id)
    {
        if ($this->isCouponUsed($id)) {
            flash()->addError('Không thể chỉnh sửa mã giảm giá này vì đã được sử dụng trong đơn hàng.');
            return;
        }

        $coupon = Coupon::with(['couponProducts.category', 'couponProducts.product'])->find($id);
        if ($coupon) {
            $this->sid = $coupon->id;
            $this->coupon_code = $coupon->coupon_code;
            $this->coupon_type = $coupon->coupon_type;
            $this->coupon_value = $coupon->coupon_value;
            $this->cart_value = $coupon->cart_value;
            $this->start_date = $coupon->start_date ? Carbon::parse($coupon->start_date)->format('Y-m-d') : null;
            $this->end_date = $coupon->end_date ? Carbon::parse($coupon->end_date)->format('Y-m-d') : null;
            $this->max_uses = $coupon->max_uses;
            $this->is_active = $coupon->is_active;
            $this->description = $coupon->description;
            $this->category_ids = $coupon->couponProducts->whereNotNull('category_id')->pluck('category_id')->toArray();
            $this->product_ids = $coupon->couponProducts->whereNotNull('product_id')->pluck('product_id')->toArray();
            $this->user_id = $coupon->user_id ?? ''; // Chuyển null thành chuỗi rỗng cho input

            $this->editForm = true;
            $this->titleForm = "Chỉnh sửa mã giảm giá";
            $this->dispatch('coupon-modal');
        } else {
            flash()->addError('Mã giảm giá không tồn tại.');
        }
    }

    public function updateCoupon()
    {
        $this->validate($this->validationRules());

        $coupon = Coupon::find($this->sid);
        if ($coupon) {
            $this->fillCouponData($coupon);
            $coupon->save();

            CouponProduct::where('coupon_id', $coupon->id)->delete();
            $this->saveCouponProducts($coupon->id);

            $this->dispatch('close-coupon-modal');
            $this->resetForm();
            flash()->addSuccess('Mã giảm giá đã được cập nhật thành công.');
        } else {
            flash()->addError('Mã giảm giá không tồn tại.');
        }
    }

    private function fillCouponData($coupon)
    {
        $coupon->coupon_code = $this->coupon_code;
        $coupon->coupon_type = $this->coupon_type;
        $coupon->coupon_value = $this->coupon_value;
        $coupon->cart_value = $this->cart_value;
        $coupon->start_date = $this->start_date;
        $coupon->end_date = $this->end_date;
        $coupon->max_uses = $this->max_uses;
        $coupon->is_active = $this->is_active;
        $coupon->description = $this->description;
        $coupon->user_id = $this->user_id === '' ? null : $this->user_id; // Chuyển chuỗi rỗng thành null
    }

    private function saveCouponProducts($couponId)
    {
        foreach ($this->category_ids ?? [] as $categoryId) {
            CouponProduct::create([
                'coupon_id' => $couponId,
                'category_id' => $categoryId,
            ]);
        }
        foreach ($this->product_ids ?? [] as $productId) {
            CouponProduct::create([
                'coupon_id' => $couponId,
                'product_id' => $productId,
            ]);
        }
    }

    public function resetForm()
    {
        $this->coupon_code = '';
        $this->coupon_type = '';
        $this->coupon_value = '';
        $this->cart_value = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->max_uses = '';
        $this->is_active = true;
        $this->description = '';
        $this->category_ids = [];
        $this->product_ids = [];
        $this->user_id = ''; // Đặt lại thành chuỗi rỗng
        $this->editForm = false;
        $this->titleForm = "Thêm mã giảm giá";
        $this->sid = null;
        $this->selectedCoupon = null;
        $this->resetValidation();
        $this->dispatch('close-coupon-modal');
    }

    public function startEditing($id)
    {
        $sale = SaleTimer::find($id);
        if ($sale) {
            $this->editingId = $id;
            $this->start_date = $sale->start_date ? $sale->start_date->format('Y-m-d\TH:i') : null;
            $this->sale_timer = $sale->sale_timer ? $sale->sale_timer->format('Y-m-d\TH:i') : null;
            $this->status = $sale->status;
            $this->dispatch('openEditModal');
        } else {
            flash()->addError('Flash Sale không tồn tại.');
        }
    }

    public function showSaleDetail($id)
    {
        $this->selectedSale = SaleTimer::with('saleProducts.product', 'saleProducts.category')->find($id);
        if ($this->selectedSale) {
            $products = $this->selectedSale->saleProducts->map(function ($saleProduct) {
                return $saleProduct->product ? $saleProduct->product->name : ($saleProduct->category ? $saleProduct->category->name : null);
            })->filter()->implode(', ') ?: 'Chưa có';
            $this->selectedSale->products = $products;
            $this->dispatch('show-sale-detail-modal');
        } else {
            flash()->addError('Flash Sale không tồn tại.');
        }
    }

    public function updateSale()
    {
        $this->validate([
            'start_date' => ['required', 'date'],
            'sale_timer' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'boolean'],
        ]);

        $sale = SaleTimer::find($this->editingId);
        if ($sale) {
            $sale->start_date = Carbon::parse($this->start_date);
            $sale->sale_timer = Carbon::parse($this->sale_timer);
            $sale->status = $this->status;
            $sale->save();

            $this->highlightedId = $sale->id;
            $this->cancelEditing();
            flash()->addSuccess('Flash Sale đã được cập nhật thành công.');
        } else {
            flash()->addError('Flash Sale không tồn tại.');
        }
    }

    public function updateSaleStatus($id, $status)
    {
        $sale = SaleTimer::find($id);
        if ($sale) {
            $sale->status = $status;
            $sale->save();
            flash()->addSuccess('Trạng thái Flash Sale đã được cập nhật thành công.');
        } else {
            flash()->addError('Flash Sale không tồn tại.');
        }
    }

    public function cancelEditing()
    {
        $this->editingId = null;
        $this->start_date = null;
        $this->sale_timer = null;
        $this->status = null;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Coupon::with(['couponProducts.category', 'couponProducts.product'])
            ->where('coupon_code', 'like', '%' . $this->search . '%');

        if ($this->filter_type) {
            $query->where('coupon_type', $this->filter_type);
        }

        $coupons = $query->paginate($this->pagesize);

        $coupons->getCollection()->transform(function ($coupon) {
            $coupon->is_used = $this->isCouponUsed($coupon->id);
            $coupon->categories = $coupon->couponProducts
                ->whereNotNull('category_id')
                ->pluck('category.name')
                ->implode(', ') ?: 'N/A';
            $coupon->products = $coupon->couponProducts
                ->whereNotNull('product_id')
                ->pluck('product.name')
                ->implode(', ') ?: 'N/A';
            return $coupon;
        });

        $saletimers = SaleTimer::paginate($this->pagesize);
        $users = \App\Models\User::pluck('name', 'id')->toArray();
        $products = Product::pluck('name', 'id')->toArray();
        $categories = Category::pluck('name', 'id')->toArray();

        return view('livewire.admin.manage-coupons-component', [
            'coupons' => $coupons,
            'saletimers' => $saletimers,
            'users' => $users,
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}