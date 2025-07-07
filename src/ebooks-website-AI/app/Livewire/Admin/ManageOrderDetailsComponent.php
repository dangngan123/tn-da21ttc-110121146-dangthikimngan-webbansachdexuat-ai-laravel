<?php

namespace App\Livewire\Admin;


use App\Models\OrderItem;
use Livewire\Component;

use App\Models\Product;
use App\Models\Order;


class ManageOrderDetailsComponent extends Component
{
    public $order;
    public $orderItems;

    public function mount($order_id)
    {
        $this->order = Order::find($order_id);
        $this->orderItems = $this->order->orderItems;
    }




    public function render()
    {
        $orderItems = OrderItem::get();
        $products = Product::all();
        $orders = Order::all();
        return view('livewire.admin.manage-order-details-component', ['orderItems' => $orderItems], ['products' => $products], ['orders' => $orders]);
    }
}
