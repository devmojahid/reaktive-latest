<?php

namespace Modules\Ecommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Ecommerce\Entities\Order;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('order_detail.singleProduct.translate')->latest()->get();
        $title = trans('translate.All Order');
        return view('ecommerce::admin.orders.index', compact('orders', 'title'));
    }

    public function active_orders()
    {
        $orders = Order::with('order_detail.singleProduct.translate')
            ->where(['order_status' => \App\Constants\Status::PROCESSING])
            ->latest()
            ->get();

        $title = trans('translate.Active Order');

        return view('ecommerce::admin.orders.index', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }


    public function reject_orders()
    {

        $orders = Order::with('order_detail.singleProduct.translate')->where(['order_status' => \App\Constants\Status::REJECTED])->latest()->get();

        $title = trans('translate.Rejected Order');

        return view('ecommerce::admin.orders.index', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }

    public function delivered_orders()
    {

        $orders = Order::with('order_detail.singleProduct.translate')->where(['order_status' => \App\Constants\Status::SHIPPED])->latest()->get();

        $title = trans('translate.Delivered Order');

        return view('ecommerce::admin.orders.index', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }

    public function complete_orders()
    {

        $orders = Order::with('order_detail.singleProduct.translate')->where(['order_status' => \App\Constants\Status::COMPLETED])->latest()->get();

        $title = trans('translate.Complete Order');

        return view('ecommerce::admin.orders.index', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }

    public function pending_payment_orders()
    {

        $orders = Order::where('payment_status', 'pending')->latest()->get();

        $title = trans('translate.Pending Payment Order');

        return view('ecommerce::admin.orders.pending_orders', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }

    public function order_show($id)
    {
        $order = Order::with('order_detail')->where('order_id', $id)->first();
        return view('ecommerce::admin.orders.view', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->order_status = $request->input('order_status');
        $order->payment_status = $request->input('payment_status');
        $order->save();

        $notification =  trans('translate.Status updated successfully');
        $notification = array('message' => $notification, 'alert-type' => 'success');

        return back()->with($notification);
    }

    public function paymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->payment_status = $request->input('payment_status');
        $order->save();

        $notification =  trans('translate.Payment status updated successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');

        return back()->with($notification);
    }

    public function orderDelete($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        $notification =  trans('translate.Order deleted successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.order.index')->with($notification);
    }
}
