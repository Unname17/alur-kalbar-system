<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor\Vendor;

class VendorWebController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->paginate(10);
        // PERUBAHAN: Path view mengarah ke procurement, tapi nama file dibedakan
        return view('procurement.vendor_index', compact('vendors'));
    }

    public function create()
    {
        return view('procurement.vendor_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'director_name' => 'required|string|max:255',
            'npwp' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        Vendor::create($request->all());

        return redirect()->route('procurement.vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan');
    }

    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('procurement.vendor_edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'director_name' => 'required|string|max:255',
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->all());

        return redirect()->route('procurement.vendors.index')
            ->with('success', 'Vendor berhasil diupdate');
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        // Cek relasi dulu biar aman
        if($vendor->procurements()->count() > 0){
             return back()->with('error', 'Gagal hapus. Vendor sedang mengerjakan paket.');
        }
        $vendor->delete();
        return redirect()->route('procurement.vendors.index')
            ->with('success', 'Vendor dihapus');
    }
}