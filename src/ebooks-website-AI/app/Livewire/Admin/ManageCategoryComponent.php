<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use App\Exports\CategoriesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;

class ManageCategoryComponent extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $pagesize = 5;
    protected $paginationTheme = 'bootstrap';
    public $delete_id;

    //add category
    public $name;
    public $slug;
    public $image;
    public $status;







    //Hiển thị trang
    public function changepageSize($size)
    {
        $this->pagesize  = $size;
        $this->resetPage();
    }

    // Xác nhận xóa
    protected $listeners = [
        'deleteConfirmed' => 'delete',
        'refreshComponent' => '$refresh'
    ];
    public function deleteConfirmation($id)
    {
        $this->delete_id = $id;
        // Phát sự kiện để hiển thị hộp thoại xác nhận xóa trong JavaScript
        $this->dispatch('show-delete-confirmation');
    }

    // Xử lý xóa khi được xác nhận
    public function delete()
    {
        $category = Category::find($this->delete_id);

        if ($category) {
            // Kiểm tra xem danh mục có sản phẩm liên quan không
            $productCount = Product::where('category_id', $category->id)->count();
            if ($productCount > 0) {
                $this->dispatch('category-has-products');
                flash('Không thể xóa danh mục này vì đang có ' . $productCount . ' sản phẩm liên quan.');
                return;
            }

            $image_path = public_path('admin/category/' . $category->image);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $category->delete();
            flash('Danh mục đã được xóa thành công.');
        }
    }

    //Kết thúc xóa

    //Slug
    public function generateSlug()
    {
        $this->slug = Str::slug($this->name);
    }


    //Thêm danh mục
    public function showCategoryModal()
    {
        $this->dispatch('category-modal');
    }

    public function updated($value)
    {
        $this->validateOnly($value, [
            'name' => 'required',
            'image' => 'required',
            'status' => 'required'
        ]);
    }
    public function addCategory()
    {

        $this->validate([
            'name' => 'required',
            'image' => 'required',
            'status' => 'required'
        ]);
        $category = new Category();
        $category->name = $this->name;
        $category->slug = $this->slug;
        $category->status = $this->status;



        if ($this->image) {
            $image_name = time() . '.' . $this->image->extension();
            $category->image = $image_name;
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->image);
            $image->resize(266, 157);
            $image->toPng()->save(base_path('public/admin/category/' . $image_name));
        }
        $category->save();
        $this->dispatch('category-modal');
        $this->resetForm();
    }

    public $rand;
    public function resetForm()
    {
        $this->name = '';
        $this->image = '';
        $this->status = '';
        $this->rand++;
        $this->titleForm = "Thêm danh mục";
        $this->resetValidation();
    }
    public function hydratedrate()
    {
        $this->resetPage();
    }
    public $editForm = false;
    public $titleForm = "Thêm danh mục";
    public $new_image;
    public $sid;
    public function showEditCategory($id)
    {
        $this->dispatch('category-modal');
        $this->titleForm = "Chỉnh sửa danh mục";
        $this->editForm = true;

        $category = Category::where('id', $id)->first();
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->status = $category->status;
        $this->new_image = $category->image;
        $this->sid = $category->id;
    }

    public function updateCategory()
    {
        $this->validate([
            'name' => 'required',
            'status' => 'required'
        ]);
        $category = Category::find($this->sid);
        $category->name = $this->name;
        $category->slug = $this->slug;
        $category->status = $this->status;
        if ($this->image) {
            unlink(('admin/category/' . $category->image));
            $image_name = time() . '.' . $this->image->extension();
            $category->image = $image_name;
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->image);
            $image->resize(266, 157);
            $image->toPng()->save(base_path('public/admin/category/' . $image_name));
        }
        $category->save();
        $this->dispatch('category-modal');
        $this->resetForm();
        flash()->success('Chỉnh sửa danh mục thành công.');
    }





    //Checkbox
    public $selectAll;
    public $selectedItems = [];

    public function updatedSelectAll($value)
    {
        // Nếu chọn tất cả, thêm toàn bộ ID slider vào mảng
        if ($value) {
            $this->selectedItems = Category::pluck('id')->toArray();
        } else {
            // Nếu bỏ chọn, xóa sạch mảng
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        // Kiểm tra nếu số lượng selected items bằng tổng số slider
        if (count($this->selectedItems) === count(Category::pluck('id'))) {
            $this->selectAll = True;
        } else {
            $this->selectAll = False;
        }
    }
    //Xóa nhiều slider
    public function selecteDelete()
    {
        // Kiểm tra tất cả danh mục một lần bằng whereIn
        $productsCount = Product::whereIn('category_id', $this->selectedItems)->count();
        if ($productsCount > 0) {
            flash('Không thể xóa vì có danh mục đang chứa ' . $productsCount . ' sản phẩm.');
            $this->dispatch('category-has-products');
            return;
        }

        // Nếu không có sản phẩm thì tiến hành xóa
        $categories = Category::whereIn('id', $this->selectedItems)->get();
        foreach ($categories as $category) {
            $image_path = public_path('admin/category/' . $category->image);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $category->delete();
        }

        $this->selectAll = False;
        $this->selectedItems = [];
        flash('Các danh mục đã được xóa thành công.');
    }

    //hoạt động nhiều slider
    public function selecteActive($value)
    {
        foreach ($this->selectedItems as $item) {
            $Category = Category::find($item);
            $Category->status = $value;
            $Category->save();
            $this->selectedItems = [];
            $this->selectAll = false;
        }
        flash('Slider đã được bật.');
    }
    //tắt nhiều slider
    public function selecteInactive($value)
    {
        foreach ($this->selectedItems as $item) {
            $Category = Category::find($item);
            $Category->status = $value;
            $Category->save();
            $this->selectedItems = [];
            $this->selectAll = false;
        }
        flash('Slider đã được tắt.');
    }

    //Chọn màu cho slider
    public function isColor($sliderId)
    {
        if ($this->selectAll == false) {
            if (in_array($sliderId, $this->selectedItems)) {
                return 'bg-1';
            } else {
                return '';
            }
        } else {
            return 'bg-1';
        }
    }
    //Xuất excel
    public function export()
    {
        // return Excel::download(new SlidersExport, 'sliders.xlsx');
        return (new CategoriesExport($this->selectedItems))->download('categories.xlsx');
    }














    //Tìm kiếm
    public $search;
    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        $product = product::all();
        $categories = Category::whereAny(['name'], 'like', '%' . $this->search . '%')
            ->paginate($this->pagesize);
        $this->resetPage();

        return view('livewire.admin.manage-category-component', ['categories' => $categories], ['product' => $product]);
    }
}
