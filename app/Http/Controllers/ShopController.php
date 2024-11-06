<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ShopController extends Controller
{
    // Shop Index
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(12);
        return view('shop', compact(['products']));
    }

    // Product View
    public function view($product_slug){
        $product = Product::where('slug', $product_slug)->first();
        $products = Product::where('slug', '<>', $product_slug)->get()->take(8);
        return view('product_details', compact(['product', 'products']));
    }

}
