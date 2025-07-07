<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;


class CustomerOrderDetailsComponent extends Component
{
    public function render()
    {
        $orderItems = OrderItem::get();
        $products = Product::all();
        $orders = Order::all();
        return view('livewire.customer.customer-order-details-component', ['orders' => $orders], ['orderItems' => $orderItems], ['products' => $products]);
    }
}
