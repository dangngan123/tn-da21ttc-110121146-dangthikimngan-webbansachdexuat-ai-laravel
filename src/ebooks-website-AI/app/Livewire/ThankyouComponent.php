<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ThankyouComponent extends Component
{
    public function render()
    {
        $order = Order::where('user_id', Auth::user()->id)->get()->last();
        return view('livewire.thankyou-component', ['order' => $order]);
    }
}
