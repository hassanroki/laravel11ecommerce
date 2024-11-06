<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    // Empty Cart
    public function emptyCart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }
}
