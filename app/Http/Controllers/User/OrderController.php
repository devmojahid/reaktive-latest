<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Ecommerce\Entities\Order;

class OrderController extends Controller
{
    public function orders(Request $request)
    {

        $orders = Order::with('order_detail')->where('user_id', Auth::user()->id)->latest()->paginate(10);

        return view('user.orders', compact('orders'));
    }

    public function order_show(Request $request, $order_id)
    {
        $user = Auth::guard('web')->user();
        $order = Order::with('order_detail')->where('user_id', $user->id)->where('order_id', $order_id)->first();

        // dd($order);

        return view('user.orders-details', compact('order'));
    }

    public function transactions(Request $request)
    {

        dd('transactions');

        $orders = Order::with('order_detail')->where('user_id', Auth::user()->id)->latest()->paginate(10);

        return view('user.orders', compact('orders'));
    }
}
