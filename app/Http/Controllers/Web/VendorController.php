<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    // Gunakan koneksi database pengadaan sesuai migration
    protected $connection = 'modul_pengadaan';

    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = DB::connection($this->connection)->table('procurement_vendors');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_perusahaan', 'LIKE', "%{$search}%")
                ->orWhere('nama_direktur', 'LIKE', "%{$search}%")
                ->orWhere('npwp', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Ganti get() menjadi paginate(10) dan bawa query string pencarian
        $vendors = $query->orderBy('nama_perusahaan', 'asc')
                        ->paginate(10)
                        ->withQueryString();

        return view('pengadaan.vendor.index', compact('vendors', 'search'));
    }

    public function create()
    {
        return view('pengadaan.vendor.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Lengkap dengan Format NPWP
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'email'           => 'nullable|email|unique:modul_pengadaan.procurement_vendors,email',
            'nama_direktur'   => 'required|string|max:255',
            // Regex NPWP Indonesia: 00.000.000.0-000.000
            'npwp'            => [
                'nullable',
                'regex:/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\.[0-9]{1}-[0-9]{3}\.[0-9]{3}$/'
            ],
        ], [
            'npwp.regex' => 'Format NPWP tidak valid. Gunakan format: 00.000.000.0-000.000',
            'email.unique' => 'Email ini sudah terdaftar untuk vendor lain.'
        ]);

        // 2. Simpan Data ke Tabel procurement_vendors
        DB::connection($this->connection)->table('procurement_vendors')->insert([
            'nama_perusahaan'       => $request->nama_perusahaan,
            'bentuk_usaha'          => $request->bentuk_usaha,
            'npwp'                  => $request->npwp,
            'alamat'                => $request->alamat,
            'email'                 => $request->email,
            'no_telepon'            => $request->no_telepon,
            'nama_direktur'         => $request->nama_direktur,
            'jabatan_direktur'      => $request->jabatan_direktur ?? 'Direktur',
            'nama_bank'             => $request->nama_bank,
            'no_rekening'           => $request->no_rekening,
            'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        return redirect()->route('pengadaan.vendor.index')->with('success', 'Vendor baru berhasil didaftarkan ke database.');
    }

    public function edit($id)
    {
        $vendor = DB::connection($this->connection)->table('procurement_vendors')->where('id', $id)->first();
        return view('pengadaan.vendor.edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'nama_direktur'   => 'required|string|max:255',
            'npwp'            => [
                'nullable',
                'regex:/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\.[0-9]{1}-[0-9]{3}\.[0-9]{3}$/'
            ],
        ]);

        DB::connection($this->connection)->table('procurement_vendors')->where('id', $id)->update([
            'nama_perusahaan'       => $request->nama_perusahaan,
            'bentuk_usaha'          => $request->bentuk_usaha,
            'npwp'                  => $request->npwp,
            'alamat'                => $request->alamat,
            'email'                 => $request->email,
            'no_telepon'            => $request->no_telepon,
            'nama_direktur'         => $request->nama_direktur,
            'jabatan_direktur'      => $request->jabatan_direktur,
            'nama_bank'             => $request->nama_bank,
            'no_rekening'           => $request->no_rekening,
            'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
            'updated_at'            => now(),
        ]);

        return redirect()->route('pengadaan.vendor.index')->with('success', 'Data profil vendor telah diperbarui.');
    }

    public function destroy($id)
    {
        DB::connection($this->connection)->table('procurement_vendors')->where('id', $id)->delete();
        return redirect()->route('pengadaan.vendor.index')->with('success', 'Vendor berhasil dihapus dari database.');
    }
}