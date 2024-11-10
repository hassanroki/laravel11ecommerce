<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // Shop Index
    public function index(Request $request)
    {
        // Per Page Product
        $size = $request->query('size', 12);

        // Product Sorting
        $orderColumn = "";
        $orderOrder = "";
        $order = $request->query('order', -1);
        switch ($order) {
            case 1:
                $orderColumn = "created_at";
                $orderOrder = "DESC";
                break;
            case 2:
                $orderColumn = "created_at";
                $orderOrder = "ASC";
                break;
            case 3:
                $orderColumn = "sale_price";
                $orderOrder = "ASC";
                break;
            case 4:
                $orderColumn = "sale_price";
                $orderOrder = "DESC";
                break;
            default:
                $orderColumn = 'id';
                $orderOrder = "DESC";
        }

        // Brand Filter
        $filterBrands = $request->query('brands', '');
        $brandIds = !empty($filterBrands) ? explode(',', $filterBrands) : [];
        $brands = Brand::orderBy('name', 'ASC')->get();

        $products = Product::when($brandIds, function ($query) use ($brandIds) {
            $query->whereIn('brand_id', $brandIds);
        })->orderBy($orderColumn, $orderOrder)->paginate($size);

        return view('shop', compact(['products', 'size', 'order', 'brands', 'filterBrands']));
    }

    // Product View
    public function view($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $products = Product::where('slug', '<>', $product_slug)->get()->take(8);
        return view('product_details', compact(['product', 'products']));
    }

}
