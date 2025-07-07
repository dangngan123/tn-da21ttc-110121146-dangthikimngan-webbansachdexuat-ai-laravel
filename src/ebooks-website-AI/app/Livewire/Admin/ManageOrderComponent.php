<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use Livewire\WithPagination;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ManageOrderComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pagesize = 5;
    public $search;
    public $statusFilter = '';
    public $page = 1;
    public $selectAll;
    public $selectedItems = [];
    public $selectedOrder = null;
    public $orderStatus = [];

    // Thêm các biến thống kê
    public $pendingOrders = 0; // Chờ xử lý
    public $shippingOrders = 0; // Đang giao
    public $completedOrders = 0; // Hoàn thành
    public $canceledOrders = 0; // Đã hủy
    public $orderedOrders = 0; // Tổng số đơn hàng đã đặt

    public function mount()
    {
        $this->orderStatus = Order::pluck('status', 'id')->toArray();
        $this->updateOrderStats();
    }

    public function updateOrderStats()
    {
        $this->orderedOrders = Order::where('status', 'ordered')->count();
        $this->pendingOrders = Order::where('status', 'processing')->count(); // Giả sử 'processing' là trạng thái chờ xử lý
        $this->shippingOrders = Order::where('status', 'shipped')->count();
        $this->completedOrders = Order::where('status', 'delivered')->count();
        $this->canceledOrders = Order::where('status', 'canceled')->count();
    }

    public function changepageSize($size)
    {
        $this->pagesize = $size;
        $this->resetPage(); // Đặt lại trang về 1 khi thay đổi kích thước trang
    }

    public function handleStatusChange($orderId, $status)
    {
        $currentPage = $this->page; // Lưu trang hiện tại
        if ($status === 'canceled') {
            $this->dispatch('confirm-cancel', ['orderId' => $orderId, 'currentPage' => $currentPage]);
        } else {
            $this->updateOrderStatus($orderId, $status, $currentPage);
        }
    }

    public function confirmCancel($data)
    {
        $orderId = $data['orderId'];
        $currentPage = $data['currentPage'];
        $order = Order::find($orderId);
        if ($order) {
            $order->status = 'canceled';
            $order->save();
            $this->orderStatus[$orderId] = 'canceled';
            $this->updateOrderStats(); // Cập nhật thống kê
            $this->page = $currentPage; // Khôi phục trang hiện tại
            Log::info('Order canceled', ['order_id' => $orderId]);
            flash()->success('Đơn hàng đã được hủy thành công.');
            $this->dispatch('refreshOrderStats'); // Gửi sự kiện để render lại
        } else {
            Log::error('Order not found', ['order_id' => $orderId]);
            flash()->error('Không tìm thấy đơn hàng.');
        }
    }

    public function resetStatus($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $this->orderStatus[$orderId] = $order->status;
        }
    }

    public function updateOrderStatus($id, $status, $currentPage)
    {
        $order = Order::find($id);
        if ($order) {
            $order->status = $status;
            $order->save();
            $this->orderStatus[$id] = $status;
            $this->updateOrderStats(); // Cập nhật thống kê
            $this->page = $currentPage; // Khôi phục trang hiện tại
            Log::info('Order status updated', [
                'order_id' => $order->id,
                'new_status' => $status,
            ]);
            flash()->success('Trạng thái đơn hàng đã được cập nhật thành công.');
            $this->dispatch('refreshOrderStats'); // Gửi sự kiện để render lại
        } else {
            Log::error('Order not found', ['order_id' => $id]);
            flash()->error('Không tìm thấy đơn hàng.');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = Order::pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        if (count($this->selectedItems) === count(Order::pluck('id'))) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false;
        }
    }

    public function selectedDelete()
    {
        $currentPage = $this->page; // Lưu trang hiện tại
        foreach ($this->selectedItems as $item) {
            $order = Order::find($item);
            if ($order) {
                $order->delete();
                Log::info('Order deleted', ['order_id' => $item]);
            } else {
                Log::error('Order not found', ['order_id' => $item]);
            }
        }
        $this->selectAll = false;
        $this->selectedItems = [];
        $this->updateOrderStats(); // Cập nhật thống kê
        $this->page = $currentPage; // Khôi phục trang hiện tại
        flash()->success('Các đơn hàng đã được xóa thành công.');
        $this->dispatch('refreshOrderStats'); // Gửi sự kiện để render lại
    }

    public function isColor($orderId)
    {
        if ($this->selectAll) {
            return 'bg-1';
        }
        return in_array($orderId, $this->selectedItems) ? 'bg-1' : '';
    }

    public function export()
    {
        return (new OrdersExport($this->selectedItems))->download('orders.xlsx');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function showOrderDetails($orderId)
    {
        $order = Order::with(['orderItems.product', 'transaction'])->find($orderId);

        if ($order) {
            $this->selectedOrder = [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone,
                'address' => $order->address,
                'province' => $order->province,
                'district' => $order->district,
                'ward' => $order->ward,
                'total' => $order->total,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'transaction' => [
                    'payment_type' => $order->transaction ? $order->transaction->payment_type : 'unknown',
                ],
                'orderItems' => $order->orderItems->map(function ($item) {
                    return [
                        'name' => $item->product ? $item->product->name : 'Không xác định',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                })->toArray(),
            ];

            $this->dispatch('show-order-details-modal');
        } else {
            $this->selectedOrder = null;
            flash()->error('Không tìm thấy đơn hàng.');
        }
    }

    public function render()
    {
        $this->updateOrderStats();
        $query = Order::query()->with('transaction');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('order_code', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%');
            });
        }

        $orders = $query
           
            ->orderBy('created_at', 'desc')
            ->paginate($this->pagesize);

        return view('livewire.admin.manage-order-component', [
            'orders' => $orders,
            'orderedOrders' => $this->orderedOrders,
            'pendingOrders' => $this->pendingOrders,
            'shippingOrders' => $this->shippingOrders,
            'completedOrders' => $this->completedOrders,
            'canceledOrders' => $this->canceledOrders,
        ]);
    }
}
