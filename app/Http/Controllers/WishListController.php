<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class WishListController extends Controller
{
    // Index
    public function index()
    {
        $items = Cart::instance('wishlist')->content();
        return view('wishlist', compact(['items']));
    }

    // Add to Wish List
    public function addToWishList(Request $request)
    {
        Cart::instance('wishlist')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    // Remove Item
    public function removeItem($rowId)
    {
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back();
    }

    // Empty Wish List
    public function emptyWishlist()
    {
        Cart::instance('wishlist')->destroy();
        return redirect()->back();
    }

    // Wishlist From Move to Cart
    public function moveToCart($rowId)
    {
        $item = Cart::instance('wishlist')->get($rowId);
        Cart::instance('wishlist')->remove($rowId);
        Cart::instance('cart')->add($item->id, $item->name, $item->qty, $item->price)->associate('App\Models\Product');
        return redirect()->back();
    }
}
