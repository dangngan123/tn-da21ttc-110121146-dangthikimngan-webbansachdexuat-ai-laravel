<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use App\Exports\ProductsExport;
use Carbon\Carbon;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Facades\Gemini;

class ManageProductComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    public $pagesize = 10;
    public $delete_id;

    // Add product
    public $name;
    public $slug;
    public $short_description;
    public $long_description;
    public $reguler_price;
    public $sale_price;
    public $quantity;
    public $publisher;
    public $author;
    public $age;
    public $image;
    public $images = [];
    public $category_id;
    public $is_hot = 0;
    public $discount_type;
    public $discount_value;

    // Create description
    public $showModal = false;
    public $productDetails = '';

    // Search and filter
    public $search;
    public $statusFilter = '';
    public $categoryFilter = '';

    public $page = 1;
    public $selectAll;
    public $selectedItems = [];
    public $file;
    public $rand;
    public $new_image;
    public $new_images = [];
    public $sid;
    public $editForm = false;
    public $titleForm = 'Thêm sản phẩm';

    // Chi tiết sản phẩm
    public $selectedProduct;

    protected $listeners = [
        'deleteConfirmed' => 'delete',
        'refreshComponent' => '$refresh'
    ];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->productDetails = '';
        $this->resetValidation();
    }

    public function generateDescription()
    {
        $this->validate([
            'productDetails' => 'required|min:10',
        ], [
            'productDetails.required' => 'Vui lòng nhập chi tiết mô tả sản phẩm.',
            'productDetails.min' => 'Chi tiết mô tả phải có ít nhất 10 ký tự.',
        ]);

        $prompt = "Hãy giúp tôi tạo mô tả cho sản phẩm {$this->name} dựa trên chi tiết sau: '{$this->productDetails}' như thể bạn là một SEO chuyên nghiệp. Giả sử sản phẩm là một cuốn sách. Trả về một đối tượng JSON với năm trường: short_description (tối đa 200 ký tự, chứa từ khóa chính '{$this->name}'), long_description (tối đa 1000 ký tự, chứa từ khóa chính '{$this->name}', từ khóa phụ 'sản phẩm chất lượng cao', và CTA như 'Mua ngay để trải nghiệm!'), author (nếu chi tiết bao gồm tên tác giả, hãy trích xuất và sử dụng nó; nếu không, hãy để trống, tối đa 100 ký tự), publisher (nếu chi tiết bao gồm tên nhà xuất bản, hãy trích xuất và sử dụng nó; nếu không, hãy để trống, tối đa 100 ký tự), name (nếu có tên tác giả hoặc tên sách, hãy trích xuất và sử dụng nó; nếu không, hãy để trống, tối đa 100 ký tự). Định dạng JSON phải là: {\"short_description\": \"...\", \"long_description\": \"...\", \"author\": \"...\", \"publisher\": \"...\", \"name\": \"...\"}. Không bao bọc trong khối mã hoặc thêm nội dung giải thích.";

        try {
            $jsonResponse = Gemini::generateContent($prompt);
            $descriptions = json_decode($jsonResponse, true);

            if (isset($descriptions['short_description']) && isset($descriptions['long_description'])) {
                Log::info('Gemini Response:', $descriptions);
                $this->short_description = $descriptions['short_description'];
                $this->long_description = $descriptions['long_description'];
                $this->author = isset($descriptions['author']) ? $descriptions['author'] : '';
                $this->publisher = isset($descriptions['publisher']) ? $descriptions['publisher'] : '';
                $this->name = isset($descriptions['name']) ? $descriptions['name'] : '';
                session()->flash('message', 'Tạo mô tả sản phẩm thành công!');
                $this->closeModal();
            } else {
                session()->flash('error', 'Không thể tạo mô tả: Phản hồi không chứa các trường hợp hợp lệ hoặc vượt quá giới hạn ký tự.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi khi tạo mô tả: ' . $e->getMessage());
        }
    }

    public function changepageSize($size)
    {
        $this->pagesize = $size;
        $this->resetPage();
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->name);
    }

    public function deleteConfirmation($id)
    {
        $this->delete_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        $product = Product::find($this->delete_id);

        if ($product) {
            try {
                $image_path = public_path('admin/product/' . $product->image);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            } catch (\Exception $e) {
            }

            try {
                $additional_images = explode(',', $product->images);
                foreach ($additional_images as $additional_image) {
                    $additional_image_path = public_path('admin/product/' . $additional_image);
                    if (file_exists($additional_image_path)) {
                        unlink($additional_image_path);
                    }
                }
            } catch (\Exception $e) {
            }
            $product->delete();
            flash('Sản phẩm đã được xóa thành công.');


            // Gọi API để xóa cache
        try {
            $response = Http::timeout(10)->post('http://localhost:8001/clear_cache');
            if ($response->successful()) {
                Log::info('Đã xóa cache gợi ý sau khi xóa sản phẩm ID: ' . $this->delete_id);
            } else {
                Log::error('Lỗi khi xóa cache sau xóa sản phẩm: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi gọi API clear_cache: ' . $e->getMessage());
        }
        }
    }

    public function showProductModal()
    {
        $this->dispatch('product-modal');
    }

    public function showProductDetail($id)
    {
        $this->selectedProduct = Product::with('category')->find($id);
        $this->dispatch('show-product-detail-modal');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['reguler_price', 'discount_type', 'discount_value'])) {
            $this->calculateSalePrice();
        }

        $this->validateOnly($propertyName, [
            'name' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'reguler_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'publisher' => 'required',
            'author' => 'required',
            'age' => 'required|regex:/^[1-9][0-9]*\+?$/|min:1|max:18',
            'image' => 'required|mimes:jpg,jpeg,png,gif,bmp, webp',
            'images' => 'nullable',
            'category_id' => 'required|exists:categories,id',
        ]);
    }

    public function calculateSalePrice()
    {
        $salePrice = $this->reguler_price ?: 0;

        if ($this->discount_type && $this->discount_value) {
            if ($this->discount_type === 'fixed') {
                $salePrice = $this->reguler_price - $this->discount_value;
            } elseif ($this->discount_type === 'percentage') {
                $salePrice = $this->reguler_price * (1 - $this->discount_value / 100);
            }
        }

        $this->sale_price = $salePrice > 0 ? $salePrice : 0;
    }

    public function addProduct()
    {
        $this->validate([
            'name' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'reguler_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'publisher' => 'required',
            'author' => 'required',
            'age' => 'required|regex:/^[1-9][0-9]*\+?$/|min:1|max:18',
            'image' => 'required|mimes:jpg,jpeg,png,gif,bmp,webp',
            'images' => 'nullable',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = new Product();
        $product->name = $this->name;
        $product->slug = $this->slug ?: Str::slug($this->name);
        $product->short_description = $this->short_description;
        $product->long_description = $this->long_description;
        $product->reguler_price = $this->reguler_price;

        $salePrice = $this->reguler_price;
        if ($this->discount_type && $this->discount_value) {
            if ($this->discount_type === 'fixed') {
                $salePrice = $this->reguler_price - $this->discount_value;
            } elseif ($this->discount_type === 'percentage') {
                $salePrice = $this->reguler_price * (1 - $this->discount_value / 100);
            }
        }
        $product->sale_price = $salePrice > 0 ? $salePrice : 0;
        $product->discount_type = $this->discount_type;
        $product->discount_value = $this->discount_value;

        $product->quantity = $this->quantity;
        $product->publisher = $this->publisher;
        $product->author = $this->author;
        $product->age = $this->age;
        $product->is_hot = $this->is_hot;

        $imageName = Carbon::now()->timestamp . '.' . $this->image->getClientOriginalExtension();
        $manager = new ImageManager(new Driver());
        $image = $manager->read($this->image->getRealPath());
        $image->resize(400, 400);
        $image->toPng()->save(public_path('admin/product/' . $imageName));
        $product->image = $imageName;

        if ($this->images) {
            $imagesname = '';
            foreach ($this->images as $key => $image) {
                $imgName = Carbon::now()->timestamp . $key . '.' . $image->getClientOriginalExtension();
                $manager = new ImageManager(new Driver());
                $processedImage = $manager->read($image->getRealPath());
                $processedImage->resize(400, 400);
                $processedImage->toPng()->save(public_path('admin/product/' . $imgName));
                $imagesname = $imagesname ? $imagesname . ',' . $imgName : $imgName;
            }
            $product->images = $imagesname;
        }

        $product->category_id = $this->category_id;
        $product->save();

        $this->dispatch('product-modal');
        $this->resetForm();
        flash('Sản phẩm đã được thêm thành công.');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->slug = '';
        $this->short_description = '';
        $this->long_description = '';
        $this->reguler_price = '';
        $this->sale_price = '';
        $this->discount_type = '';
        $this->discount_value = '';
        $this->quantity = '';
        $this->publisher = '';
        $this->author = '';
        $this->age = '';
        $this->image = '';
        $this->images = '';
        $this->category_id = '';
        $this->is_hot = '';
        $this->rand++;
        $this->resetValidation();
    }

    public function hydrate()
    {
        $this->resetPage();
    }

    public function showEditProduct($id)
    {
        $this->dispatch('product-modal');
        $this->titleForm = 'Cập nhật sản phẩm';
        $this->editForm = true;

        $product = Product::where('id', $id)->first();
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->short_description = $product->short_description;
        $this->long_description = $product->long_description;
        $this->reguler_price = $product->reguler_price;
        $this->sale_price = $product->sale_price;
        $this->discount_type = $product->discount_type;
        $this->discount_value = $product->discount_value;
        $this->quantity = $product->quantity;
        $this->publisher = $product->publisher;
        $this->author = $product->author;
        $this->age = $product->age;
        $this->is_hot = $product->is_hot;
        $this->category_id = $product->category_id;
        $this->new_image = $product->image;
        $this->new_images = explode(',', $product->images);
        $this->sid = $product->id;
    }

    public function updateProduct()
    {
        $this->validate([
            'name' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'reguler_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'publisher' => 'required',
            'author' => 'required',
            'age' => 'required|regex:/^[1-9][0-9]*\+?$/|min:1|max:18',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::find($this->sid);
        $product->name = $this->name;
        $product->slug = $this->slug ?: Str::slug($this->name);
        $product->short_description = $this->short_description;
        $product->long_description = $this->long_description;
        $product->reguler_price = $this->reguler_price;

        $salePrice = $this->reguler_price;
        if ($this->discount_type && $this->discount_value) {
            if ($this->discount_type === 'fixed') {
                $salePrice = $this->reguler_price - $this->discount_value;
            } elseif ($this->discount_type === 'percentage') {
                $salePrice = $this->reguler_price * (1 - $this->discount_value / 100);
            }
        }
        $product->sale_price = $salePrice > 0 ? $salePrice : 0;
        $product->discount_type = $this->discount_type;
        $product->discount_value = $this->discount_value;

        $product->quantity = $this->quantity;
        $product->publisher = $this->publisher;
        $product->author = $this->author;
        $product->age = $this->age;
        $product->is_hot = $this->is_hot;
        $product->category_id = $this->category_id;

        if ($this->image && $this->image instanceof \Illuminate\Http\UploadedFile) {
            $oldImagePath = public_path('admin/product/' . $product->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            $imageName = Carbon::now()->timestamp . '.' . $this->image->getClientOriginalExtension();
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->image->getRealPath());
            $image->resize(400, 400);
            $image->toPng()->save(public_path('admin/product/' . $imageName));
            $product->image = $imageName;
        }

        if ($this->images && is_array($this->images)) {
            $imagesName = '';
            foreach ($this->images as $key => $image) {
                if ($image instanceof \Illuminate\Http\UploadedFile) {
                    $imgName = Carbon::now()->timestamp . $key . '.' . $image->getClientOriginalExtension();
                    $manager = new ImageManager(new Driver());
                    $processedImage = $manager->read($image->getRealPath());
                    $processedImage->resize(400, 400);
                    $processedImage->toPng()->save(public_path('admin/product/' . $imgName));
                    $imagesName = $imagesName ? $imagesName . ',' . $imgName : $imgName;
                }
            }
            $product->images = $imagesName;
        }

        $product->save();
        $this->dispatch('product-modal');
        $this->resetForm();
        flash('Sản phẩm đã được cập nhật thành công.');


        // Gọi API để xóa cache
    try {
        $response = Http::timeout(10)->post('http://localhost:8001/clear_cache');
        if ($response->successful()) {
            Log::info('Đã xóa cache gợi ý sau khi cập nhật sản phẩm ID: ' . $this->sid);
        } else {
            Log::error('Lỗi khi xóa cache sau cập nhật sản phẩm: ' . $response->body());
        }
    } catch (\Exception $e) {
        Log::error('Lỗi khi gọi API clear_cache: ' . $e->getMessage());
    }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = Product::pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        if (count($this->selectedItems) === count(Product::pluck('id'))) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false;
        }
    }

    public function selecteDelete()
    {
        try {
            foreach ($this->selectedItems as $item) {
                $product = Product::find($item);

                if ($product) {
                    try {
                        $image_path = public_path('admin/product/' . $product->image);
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    } catch (\Exception $e) {
                    }

                    try {
                        $additional_images = explode(',', $product->images);
                        foreach ($additional_images as $additional_image) {
                            if (!empty($additional_image)) {
                                $additional_image_path = public_path('admin/product/' . $additional_image);
                                if (file_exists($additional_image_path)) {
                                    unlink($additional_image_path);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                    }

                    $product->delete();
                }
            }

            $this->selectAll = false;
            $this->selectedItems = [];
            flash('Sản phẩm đã được xóa thành công.');
        } catch (\Exception $e) {
            flash('Có lỗi xảy ra khi xóa sản phẩm: ' . $e->getMessage())->error();
        
// Gọi API để xóa cache
        try {
            $response = Http::timeout(10)->post('http://localhost:8001/clear_cache');
            if ($response->successful()) {
                Log::info('Đã xóa cache gợi ý sau khi xóa hàng loạt sản phẩm.');
            } else {
                Log::error('Lỗi khi xóa cache sau xóa hàng loạt: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi gọi API clear_cache: ' . $e->getMessage());
        }
    } catch (\Exception $e) {
        flash('Có lỗi xảy ra khi xóa sản phẩm: ' . $e->getMessage())->error();
    }
    
        
}

    public function isColor($productId)
    {
        if ($this->selectAll == false) {
            if (in_array($productId, $this->selectedItems)) {
                return 'bg-1';
            } else {
                return '';
            }
        } else {
            return 'bg-1';
        }
    }

    public function export()
    {
        return (new ProductsExport($this->selectedItems))->download('products.xlsx');
    }

    public function updateIsHot($checked)
    {
        $this->is_hot = $checked ? 1 : 0;
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            if ($this->file) {
                Log::info('Bắt đầu nhập file Excel: ' . $this->file->getClientOriginalName());
                Excel::import(new ProductImport, $this->file);
                Log::info('Nhập file Excel hoàn tất.');
                session()->flash('message', 'Nhập dữ liệu thành công!');
                $this->file = null;
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            session()->flash('error', 'Lỗi dữ liệu: ' . implode(", ", array_map(function ($failure) {
                return "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }, $e->failures())));
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi nhập dữ liệu: ' . $e->getMessage());
            Log::error('Import error: ' . $e->getMessage());
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function removeImage($type, $index = null)
    {
        if ($type === 'main') {
            $this->image = null;
            $this->new_image = null;
        }

        if ($type === 'additional' && $index !== null) {
            unset($this->images[$index]);
            unset($this->new_images[$index]);
        }
    }

    public function render()
    {
        $query = Product::query();

        if ($this->statusFilter == 'in_stock') {
            $query->where('quantity', '>', 10);
        } elseif ($this->statusFilter == 'low_stock') {
            $query->whereBetween('quantity', [1, 10]);
        } elseif ($this->statusFilter == 'out_of_stock') {
            $query->where('quantity', 0);
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        $categories = Category::all();
        $products = $query->paginate($this->pagesize);

        return view('livewire.admin.manage-product-component', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
