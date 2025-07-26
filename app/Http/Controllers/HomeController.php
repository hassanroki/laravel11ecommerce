<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Index
    public function index()
    {
        $slides           = Slide::where('status', 1)->get()->take(3);
        $categories       = Category::orderBy('name')->get();
        $sproducts        = Product::whereNotNull('sale_price')->where('sale_price', '<>', '')->inRandomOrder()->get()->take(8);
        $minSalePrice = Product::min('sale_price');
        $featuresProducts = Product::where('features', 1)->get()->take(8);
        return view('index', compact(['slides', 'categories', 'sproducts', 'minSalePrice', 'featuresProducts']));
    }

    // Privacy Policy
    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    // Terms Conditions
    public function termsConditions()
    {
        return view('terms-conditions');
    }

    // Contact
    public function contact()
    {
        return view('contact');
    }

    // Contact Store
    public function contactStore(Request $request)
    {
        $request->validate([
            'name'    => 'required|max:100',
            'email'   => 'required|email',
            'phone'   => 'required|numeric|digits:11',
            'comment' => 'required',
        ]);

        $contact          = new Contact();
        $contact->name    = $request->name;
        $contact->email   = $request->email;
        $contact->phone   = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();
        return redirect()->route('home.contact')->with('success', 'Message Successfully Send!');
    }

    // Search Product
    public function searchProduct(Request $request)
    {
        $query   = $request->input('query');
        $results = Product::where('name', 'LIKE', "%$query%") // Corrected SQL LIKE syntax
            ->select('id', 'name', 'slug', 'image')               // Fetch only the necessary fields
            ->take(8)                                             // Limit results to 8
            ->get();

        return response()->json($results);
    }

}
