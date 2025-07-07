<?php

namespace App\Livewire;

use App\Models\Shipping;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\UserInteraction;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderConfirmedMail;
use Illuminate\Support\Facades\Mail;
use App\Services\VietnamAddressService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CheckoutComponent extends Component
{
    public $address_type = 'home';
    public $name;
    public $phone;
    public $selectedProvince;
    public $selectedDistrict;
    public $selectedWard;
    public $address;
    public $delete_id;
    public $shipping_id;
    public $couponCode = '';
    public $discount;
    public $subtotal;
    public $subtotalAfterDiscount;
    public $total;
    public $shippingCost;
    public $additional_info;
    public $paymentmode;
    public $thankyou;
    public $status;
    public $editForm = false;
    public $titleForm = "Thêm địa chỉ";
    public $sid;

    public $provinces = [];
    public $districts = [];
    public $wards = [];
    public $selectedShippingId;
    protected $vietnamAddressService;

    public function boot(VietnamAddressService $vietnamAddressService)
    {
        $this->vietnamAddressService = $vietnamAddressService;
    }

    public function mount()
    {
        Log::info('CheckoutComponent::mount called', [
            'user_id' => Auth::id() ?? 'Guest',
        ]);

        $this->provinces = $this->vietnamAddressService->getProvinces();

        if (empty($this->provinces)) {
            Log::warning('No provinces loaded from VietnamAddressService', [
                'user_id' => Auth::id() ?? 'Guest',
            ]);
            session()->flash('error', 'Không thể tải danh sách tỉnh/thành. Vui lòng kiểm tra kết nối mạng hoặc liên hệ hỗ trợ.');
        } else {
            Log::info('Provinces loaded', [
                'count' => count($this->provinces),
                'user_id' => Auth::id() ?? 'Guest',
            ]);
        }

        // Đảm bảo luôn có một địa chỉ mặc định
        $this->ensureDefaultShipping();

        // Tự động chọn địa chỉ mặc định nếu có
        $defaultShipping = Shipping::where('user_id', Auth::id())->where('status', 1)->first();
        if ($defaultShipping) {
            $this->selectShipping($defaultShipping->id);
        }

        // Khởi tạo subtotal và cập nhật phí vận chuyển
        $this->subtotal = floatval(Cart::instance('cart')->subtotal());
        $this->updateShippingCost();
        $this->calculateDiscount();
    }

    public function updatedSelectedProvince($provinceId)
    {
        $this->districts = [];
        $this->wards = [];
        $this->selectedDistrict = null;
        $this->selectedWard = null;
        $this->shippingCost = null;

        if ($provinceId) {
            $this->districts = $this->vietnamAddressService->getDistricts($provinceId);
            Log::info('Districts loaded for province', [
                'province_id' => $provinceId,
                'count' => count($this->districts),
            ]);
            if (empty($this->districts)) {
                session()->flash('error', 'Không thể tải danh sách quận/huyện. Vui lòng thử lại.');
            }
        }
    }

    public function updatedSelectedDistrict($districtId)
    {
        $this->wards = [];
        $this->selectedWard = null;
        $this->shippingCost = null;

        if ($districtId) {
            $this->wards = $this->vietnamAddressService->getWards($districtId);
            Log::info('Wards loaded for district', [
                'district_id' => $districtId,
                'count' => count($this->wards),
            ]);
            if (empty($this->wards)) {
                session()->flash('error', 'Không thể tải danh sách phường/xã. Vui lòng thử lại.');
            }
        }
    }

    public function updatedSelectedWard()
    {
        $this->calculateShippingCost();
        $this->calculateDiscount();
    }

    public function calculateShippingCost()
    {
        if (!$this->selectedDistrict || !$this->selectedWard) {
            session()->flash('error', 'Vui lòng chọn quận/huyện và phường/xã.');
            return;
        }

        $subtotal = floatval(Cart::instance('cart')->subtotal());
        if ($subtotal >= 200) {
            $this->shippingCost = 0;
            Log::info('Free shipping applied', [
                'subtotal' => $subtotal,
                'user_id' => Auth::id(),
            ]);
        } else {
            $items = Cart::instance('cart')->content();
            $this->shippingCost = $this->vietnamAddressService->calculateShippingFee(
                $this->selectedDistrict,
                $this->selectedWard,
                $items
            );

            Log::info('Shipping cost calculated', [
                'district_id' => $this->selectedDistrict,
                'ward_code' => $this->selectedWard,
                'shipping_cost' => $this->shippingCost,
            ]);

            if ($this->shippingCost === 0 && $subtotal < 200) {
                session()->flash('error', 'Không thể tính phí vận chuyển. Vui lòng thử lại.');
            }
        }
    }

    private function updateShippingCost()
    {
        $subtotal = floatval(Cart::instance('cart')->subtotal());
        if ($subtotal >= 200) {
            $this->shippingCost = 0;
            Log::info('Free shipping applied on mount', [
                'subtotal' => $subtotal,
                'user_id' => Auth::id(),
            ]);
        } elseif ($this->selectedDistrict && $this->selectedWard) {
            $this->calculateShippingCost();
        }
    }

    private function getProvinceName($provinceId)
    {
        return collect($this->provinces)->firstWhere('id', $provinceId)['name'] ?? '';
    }

    private function getDistrictName($districtId)
    {
        return collect($this->districts)->firstWhere('id', $districtId)['name'] ?? '';
    }

    private function getWardName($wardId)
    {
        return collect($this->wards)->firstWhere('id', $wardId)['name'] ?? '';
    }

    public function showShippingModal()
    {
        $this->resetForm();
        $this->dispatch('show-shipping-modal');
    }

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'address_type' => 'required|in:home,office,other',
            'name' => 'required|string|max:20',
            'phone' => 'required|string|min:10|max:10|regex:/^[0-9]{10}$/',
            'selectedProvince' => 'required',
            'selectedDistrict' => 'required',
            'selectedWard' => 'required',
            'address' => 'required|string|max:255',
        ]);
    }

    public function addShipping()
    {
        $this->validate([
            'address_type' => 'required|in:home,office,other',
            'name' => 'required|string|max:20',
            'phone' => 'required|string|min:10|max:10|regex:/^[0-9]{10}$/',
            'selectedProvince' => 'required',
            'selectedDistrict' => 'required',
            'selectedWard' => 'required',
            'address' => 'required|string|max:255',
        ]);

        $shipping = new Shipping();
        $shipping->user_id = Auth::id();
        $shipping->address_type = $this->address_type;
        $shipping->name = $this->name;
        $shipping->phone = $this->phone;
        $shipping->province_id = $this->selectedProvince;
        $shipping->province = $this->getProvinceName($this->selectedProvince);
        $shipping->district_id = $this->selectedDistrict;
        $shipping->district = $this->getDistrictName($this->selectedDistrict);
        $shipping->ward_code = $this->selectedWard;
        $shipping->ward = $this->getWardName($this->selectedWard);
        $shipping->address = $this->address;

        $existingShippings = Shipping::where('user_id', Auth::id())->count();

        if ($this->status) {
            Shipping::where('user_id', Auth::id())->update(['status' => 0]);
            $shipping->status = 1;
        } else {
            $shipping->status = $existingShippings === 0 ? 1 : 0;
        }

        $shipping->save();

        Log::info('Shipping address added', [
            'shipping_id' => $shipping->id,
            'user_id' => Auth::id(),
            'name' => $shipping->name,
            'phone' => $shipping->phone,
            'province' => $shipping->province,
            'district' => $shipping->district,
            'ward' => $shipping->ward,
            'address' => $shipping->address,
            'status' => $shipping->status,
        ]);

        if ($shipping->status == 1 || $existingShippings === 0) {
            $this->selectShipping($shipping->id);
        }

        $this->dispatch('close-shipping-modal');
        $this->resetForm();
        flash()->success('Đã thêm địa chỉ giao hàng thành công!');
    }

    public function resetForm()
    {
        $this->address_type = 'home';
        $this->name = '';
        $this->phone = '';
        $this->selectedProvince = null;
        $this->selectedDistrict = null;
        $this->selectedWard = null;
        $this->address = '';
        $this->shipping_id = null;
        $this->status = null;
        $this->titleForm = "Thêm địa chỉ";
        $this->editForm = false;
        $this->sid = null;
        $this->districts = [];
        $this->wards = [];
        $this->resetValidation();
    }

    public function showEditShipping($id)
    {
        $shipping = Shipping::where('id', $id)->first();
        if ($shipping) {
            $this->titleForm = "Chỉnh sửa địa chỉ";
            $this->editForm = true;
            $this->address_type = $shipping->address_type;
            $this->name = $shipping->name;
            $this->phone = $shipping->phone;
            $this->selectedProvince = $shipping->province_id;
            $this->updatedSelectedProvince($shipping->province_id);
            $this->selectedDistrict = $shipping->district_id;
            $this->updatedSelectedDistrict($shipping->district_id);
            $this->selectedWard = $shipping->ward_code;
            $this->address = $shipping->address;
            $this->shipping_id = $shipping->id;
            $this->status = $shipping->status;
            $this->sid = $shipping->id;
            $this->calculateShippingCost();
            $this->dispatch('show-shipping-modal');
        }
    }

    protected $listeners = [
        'deleteConfirmed' => 'deleteShipping',
        'refreshComponent' => '$refresh'
    ];

    public function deleteConfirmation($id)
    {
        $this->delete_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function deleteShipping()
    {
        $shipping = Shipping::find($this->delete_id);
        if ($shipping) {
            $isDefault = $shipping->status == 1;
            $shipping->delete();

            if ($isDefault) {
                $newDefault = Shipping::where('user_id', Auth::id())->first();
                if ($newDefault) {
                    $newDefault->status = 1;
                    $newDefault->save();

                    Log::info('New default shipping address set after deletion', [
                        'new_default_shipping_id' => $newDefault->id,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            $this->dispatch('ShippingDeleted');
            flash()->success('Đã xóa địa chỉ giao hàng!');
        } else {
            $this->dispatch('DeleteFailed');
            flash()->error('Xóa địa chỉ thất bại!');
        }
    }

    public function updateShipping()
    {
        $this->validate([
            'address_type' => 'required|in:home,office,other',
            'name' => 'required|string|max:20',
            'phone' => 'required|string|min:10|max:10|regex:/^[0-9]{10}$/',
            'selectedProvince' => 'required',
            'selectedDistrict' => 'required',
            'selectedWard' => 'required',
            'address' => 'required|string|max:255',
        ]);

        $shipping = Shipping::find($this->shipping_id);
        if ($shipping) {
            if ($shipping->status == 1 && !$this->status) {
                $otherShippings = Shipping::where('user_id', Auth::id())
                    ->where('id', '!=', $this->shipping_id)
                    ->count();

                if ($otherShippings == 0) {
                    session()->flash('error', 'Không thể bỏ chọn địa chỉ mặc định vì đây là địa chỉ duy nhất!');
                    return;
                } else {
                    $newDefault = Shipping::where('user_id', Auth::id())
                        ->where('id', '!=', $this->shipping_id)
                        ->first();
                    if ($newDefault) {
                        $newDefault->status = 1;
                        $newDefault->save();

                        Log::info('New default shipping address set automatically', [
                            'new_default_shipping_id' => $newDefault->id,
                            'user_id' => Auth::id(),
                        ]);
                    }
                }
            }

            $shipping->user_id = Auth::id();
            $shipping->address_type = $this->address_type;
            $shipping->name = $this->name;
            $shipping->phone = $this->phone;
            $shipping->province_id = $this->selectedProvince;
            $shipping->province = $this->getProvinceName($this->selectedProvince);
            $shipping->district_id = $this->selectedDistrict;
            $shipping->district = $this->getDistrictName($this->selectedDistrict);
            $shipping->ward_code = $this->selectedWard;
            $shipping->ward = $this->getWardName($this->selectedWard);
            $shipping->address = $this->address;

            if ($this->status) {
                Shipping::where('user_id', Auth::id())->update(['status' => 0]);
                $shipping->status = 1;
            } else {
                $shipping->status = 0;
            }

            $shipping->save();

            Log::info('Shipping address updated', [
                'shipping_id' => $shipping->id,
                'user_id' => Auth::id(),
                'name' => $shipping->name,
                'phone' => $shipping->phone,
                'province' => $shipping->province,
                'district' => $shipping->district,
                'ward' => $shipping->ward,
                'address' => $shipping->address,
                'status' => $shipping->status,
            ]);

            $this->dispatch('close-shipping-modal');
            $this->resetForm();
            flash()->success('Đã cập nhật địa chỉ giao hàng thành công!');
        }
    }

    private function ensureDefaultShipping()
    {
        $defaultShipping = Shipping::where('user_id', Auth::id())->where('status', 1)->first();
        if (!$defaultShipping) {
            $firstShipping = Shipping::where('user_id', Auth::id())->first();
            if ($firstShipping) {
                $firstShipping->status = 1;
                $firstShipping->save();

                Log::info('Default shipping address set automatically', [
                    'shipping_id' => $firstShipping->id,
                    'user_id' => Auth::id(),
                ]);
            }
        }
    }

    public function updateStatus($checked)
    {
        if ($checked) {
            Shipping::where('user_id', Auth::user()->id)->update(['status' => 0]);
            $this->status = 1;
        } else {
            $this->status = 0;
        }

        if ($this->shipping_id) {
            Shipping::where('id', $this->shipping_id)->update(['status' => $this->status]);
        }
    }

    public function calculateDiscount()
    {
        $this->subtotal = floatval(Cart::instance('cart')->subtotal());

        if (session()->has('coupon')) {
            if (session()->get('coupon')['coupon_type'] == 'fixed') {
                $this->discount = session()->get('coupon')['coupon_value'];
            } else {
                $this->discount = ($this->subtotal * session()->get('coupon')['coupon_value']) / 100;
            }
            $this->subtotalAfterDiscount = $this->subtotal - $this->discount;
            $this->total = $this->subtotalAfterDiscount + ($this->shippingCost ?? 0);
        } else {
            $this->discount = 0;
            $this->subtotalAfterDiscount = $this->subtotal;
            $this->total = $this->subtotalAfterDiscount + ($this->shippingCost ?? 0);
        }
    }

    public function selectShipping($id)
    {
        $shipping = Shipping::find($id);
        if ($shipping && $shipping->user_id === Auth::id()) {
            $this->selectedShippingId = $id;
            $this->selectedProvince = $shipping->province_id;
            $this->updatedSelectedProvince($shipping->province_id);
            $this->selectedDistrict = $shipping->district_id;
            $this->updatedSelectedDistrict($shipping->district_id);
            $this->selectedWard = $shipping->ward_code;
            $this->address_type = $shipping->address_type;
            $this->address = $shipping->address;
            $this->calculateShippingCost();
            $this->calculateDiscount();

            Log::info('Shipping address selected', [
                'shipping_id' => $shipping->id,
                'user_id' => Auth::id(),
                'name' => $shipping->name,
                'phone' => $shipping->phone,
                'province' => $shipping->province,
                'district' => $shipping->district,
                'ward' => $shipping->ward,
                'address' => $shipping->address,
            ]);
        }
    }

    public function placeOrder()
    {
        Log::info('Place Order Initiated', [
            'user_id' => Auth::id(),
            'session_checkout' => session()->get('checkout'),
        ]);

        if (!$this->selectedShippingId) {
            Log::warning('No shipping address selected', [
                'user_id' => Auth::id(),
            ]);
            session()->flash('error', 'Vui lòng chọn một địa chỉ giao hàng!');
            return;
        }

        $shipping = Shipping::where('id', $this->selectedShippingId)
            ->where('user_id', Auth::user()->id)
            ->first();

        if (!$shipping) {
            Log::warning('Selected shipping address not found or invalid', [
                'user_id' => Auth::id(),
                'selected_shipping_id' => $this->selectedShippingId,
            ]);
            session()->flash('error', 'Địa chỉ giao hàng không hợp lệ!');
            return;
        }

        if (!isset($this->shippingCost)) {
            Log::warning('Shipping cost not calculated', [
                'user_id' => Auth::id(),
                'selected_shipping_id' => $this->selectedShippingId,
            ]);
            session()->flash('error', 'Không thể tính phí vận chuyển. Vui lòng chọn lại địa chỉ!');
            return;
        }

        Log::info('Shipping Address Selected', [
            'user_id' => Auth::id(),
            'shipping_id' => $shipping->id,
            'name' => $shipping->name,
            'phone' => $shipping->phone,
            'province' => $shipping->province,
            'district' => $shipping->district,
            'ward' => $shipping->ward,
            'address' => $shipping->address,
            'address_type' => $shipping->address_type,
        ]);

        $this->validate([
            'paymentmode' => 'required|in:COD,PayOS',
        ]);

        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->subtotal = $this->subtotal;
        $order->discount = $this->discount;
        $order->shipping_cost = $this->shippingCost;
        $order->total = $this->total;
        $order->name = $shipping->name;
        $order->phone = $shipping->phone;
        $order->email = Auth::user()->email;
        $order->province_id = $shipping->province_id;
        $order->province = $shipping->province;
        $order->district_id = $shipping->district_id;
        $order->district = $shipping->district;
        $order->ward_code = $shipping->ward_code;
        $order->ward = $shipping->ward;
        $order->address = $shipping->address;
        $order->additional_info = $this->additional_info;
        $order->status = 'ordered';
        $order->save();

        Log::info('Order Created', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'subtotal' => $order->subtotal,
            'discount' => $order->discount,
            'shipping_cost' => $order->shipping_cost,
            'total' => $order->total,
        ]);

        $items = Cart::instance('cart')->content();
        foreach ($items as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;
            $orderItem->save();

            UserInteraction::create([
                'user_id' => Auth::id(),
                'product_id' => $item->id,
                'interaction_type' => 'order',
                'interaction_value' => 5,
                'created_at' => now(),
            ]);
            Log::info('User interaction recorded (order)', [
                'user_id' => Auth::id(),
                'product_id' => $item->id,
                'interaction_value' => 5,
                'order_id' => $order->id,
            ]);

            Product::where('id', $item->id)->update(['quantity' => DB::raw('quantity - ' . $item->qty)]);
        }

        Log::info('Order Items Created', [
            'order_id' => $order->id,
            'items_count' => $items->count(),
        ]);

        $transaction = new Transaction();
        $transaction->user_id = Auth::user()->id;
        $transaction->order_id = $order->id;
        $transaction->amount = $order->total;
        $transaction->status = 'pending';

        if ($this->paymentmode == 'COD') {
            $transaction->payment_type = 'cod';
            $transaction->save();
            Log::info('Transaction Created', [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'payment_type' => 'cod',
            ]);

            $this->thankyou = 1;
            Cart::instance('cart')->destroy();
            session()->forget('checkout');

            Log::info('Order Completed', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'email_sent_to' => $order->email,
            ]);

            Mail::to($order->email)->send(new OrderConfirmedMail($order));
        } elseif ($this->paymentmode == 'PayOS') {
            $transaction->payment_type = 'payos';
            $transaction->save();
            Log::info('Transaction Created', [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'payment_type' => 'payos',
            ]);

            try {
                $payOS = new \PayOS\PayOS(
                    config('services.payos.client_id'),
                    config('services.payos.api_key'),
                    config('services.payos.checksum_key')
                );

                $paymentData = [
                    'orderCode' => $order->id,
                    'amount' => 2000,
                    'description' => 'Thanh toán đơn hàng #' . $order->id,
                    'returnUrl' => route('payment.success'),
                    'cancelUrl' => route('payment.cancel'),
                ];

                $response = $payOS->createPaymentLink($paymentData);
                Log::info('PayOS Payment Link Created', [
                    'order_id' => $order->id,
                    'payment_url' => $response['checkoutUrl'],
                ]);

                $transaction->transaction_id = $response['paymentLinkId'];
                $transaction->save();

                return redirect($response['checkoutUrl']);
            } catch (\Exception $e) {
                Log::error('PayOS Payment Link Creation Failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                session()->flash('error', 'Không thể tạo link thanh toán PayOS. Vui lòng thử lại.');
                $transaction->status = 'declined';
                $transaction->save();
                $order->status = 'canceled';
                $order->save();
            }
        }
    }

    public function verifyCheckout()
    {
        if (!Auth::check()) {
            return Redirect::route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
        }

        if ($this->thankyou) {
            return Redirect::route('thankyou');
        }

        if (!session()->has('checkout') || Cart::instance('cart')->count() === 0) {
            return Redirect::route('cart')->with('error', 'Giỏ hàng của bạn trống hoặc chưa sẵn sàng để thanh toán.');
        }
    }

    public function render()
    {
        $this->verifyCheckout();

        if (session()->has('coupon')) {
            if (Cart::instance('cart')->subtotal() < session()->get('coupon')['cart_value']) {
                session()->forget('coupon');
                $this->calculateDiscount();
            } else {
                $this->calculateDiscount();
            }
        } else {
            $this->calculateDiscount();
        }

        $shippings = Auth::check() ? Shipping::where('user_id', Auth::user()->id)->get() : collect([]);

        Log::info('CheckoutComponent::render called', [
            'user_id' => Auth::id() ?? 'Guest',
            'shippings_count' => $shippings->count(),
            'provinces_count' => count($this->provinces),
            'districts_count' => count($this->districts),
            'wards_count' => count($this->wards),
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'shipping_cost' => $this->shippingCost,
            'total' => $this->total,
        ]);

        if ($shippings->isEmpty() && Auth::check()) {
            Log::warning('No shipping addresses found for user', [
                'user_id' => Auth::id(),
            ]);
        }

        return view('livewire.checkout-component', [
            'shippings' => $shippings,
            'provinces' => $this->provinces,
            'districts' => $this->districts,
            'wards' => $this->wards,
        ]);
    }
}