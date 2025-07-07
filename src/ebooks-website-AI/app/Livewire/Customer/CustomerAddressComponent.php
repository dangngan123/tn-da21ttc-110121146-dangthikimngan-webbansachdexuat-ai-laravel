<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Shipping;
use App\Services\VietnamAddressService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerAddressComponent extends Component
{
    public $province;
    public $district;
    public $ward;
    public $address;
    public $address_type = 'home';
    public $name;
    public $phone;
    public $shipping_id;
    public $delete_id;
    public $status = 0;
    public $editForm = false;
    public $titleForm = "Thêm địa chỉ";
    public $errorMessage = '';
    public $provinces = [];
    public $districts = [];
    public $wards = [];

    protected $vietnamAddressService;

    public function boot(VietnamAddressService $vietnamAddressService)
    {
        $this->vietnamAddressService = $vietnamAddressService;
    }

    public function mount()
    {
        Log::info('CustomerAddressComponent mounted', ['user_id' => Auth::id() ?? 'Guest']);
        $this->provinces = $this->vietnamAddressService->getProvinces();

        if (empty($this->provinces)) {
            Log::warning('No provinces loaded from VietnamAddressService', [
                'user_id' => Auth::id() ?? 'Guest',
            ]);
            $this->errorMessage = 'Không thể tải danh sách tỉnh/thành. Vui lòng thử lại sau.';
        } else {
            Log::info('Provinces loaded', ['count' => count($this->provinces), 'names' => array_column($this->provinces, 'name')]);
        }
    }

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'address_type' => 'required|in:home,office,other',
            'name' => 'required|string|max:20',
            'phone' => 'required|string|min:10|max:10|regex:/^[0-9]{10}$/',
            'province' => 'required',
            'district' => 'required',
            'ward' => 'required',
            'address' => 'required|string|max:255',
        ]);
    }

    public function updatedProvince($value)
    {
        Log::info('Updated province', ['value' => $value, 'provinces' => array_column($this->provinces, 'name')]);
        $this->district = null;
        $this->ward = null;
        $this->districts = [];
        $this->wards = [];
        $this->errorMessage = '';

        if ($value) {
            $province = collect($this->provinces)->firstWhere('name', $value);
            if (!$province) {
                $normalizedValue = str_replace(['Tỉnh ', 'Thành phố '], '', $value);
                $province = collect($this->provinces)->firstWhere('name', $normalizedValue);
            }

            if ($province) {
                Log::info('Province found', ['province' => $province]);
                $this->districts = $this->vietnamAddressService->getDistricts($province['id']);
                Log::info('Districts loaded', ['province_id' => $province['id'], 'districts' => $this->districts]);
                if (empty($this->districts)) {
                    Log::warning('No districts loaded for province', ['province_id' => $province['id']]);
                    $this->errorMessage = 'Không thể tải danh sách quận/huyện. Vui lòng thử lại.';
                }
            } else {
                Log::warning('Province not found in provinces list', ['value' => $value]);
                $this->errorMessage = 'Tỉnh/thành phố không hợp lệ. Vui lòng chọn lại.';
            }
        }
    }

    public function updatedDistrict($value)
    {
        Log::info('Updated district', ['value' => $value, 'districts' => array_column($this->districts, 'name')]);
        $this->ward = null;
        $this->wards = [];
        $this->errorMessage = '';

        if ($value) {
            $district = collect($this->districts)->firstWhere('name', $value);
            if (!$district) {
                $normalizedValue = str_replace(['Huyện ', 'Thành phố ', 'Thị xã '], '', $value);
                $district = collect($this->districts)->firstWhere('name', $normalizedValue);
            }

            if ($district) {
                Log::info('District found', ['district' => $district]);
                $this->wards = $this->vietnamAddressService->getWards($district['id']);
                Log::info('Wards loaded', ['district_id' => $district['id'], 'wards' => $this->wards]);

                // Thử lại nếu danh sách phường/xã rỗng
                if (empty($this->wards)) {
                    Log::warning('No wards loaded for district, retrying...', ['district_id' => $district['id']]);
                    Cache::forget("vietnam_wards_{$district['id']}"); // Xóa cache
                    $this->wards = $this->vietnamAddressService->getWards($district['id']);
                    Log::info('Wards retry result', ['district_id' => $district['id'], 'wards' => $this->wards]);

                    if (empty($this->wards)) {
                        $this->errorMessage = 'Không thể tải danh sách phường/xã. Vui lòng thử lại.';
                    }
                }
            } else {
                Log::warning('District not found in districts list', ['value' => $value]);
                $this->errorMessage = 'Quận/huyện không hợp lệ. Vui lòng chọn lại.';
            }
        }
    }

    public function addShipping()
    {
        $this->validate([
            'address_type' => 'required|in:home,office,other',
            'name' => 'required|string|max:20',
            'phone' => 'required|string|min:10|max:10|regex:/^[0-9]{10}$/',
            'province' => 'required',
            'district' => 'required',
            'ward' => 'required',
            'address' => 'required|string|max:255',
        ]);

        $province = collect($this->provinces)->firstWhere('name', $this->province);
        $district = collect($this->districts)->firstWhere('name', $this->district);
        $ward = collect($this->wards)->firstWhere('name', $this->ward);

        if (!$province || !$district || !$ward) {
            $this->errorMessage = 'Dữ liệu địa chỉ không hợp lệ. Vui lòng chọn lại tỉnh/thành, quận/huyện, hoặc phường/xã.';
            return;
        }

        $shipping = new Shipping();
        $shipping->user_id = Auth::id();
        $shipping->address_type = $this->address_type;
        $shipping->name = $this->name;
        $shipping->phone = $this->phone;
        $shipping->province = $this->province;
        $shipping->province_id = $province['id'];
        $shipping->district = $this->district;
        $shipping->district_id = $district['id'];
        $shipping->ward = $this->ward;
        $shipping->ward_id = $ward['id'];
        $shipping->address = $this->address;
        $shipping->status = $this->status ?: (Shipping::where('user_id', Auth::id())->count() == 0 ? 1 : 0);
        $shipping->save();

        $this->resetForm();
        $this->dispatch('show-shipping-modal');
        session()->flash('message', 'Đã thêm địa chỉ giao hàng thành công!');
    }

    public function resetForm()
    {
        $this->address_type = 'home';
        $this->name = '';
        $this->phone = '';
        $this->province = '';
        $this->district = '';
        $this->ward = '';
        $this->address = '';
        $this->shipping_id = null;
        $this->status = 0;
        $this->editForm = false;
        $this->titleForm = "Thêm địa chỉ";
        $this->errorMessage = '';
        $this->districts = [];
        $this->wards = [];
        $this->resetValidation();
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
            $shipping->delete();
            session()->flash('message', 'Đã xóa địa chỉ thành công!');
        } else {
            $this->errorMessage = 'Xóa địa chỉ thất bại!';
        }
    }

    public function showEditShipping($id)
    {
        $shipping = Shipping::find($id);
        if ($shipping) {
            $this->titleForm = "Chỉnh sửa địa chỉ";
            $this->editForm = true;
            $this->address_type = $shipping->address_type;
            $this->name = $shipping->name;
            $this->phone = $shipping->phone;
            $this->province = $shipping->province;
            $this->district = $shipping->district;
            $this->ward = $shipping->ward;
            $this->address = $shipping->address;
            $this->shipping_id = $shipping->id;
            $this->status = $shipping->status;

            // Tải dữ liệu địa chỉ
            $provinceExists = collect($this->provinces)->firstWhere('name', $shipping->province);
            if (!$provinceExists) {
                $normalizedProvince = str_replace(['Tỉnh ', 'Thành phố '], '', $shipping->province);
                $provinceExists = collect($this->provinces)->firstWhere('name', $normalizedProvince);
                if ($provinceExists) {
                    $this->province = $provinceExists['name'];
                }
            }

            if ($provinceExists) {
                $this->districts = $this->vietnamAddressService->getDistricts($provinceExists['id']);
                Log::info('Districts loaded for edit', ['province_id' => $provinceExists['id'], 'districts' => $this->districts]);

                if (empty($this->districts)) {
                    Log::warning('No districts loaded for edit', ['province_id' => $provinceExists['id']]);
                    $this->errorMessage = 'Không thể tải danh sách quận/huyện. Vui lòng thử lại.';
                    return;
                }

                $districtExists = collect($this->districts)->firstWhere('name', $shipping->district);
                if (!$districtExists) {
                    $normalizedDistrict = str_replace(['Huyện ', 'Thành phố ', 'Thị xã '], '', $shipping->district);
                    $districtExists = collect($this->districts)->firstWhere('name', $normalizedDistrict);
                    if ($districtExists) {
                        $this->district = $districtExists['name'];
                    }
                }

                if ($districtExists) {
                    $this->wards = $this->vietnamAddressService->getWards($districtExists['id']);
                    Log::info('Wards loaded for edit', ['district_id' => $districtExists['id'], 'wards' => $this->wards]);

                    if (empty($this->wards)) {
                        Log::warning('No wards loaded for edit, retrying...', ['district_id' => $districtExists['id']]);
                        Cache::forget("vietnam_wards_{$districtExists['id']}"); // Xóa cache
                        $this->wards = $this->vietnamAddressService->getWards($districtExists['id']);
                        Log::info('Wards retry result', ['district_id' => $districtExists['id'], 'wards' => $this->wards]);

                        if (empty($this->wards)) {
                            $this->errorMessage = 'Không thể tải danh sách phường/xã. Vui lòng thử lại.';
                        }
                    }
                } else {
                    $this->errorMessage = 'Dữ liệu quận/huyện không hợp lệ. Vui lòng chọn lại.';
                }
            } else {
                $this->errorMessage = 'Dữ liệu tỉnh/thành phố không hợp lệ. Vui lòng chọn lại.';
            }

            $this->dispatch('show-shipping-modal');
        }
    }

    public function updateShipping()
    {
        $this->validate([
            'address_type' => 'required|in:home,office,other',
            'name' => 'required|string|max:20',
            'phone' => 'required|string|min:10|max:10|regex:/^[0-9]{10}$/',
            'province' => 'required',
            'district' => 'required',
            'ward' => 'required',
            'address' => 'required|string|max:255',
        ]);

        $province = collect($this->provinces)->firstWhere('name', $this->province);
        $district = collect($this->districts)->firstWhere('name', $this->district);
        $ward = collect($this->wards)->firstWhere('name', $this->ward);

        if (!$province || !$district || !$ward) {
            $this->errorMessage = 'Dữ liệu địa chỉ không hợp lệ. Vui lòng chọn lại tỉnh/thành, quận/huyện, hoặc phường/xã.';
            return;
        }

        $shipping = Shipping::find($this->shipping_id);
        if ($shipping) {
            $shipping->user_id = Auth::id();
            $shipping->address_type = $this->address_type;
            $shipping->name = $this->name;
            $shipping->phone = $this->phone;
            $shipping->province = $this->province;
            $shipping->province_id = $province['id'];
            $shipping->district = $this->district;
            $shipping->district_id = $district['id'];
            $shipping->ward = $this->ward;
            $shipping->ward_id = $ward['id'];
            $shipping->address = $this->address;
            $shipping->status = $this->status;
            $shipping->save();

            $this->resetForm();
            $this->dispatch('show-shipping-modal');
            session()->flash('message', 'Đã cập nhật địa chỉ giao hàng thành công!');
        }
    }

    public function updateStatus($checked)
    {
        if ($checked) {
            Shipping::where('user_id', Auth::id())->update(['status' => 0]);
            $this->status = 1;
        } else {
            $this->status = 0;
        }

        if ($this->shipping_id) {
            Shipping::where('id', $this->shipping_id)->update(['status' => $this->status]);
        }
    }

    public function showShippingModal()
    {
        Log::info('showShippingModal called');
        $this->errorMessage = '';
        $this->dispatch('show-shipping-modal');
    }

    public function render()
    {
        $shippings = Shipping::where('user_id', Auth::id())->get();
        Log::info('Rendering CustomerAddressComponent', [
            'provinces_count' => count($this->provinces),
            'districts_count' => count($this->districts),
            'wards_count' => count($this->wards),
            'shippings_count' => count($shippings),
            'districts_names' => array_column($this->districts, 'name'),
            'wards_names' => array_column($this->wards, 'name'),
        ]);

        return view('livewire.customer.customer-address-component', [
            'provinces' => $this->provinces,
            'districts' => $this->districts,
            'wards' => $this->wards,
            'shippings' => $shippings,
        ]);
    }
}