<?php

namespace App\Livewire\Admin;

use App\Models\Saletimer;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ManageSaleComponent extends Component
{
    use WithPagination;

    public $pagesize = 12;
    public $highlightedId = null;
    public $editingId = null;
    public $start_date;
    public $sale_timer;
    public $status;

    public function changepageSize($size)
    {
        $this->pagesize = $size;
        $this->resetPage();
    }

    public function selectActive($saleId)
    {
        $sale = Saletimer::find($saleId);
        if ($sale) {
            $sale->update(['status' => 1]);
            $this->highlightedId = $saleId;
            flash()->success('Đã bật Flash Sale!');
        } else {
            flash()->error('Không tìm thấy Flash Sale!');
        }
    }

    public function selectInactive($saleId)
    {
        $sale = Saletimer::find($saleId);
        if ($sale) {
            $sale->update(['status' => 0]);
            $this->highlightedId = $saleId;
            flash()->success('Đã tắt Flash Sale!');
        } else {
            flash()->error('Không tìm thấy Flash Sale!');
        }
    }

    public function updateSaleStatus($saleId, $status)
    {
        $sale = Saletimer::find($saleId);
        if ($sale) {
            $sale->update(['status' => $status]);
            $this->highlightedId = $saleId;
            flash()->success('Cập nhật trạng thái Flash Sale thành công!');
        } else {
            flash()->error('Không tìm thấy Flash Sale!');
        }
    }

    public function startEditing($saleId)
    {
        $sale = Saletimer::find($saleId);
        if ($sale) {
            $this->editingId = $saleId;
            $this->start_date = $sale->start_date ? $sale->start_date->format('Y-m-d\TH:i') : null;
            $this->sale_timer = $sale->sale_timer ? $sale->sale_timer->format('Y-m-d\TH:i') : null;
            $this->status = $sale->status;
            $this->dispatch('openEditModal', saleId: $saleId);
        } else {
            flash()->error('Không tìm thấy Flash Sale để chỉnh sửa!');
        }
    }
    public function cancelEditing()
    {
        $this->editingId = null;
        $this->reset(['start_date', 'sale_timer', 'status']);
        $this->dispatch('closeEditModal');
    }
    public function updateSale()
    {
        $this->validate([
            'start_date' => 'required|date',
            'sale_timer' => 'required|date|after:start_date',
            'status' => 'required|boolean',
        ], [
            'start_date.required' => 'Vui lòng nhập thời gian bắt đầu.',
            'start_date.date' => 'Thời gian bắt đầu không hợp lệ.',
            'sale_timer.required' => 'Vui lòng nhập thời gian kết thúc.',
            'sale_timer.date' => 'Thời gian kết thúc không hợp lệ.',
            'sale_timer.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'status.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $sale = Saletimer::find($this->editingId);
        if ($sale) {
            $sale->update([
                'start_date' => Carbon::parse($this->start_date, 'Asia/Ho_Chi_Minh'),
                'sale_timer' => Carbon::parse($this->sale_timer, 'Asia/Ho_Chi_Minh'),
                'status' => $this->status,
            ]);
            $this->editingId = null;
            $this->reset(['start_date', 'sale_timer', 'status']);
            $this->dispatch('closeEditModal');
            flash()->success('Cập nhật Flash Sale thành công!');
        } else {
            flash()->error('Không tìm thấy Flash Sale để cập nhật!');
        }
    }
    private function getSaletimers()
    {
        return Saletimer::query()
            ->orderBy('created_at', 'desc')
            ->paginate($this->pagesize);
    }
    public function render()
    {
        $saletimers = $this->getSaletimers();

        return view('livewire.admin.manage-sale-component', [
            'saletimers' => $saletimers,
        ]);
    }
}
