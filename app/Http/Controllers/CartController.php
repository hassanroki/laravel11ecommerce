<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    // Decrease Cart Quantity
    public function decreaseCartQty($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    // Remove Item
    public function removeItem($rowId)
    {
        // Remove the specific cart item
        Cart::instance('cart')->remove($rowId);

        // Check if a coupon is applied
        if (Session::has('coupon')) {
            // Remove coupon and discount session
            Session::forget('coupon');
            Session::forget('discounts');
        }

        return redirect()->back()->with('success', 'Item removed from the cart');
    }

    // Empty Cart
    public function emptyCart()
    {
        // Clear the cart
        Cart::instance('cart')->destroy();

        // Check if a coupon is applied
        if (Session::has('coupon')) {
            // Remove coupon and discount session
            Session::forget('coupon');
            Session::forget('discounts');
        }

        return redirect()->back()->with('success', 'Cart has been cleared');
    }

    // Apply Coupon Code
    public function applyCouponCode(Request $request)
    {
        $couponCode = $request->code; // Correct the input name to match the form input.
        if (!empty($couponCode)) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
                ->first();

            if (!$coupon) {
                return redirect()->back()->with('error', 'Invalid or expired coupon code!');
            }

            // Store coupon details in session
            Session::put('coupon', [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value,
            ]);

            // Calculate discount and update session
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
            $coupon = Session::get('coupon');
            $cartSubtotal = Cart::instance('cart')->subtotal();

            if ($coupon['type'] == 'fixed') {
                $discount = $coupon['value'];
            } else {
                $discount = ($cartSubtotal * $coupon['value']) / 100;
            }

            $subTotalAfterDiscount = max(0, $cartSubtotal - $discount); // Ensure subtotal isn't negative.
            $taxAfterDiscount = ($subTotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subTotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts', [
                'discount' => number_format($discount, 2, '.', ''),
                'subtotal' => number_format($subTotalAfterDiscount, 2, '.', ''),
                'tax' => number_format($taxAfterDiscount, 2, '.', ''),
                'total' => number_format($totalAfterDiscount, 2, '.', ''),
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

}
