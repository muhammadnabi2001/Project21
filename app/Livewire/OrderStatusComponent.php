<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;
use Livewire\WithPagination;

class OrderStatusComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $check=false;
    public function render()
    {
        $orders=Order::orderBy('id','desc')->paginate(10);
        return view('livewire.order-status-component',compact('orders'));
    }
}
