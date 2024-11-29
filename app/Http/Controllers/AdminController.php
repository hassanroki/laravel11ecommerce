<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class AdminController extends Controller
{
    // Index
    public function index()
    {
        return view('admin.index');
    }

    // Brand
    public function brand()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brand', compact('brands'));
    }

    // Add Brand
    public function brandCreate()
    {
        return view('admin.brand_add');
    }

    // Brand Store
    public function brandStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug', // Corrected 'brand' to 'brands'
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // Nullable image validation
        ]);

        // Initialize an empty variable for the image path
        $saveUrl = null;

        if ($request->file('image')) {
            $manager = new ImageManager(new Driver());
            $imgName = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(124, 124);

            $img->toJpeg(80)->save(base_path('public/uploads/brands/' . $imgName));
            $saveUrl = 'uploads/brands/' . $imgName; // Assign image path
        }

        // Insert into database, using the image path if available
        Brand::insert([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $saveUrl, // Will be null if no image is uploaded
        ]);

        return redirect()->route('admin.brand')->with('success', 'Brand has been added successfully!');
    }

    // Brand Edit
    public function brandEdit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand_edit', compact('brand'));
    }

    // Brand Update
    public function brandUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $id, // Allowing the current brand's slug
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $brand = Brand::findOrFail($id);

        if ($request->file('image')) {
            $manager = new ImageManager(new Driver());
            $imgName = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(124, 124);

            $img->toJpeg(80)->save(base_path('public/uploads/brands/' . $imgName));
            $saveUrl = 'uploads/brands/' . $imgName;

            // Optionally delete the old image if it exists
            if ($brand->image) {
                @unlink(base_path('public/' . $brand->image));
            }

            $brand->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'image' => $saveUrl,
            ]);
        } else {
            $brand->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);
        }

        return redirect()->route('admin.brand')->with('success', 'Brand has been updated successfully!');
    }

    // Brand Delete
    public function brandDelete($id)
    {
        $brand = Brand::find($id);

        // Check if the image exists and delete it
        if ($brand->image && File::exists(public_path($brand->image))) {
            File::delete(public_path($brand->image));
        }

        // Delete the brand from the database
        $brand->delete();

        return redirect()->route('admin.brand')->with('success', 'Brand has been deleted successfully!');
    }

    // Categories
    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    // Categroy Add
    public function categoryCreate()
    {
        return view('admin.category_add');
    }

    // Category Store
    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug', // Corrected 'brand' to 'brands'
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // Nullable image validation
        ]);

        // Initialize an empty variable for the image path
        $saveUrl = null;

        if ($request->file('image')) {
            $manager = new ImageManager(new Driver());
            $imgName = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(124, 124);

            $img->toJpeg(80)->save(base_path('public/uploads/categories/' . $imgName));
            $saveUrl = 'uploads/categories/' . $imgName;
        }

        // Insert into database, using the image path if available
        Category::insert([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $saveUrl, // Will be null if no image is uploaded
        ]);

        return redirect()->route('admin.categories')->with('success', 'Category has been added successfully!');
    }

    // Category Edit
    public function categoryEdit($id)
    {
        $category = Category::find($id);
        return view('admin.category_edit', compact('category'));
    }

    // Category Update
    public function categoryUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $id, // Allowing the current brand's slug
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $category = Category::findOrFail($id);

        if ($request->file('image')) {
            $manager = new ImageManager(new Driver());
            $imgName = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(124, 124);

            $img->toJpeg(80)->save(base_path('public/uploads/categories/' . $imgName));
            $saveUrl = 'uploads/categories/' . $imgName;

            // Optionally delete the old image if it exists
            if ($category->image) {
                @unlink(base_path('public/' . $category->image));
            }

            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'image' => $saveUrl,
            ]);
        } else {
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);
        }

        return redirect()->route('admin.categories')->with('success', 'Category has been updated successfully!');
    }

    // Category Delete
    public function categoryDelete($id)
    {
        $category = Category::find($id);

        // Check if the image exists and delete it
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        // Delete the brand from the database
        $category->delete();
        return redirect()->route('admin.categories')->with('success', 'Category Deleted!');
    }

    // Coupons
    public function Coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact(['coupons']));
    }

    // Coupon Add
    public function couponAdd()
    {
        return view('admin.coupon_add');
    }

    // Coupon Store
    public function couponStore(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('success', 'New Coupon Added Successfully!');
    }

    // Coupon Edit
    public function couponEdit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon_edit', compact(['coupon']));
    }

    // Coupon Update
    public function couponUpdate(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code,' . $id,
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('success', 'Coupon Updated Successfully!');
    }

    // Coupon Delete
    public function couponDelete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('success', 'Coupon Deleted Successdully!');
    }

    // All Order
    public function orders()
    {
        $orders = Order::orderBy('created_at', 'DESC')->paginate(12);
        return view('admin.orders', compact('orders'));
    }

    // Order Details
    public function orderDetails($order_id)
    {
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)
            ->orderBy('id')
            ->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();
        return view('admin.order_details', compact(['order', 'orderItems', 'transaction']));
    }

    // Update Order Status
    public function updateOrderStatus(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;
        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
        } elseif ($request->order_status == 'canceled') {
            $order->delivered_date = Carbon::now();
        }
        $order->save();

        if ($request->order_status == 'delivered') {
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            $transaction->status = 'approved';
            $transaction->save();
        }
        return redirect()->back()->with('success', 'Status Changed Successfully!');
    }

}
