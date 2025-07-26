<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Index
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Create
        $categories = Category::select('id', 'name')
            ->orderBy('name')
            ->get();
        $brands = Brand::select('id', 'name')
            ->orderBy('name')
            ->get();
        return view('admin.product_create', compact(['categories', 'brands']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'SKU' => 'required',
            'stock_status' => 'required|in:instock,outofstock',
            'features' => 'required',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // Primary image
            'images.*' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // Multiple images
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $saveUrl = null;
        $imagePaths = []; // Array to store paths of multiple images

        // Primary Image (Single File)
        if ($request->file('image')) {
            $manager = new ImageManager(new Driver());
            $imgName = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'))->resize(540, 689);
            $img->toJpeg(80)->save(base_path('public/uploads/products/' . $imgName));
            $saveUrl = 'uploads/products/' . $imgName;
        }

        // Multiple Images
        if ($request->file('images')) {
            foreach ($request->file('images') as $multiImage) {
                $imgName = hexdec(uniqid()) . '.' . $multiImage->getClientOriginalExtension();
                $img = $manager->read($multiImage)->resize(540, 689);
                $img->toJpeg(80)->save(base_path('public/uploads/products/' . $imgName));
                $imagePaths[] = 'uploads/products/' . $imgName;
            }
        }

        Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'regular_price' => $request->regular_price,
            'sale_price' => $request->sale_price,
            'SKU' => $request->SKU,
            'stock_status' => $request->stock_status,
            'features' => $request->has('features'),
            'quantity' => $request->quantity,
            'image' => $saveUrl,
            'images' => json_encode($imagePaths),
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);

        return redirect()->route('products.index')->with('success', 'Product has been added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->images = json_decode($product->images, true); // Decode JSON to array
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product_edit', compact(['product', 'categories', 'brands']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $product->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'SKU' => 'required',
            'stock_status' => 'required|in:instock,outofstock',
            'features' => 'required',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'images.*' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $manager = new ImageManager(new Driver());
        $saveUrl = $product->image;
        $imagePaths = json_decode($product->images, true) ?: [];

        // Primary Image Update
        if ($request->file('image')) {
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image)); // Delete old primary image
            }
            $imgName = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'))->resize(540, 689);
            $img->toJpeg(80)->save(base_path('public/uploads/products/' . $imgName));
            $saveUrl = 'uploads/products/' . $imgName;
        }

        // Delete existing multiple images if new images are uploaded
        if ($request->file('images')) {
            // Delete old multiple images
            foreach ($imagePaths as $oldImagePath) {
                if (file_exists(public_path($oldImagePath))) {
                    unlink(public_path($oldImagePath));
                }
            }
            $imagePaths = []; // Clear old paths

            // Add new multiple images
            foreach ($request->file('images') as $multiImage) {
                $imgName = hexdec(uniqid()) . '.' . $multiImage->getClientOriginalExtension();
                $img = $manager->read($multiImage)->resize(540, 689);
                $img->toJpeg(80)->save(base_path('public/uploads/products/' . $imgName));
                $imagePaths[] = 'uploads/products/' . $imgName;
            }
        }

        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'regular_price' => $request->regular_price,
            'sale_price' => $request->sale_price,
            'SKU' => $request->SKU,
            'stock_status' => $request->stock_status,
            'features' => $request->has('features'),
            'quantity' => $request->quantity,
            'image' => $saveUrl,
            'images' => json_encode($imagePaths), // Update array as JSON
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete primary image
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
        // Delete multiple images
        $imagePaths = json_decode($product->images, true) ?: [];
        foreach ($imagePaths as $path) {
            if (file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

}
