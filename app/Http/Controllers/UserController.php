<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Index
    public function index()
    {
        return view('user.index');
    }

    // Orders
    public function orders()
    {
        $orders = Order::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        return view('user.orders', compact('orders'));
    }

    // Order Details
    public function orderDetails($order_id)
    {
        $order = Order::where('user_id', Auth::user()->id)
            ->where('id', $order_id)
            ->first();
        if ($order) {
            $orderItems = OrderItem::where('order_id', $order_id)
                ->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order_id)->first();
        } else {
            return redirect()->route('login');
        }

        return view('user.order_details', compact(['order', 'orderItems', 'transaction']));
    }

    // Order Cancel
    public function orderCancel(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = "canceled";
        $order->canceled_date = Carbon::now();
        $order->save();
        return redirect()->back()->with('success', 'Order has been canceled successfully!');
    }
}
