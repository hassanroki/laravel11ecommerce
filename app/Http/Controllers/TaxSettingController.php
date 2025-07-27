<?php
namespace App\Http\Controllers;

use App\Models\TaxSetting;
use Illuminate\Http\Request;

class TaxSettingController extends Controller
{
    // List
    public function index()
    {
        $taxSettings = TaxSetting::all();
        return view('admin.vat', compact('taxSettings'));
    }

    // Vat Edit
    public function edit($id)
    {
        $taxSetting = TaxSetting::findOrFail($id);
        return view('admin.vat_edit', compact('taxSetting'));
    }

    // Vat Updatet
    public function update(Request $request, $id)
    {
        $request->validate([
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        $taxSetting           = TaxSetting::findOrFail($id);
        $taxSetting->tax_rate = $request->tax_rate;
        $taxSetting->save();

        return redirect()->route('tax-settings.index')->with('success', 'Tax rate updated successfully.');
    }
}
