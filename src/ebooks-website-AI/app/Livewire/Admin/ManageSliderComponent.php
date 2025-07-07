<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Slider;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use App\Exports\SlidersExport;
use Maatwebsite\Excel\Facades\Excel;

class ManageSliderComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $delete_id;
    public $pagesize = 5;

    // Add slider
    public $top_title;
    public $slug;
    public $title;
    public $sub_title;
    public $link;
    public $offer;
    public $image;
    public $start_date;
    public $end_date;
    public $status;
    public $type;

    // Detail slider
    public $detail_top_title;
    public $detail_slug;
    public $detail_title;
    public $detail_sub_title;
    public $detail_link;
    public $detail_offer;
    public $detail_image;
    public $detail_start_date;
    public $detail_end_date;
    public $detail_status;

    // Hiển thị trang
    public function changepageSize($size)
    {
        $this->pagesize = $size;
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
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        $slider = Slider::find($this->delete_id);

        if ($slider) {
            $image_path = public_path('admin/slider/' . $slider->image);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $slider->delete();
            flash('Slider đã được xóa thành công.');
        }
    }

    // Thêm slider
    public function showSliderModal()
    {
        $this->dispatch('rafa-modal');
    }

    public function updated($field)
    {
        $this->validateOnly($field, [
            'top_title' => 'required',
            'slug' => 'required',
            'title' => 'required',
            'sub_title' => 'required',
            'link' => 'required',
            'offer' => 'required',
            'image' => 'nullable|mimes:jpeg,png', // Changed to nullable for edit case
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
        ]);
    }

    public function addSlider()
    {
        $this->validate([
            'top_title' => 'required',
            'slug' => 'required',
            'title' => 'required',
            'sub_title' => 'required',
            'link' => 'required',
            'offer' => 'required',
            'image' => 'required|mimes:jpeg,png',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
        ]);
        $slider = new Slider();
        $slider->top_title = $this->top_title;
        $slider->slug = $this->slug;
        $slider->title = $this->title;
        $slider->sub_title = $this->sub_title;
        $slider->link = $this->link;
        $slider->offer = $this->offer;
        $slider->start_date = $this->start_date;
        $slider->end_date = $this->end_date;
        $slider->status = $this->status;
        $slider->type = 'slider';
        if ($this->image) {
            $image_name = time() . '.' . $this->image->extension();
            $slider->image = $image_name;
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->image);
            $image->resize(400, 400);
            $image->toPng()->save(base_path('public/admin/slider/' . $image_name));
        }
        $slider->save();
        $this->dispatch('rafa-modal');
        $this->resetForm();
        flash('Slider đã được thêm thành công.');
    }

    public $rand;

    public function resetForm()
    {
        $this->top_title = '';
        $this->slug = '';
        $this->title = '';
        $this->sub_title = '';
        $this->link = '';
        $this->offer = '';
        $this->image = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->status = '';
        $this->rand++;
        $this->new_image = '';
        $this->editForm = false;
        $this->titleForm = "Thêm slider";
        $this->resetValidation();
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->top_title);
    }

    // Chỉnh sửa slider
    public $editForm = false;
    public $titleForm = "Thêm slider";
    public $new_image;
    public $sid;

    public function showEditSlider($id)
    {
        $this->dispatch('rafa-modal');
        $this->titleForm = "Chỉnh sửa slider";
        $this->editForm = true;

        $slider = Slider::where('id', $id)->first();
        $this->top_title = $slider->top_title;
        $this->slug = $slider->slug;
        $this->title = $slider->title;
        $this->sub_title = $slider->sub_title;
        $this->link = $slider->link;
        $this->offer = $slider->offer;
        $this->new_image = $slider->image;
        $this->start_date = $slider->start_date;
        $this->end_date = $slider->end_date;
        $this->status = $slider->status;
        $this->sid = $slider->id;
    }

    public function updateSlider()
    {
        $this->validate([
            'top_title' => 'required',
            'slug' => 'required',
            'title' => 'required',
            'sub_title' => 'required',
            'link' => 'required',
            'offer' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
        ]);
        $slider = Slider::find($this->sid);
        $slider->top_title = $this->top_title;
        $slider->slug = $this->slug;
        $slider->title = $this->title;
        $slider->sub_title = $this->sub_title;
        $slider->link = $this->link;
        $slider->offer = $this->offer;
        $slider->start_date = $this->start_date;
        $slider->end_date = $this->end_date;
        $slider->status = $this->status;
        if ($this->image) {
            $image_path = public_path('admin/slider/' . $slider->image);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $image_name = time() . '.' . $this->image->extension();
            $slider->image = $image_name;
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->image);
            $image->resize(400, 400);
            $image->toPng()->save(base_path('public/admin/slider/' . $image_name));
        }
        $slider->save();
        $this->dispatch('rafa-modal');
        $this->resetForm();
        flash('Slider được chỉnh sửa.');
    }

    // Xem chi tiết slider
    public function showDetailSlider($id)
    {
        $slider = Slider::where('id', $id)->first();
        $this->detail_top_title = $slider->top_title;
        $this->detail_slug = $slider->slug;
        $this->detail_title = $slider->title;
        $this->detail_sub_title = $slider->sub_title;
        $this->detail_link = $slider->link;
        $this->detail_offer = $slider->offer;
        $this->detail_image = $slider->image;
        $this->detail_start_date = $slider->start_date;
        $this->detail_end_date = $slider->end_date;
        $this->detail_status = $slider->status;
        $this->dispatch('show-detail-slider-modal');
    }

    public function closeDetailModal()
    {
        $this->detail_top_title = '';
        $this->detail_slug = '';
        $this->detail_title = '';
        $this->detail_sub_title = '';
        $this->detail_link = '';
        $this->detail_offer = '';
        $this->detail_image = '';
        $this->detail_start_date = '';
        $this->detail_end_date = '';
        $this->detail_status = '';
        $this->dispatch('hide-detail-slider-modal');
    }

    // Checkbox
    public $selectAll;
    public $selectedItems = [];

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = Slider::pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        if (count($this->selectedItems) === count(Slider::pluck('id'))) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false;
        }
    }

    public function selecteDelete()
    {
        foreach ($this->selectedItems as $item) {
            $slider = Slider::find($item);
            $image_path = public_path('admin/slider/' . $slider->image);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $slider->delete();
        }
        $this->selectAll = false;
        $this->selectedItems = [];
        flash('Slider đã được bị xóa.');
    }

    public function selecteActive($value)
    {
        foreach ($this->selectedItems as $item) {
            $slider = Slider::find($item);
            $slider->status = $value;
            $slider->save();
            $this->selectedItems = [];
            $this->selectAll = false;
        }
        flash('Slider đã được bật.');
    }

    public function selecteInactive($value)
    {
        foreach ($this->selectedItems as $item) {
            $slider = Slider::find($item);
            $slider->status = $value;
            $slider->save();
            $this->selectedItems = [];
            $this->selectAll = false;
        }
        flash('Slider đã được tắt.');
    }

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

    public function export()
    {
        return (new SlidersExport($this->selectedItems))->download('sliders.xlsx');
    }

    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sliders = Slider::whereAny(['top_title', 'title', 'sub_title', 'link', 'offer'], 'like', '%' . $this->search . '%')
            ->paginate($this->pagesize);
        $this->resetPage();
        return view('livewire.admin.manage-slider-component', ['sliders' => $sliders]);
    }
}