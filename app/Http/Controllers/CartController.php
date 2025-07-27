<?php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{

    // Cart Index
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact(['items']));
    }

    // Add Cart
    public function addCart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    // Increate Cart Quantity
    public function increaseCartQty($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty     = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    // Decrease Cart Quantity
    public function decreaseCartQty($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty     = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    // Remove Item
    public function removeItem($rowId)
    {
        Cart::instance('cart')->remove($rowId);

        if (Session::has('coupon')) {
            Session::forget('coupon');
            Session::forget('discounts');
        }

        return redirect()->back()->with('success', 'Item removed from the cart');
    }

    // Empty Cart
    public function emptyCart()
    {
        Cart::instance('cart')->destroy();

        if (Session::has('coupon')) {
            Session::forget('coupon');
            Session::forget('discounts');
        }

        return redirect()->back()->with('success', 'Cart has been cleared');
    }

    // Apply Coupon Code
    public function applyCouponCode(Request $request)
    {
        $couponCode = $request->code;
        if (! empty($couponCode)) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
                ->first();

            if (! $coupon) {
                return redirect()->back()->with('error', 'Invalid or expired coupon code!');
            }

            Session::put('coupon', [
                'code'       => $coupon->code,
                'type'       => $coupon->type,
                'value'      => $coupon->value,
                'cart_value' => $coupon->cart_value,
            ]);

            $this->calculateDiscount();

            return redirect()->back()->with('success', 'Coupon has been applied successfully!');
        } else {
            return redirect()->back()->with('error', 'Please provide a coupon code!');
        }
    }

    // Calculate Discount
    public function calculateDiscount()
    {
        $discount = 0;

        if (Session::has('coupon')) {
            $coupon       = Session::get('coupon');
            $cartSubtotal = Cart::instance('cart')->subtotal();

            if ($coupon['type'] == 'fixed') {
                $discount = $coupon['value'];
            } else {
                $discount = ($cartSubtotal * $coupon['value']) / 100;
            }

            $subTotalAfterDiscount = max(0, $cartSubtotal - $discount);
            $taxAfterDiscount      = ($subTotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount    = $subTotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts', [
                'discount' => number_format($discount, 2, '.', ''),
                'subtotal' => number_format($subTotalAfterDiscount, 2, '.', ''),
                'tax'      => number_format($taxAfterDiscount, 2, '.', ''),
                'total'    => number_format($totalAfterDiscount, 2, '.', ''),
            ]);
        }
    }

    // Remove Coupon Code
    public function removeCouponCode()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return redirect()->back()->with('success', 'Coupon has veen removed!');
    }

    // Checkout
    public function checkout()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::user()->id)
            ->where('isDefault', 1)
            ->first();
        return view('checkout', compact(['address']));
    }

    // Place an Order
    public function placeAnOrder(Request $request)
    {
        $user_id = Auth::user()->id;

        $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();
        if (! $address) {
            $request->validate([
                'name'     => 'required|max:100',
                'phone'    => 'required|numeric|digits:11',
                'zip'      => 'required|numeric|digits:4',
                'state'    => 'required',
                'city'     => 'required',
                'address'  => 'required',
                'locality' => 'required',
                'landmark' => 'required',
            ]);

            $address            = new Address();
            $address->user_id   = $user_id;
            $address->name      = $request->name;
            $address->phone     = $request->phone;
            $address->zip       = $request->zip;
            $address->state     = $request->state;
            $address->city      = $request->city;
            $address->address   = $request->address;
            $address->locality  = $request->locality;
            $address->landmark  = $request->landmark;
            $address->country   = '';
            $address->isdefault = true;
            $address->save();
        }

        $this->setAmountForCheckout();

        $order           = new Order();
        $order->user_id  = $user_id;
        $order->subtotal = session()->get('checkout')['subtotal'];
        $order->discount = session()->get('checkout')['discount'];
        $order->tax      = session()->get('checkout')['tax'];
        $order->total    = session()->get('checkout')['total'];
        $order->name     = $address->name;
        $order->phone    = $address->phone;
        $order->locality = $address->locality;
        $order->address  = $address->address;
        $order->city     = $address->city;
        $order->state    = $address->state;
        $order->country  = $address->country;
        $order->landmark = $address->landmark;
        $order->zip      = $address->zip;
        $order->save();

        foreach (Cart::instance('cart')->content() as $item) {
            $orderitem             = new OrderItem();
            $orderitem->product_id = $item->id;
            $orderitem->order_id   = $order->id;
            $orderitem->price      = $item->price;
            $orderitem->quantity   = $item->qty;
            $orderitem->save();
        }

        $transaction           = new Transaction();
        $transaction->user_id  = $user_id;
        $transaction->order_id = $order->id;
        $transaction->mode     = $request->mode;
        $transaction->status   = "pending";
        $transaction->save();

        Cart::instance('cart')->destroy();
        session()->forget('checkout');
        session()->forget('coupon');
        session()->forget('discounts');
        Session::put('order_id', $order->id);
        return redirect()->route('cart.order.confrimation');
    }

    // Set Amount For Checkout
    public function setAmountForCheckout()
    {
        if (! Cart::instance('cart')->count() > 0) {
            session()->forget('checkout');
            return;
        }

        if (session()->has('coupon')) {
            session()->put('checkout', [
                'discount' => session()->get('discounts')['discount'],
                'subtotal' => session()->get('discounts')['subtotal'],
                'tax'      => session()->get('discounts')['tax'],
                'total'    => session()->get('discounts')['total'],
            ]);
        } else {
            session()->put('checkout', [
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax'      => Cart::instance('cart')->tax(),
                'total'    => Cart::instance('cart')->total(),
            ]);
        }
    }

    // Order Confirmation
    public function orderConfirmation()
    {
        if (Session::has('order_id')) {
            $order = Order::find(Session::get('order_id'));
            return view('order_confirmation', compact('order'));
        }
        return redirect()->route('cart.index');
    }

}
