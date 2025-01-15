<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function index()
    {
        $orders=Order::orderBy('id','desc')->paginate(10);
        $check=false;
        return view('orders',['orders'=>$orders,'check'=>$check]);
    }
}
