<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class CustomerOrderComponent extends Component
{
    public $orderItems;
    public $orders;
    public $products;
    public $reviews;
    public $selectedOrderId; // To store the ID of the selected order for the modal
    public $selectedOrderItems; // To store the items of the selected order

    public function mount()
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Fetch orders for the authenticated user
        $this->orders = Order::where('user_id', Auth::id())->get();

        // Fetch order items for the user's orders
        $this->orderItems = OrderItem::whereIn('order_id', $this->orders->pluck('id'))->get();

        // Fetch products related to the user's order items
        $productIds = $this->orderItems->pluck('product_id')->unique();
        $this->products = Product::whereIn('id', $productIds)->get();

        // Fetch reviews for the user's order items
        $this->reviews = Review::whereIn('order_item_id', $this->orderItems->pluck('id'))->get();
    }

    public function cancelOrder($orderId)
    {
        $order = Order::where('user_id', Auth::id())->find($orderId);

        if ($order && $order->status === 'ordered') {
            $order->status = 'canceled';
            $order->save();

            flash()->success('Đơn hàng đã được hủy thành công.');

            // Refresh the interface
            $this->mount();
        } else {
            flash()->error('Không thể hủy đơn hàng này.');
        }
    }

    public function showOrderDetails($orderId)
    {
        // Store the selected order ID
        $this->selectedOrderId = $orderId;

        // Fetch the order items for the selected order
        $this->selectedOrderItems = $this->orderItems->where('order_id', $orderId);

        // Dispatch a browser event to open the modal
        $this->dispatch('openOrderDetailsModal');
    }

    public function render()
    {
        return view('livewire.customer.customer-order-component');
    }
}