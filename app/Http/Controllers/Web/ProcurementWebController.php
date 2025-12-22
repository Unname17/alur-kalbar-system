<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 1. Import Model dari folder Vendor (Modul Pengadaan)
use App\Models\Vendor\Procurement;
use App\Models\Vendor\Vendor;

// 2. Import Model RKA (Modul Anggaran - Lintas Modul)
// Pastikan namespace ini sesuai dengan lokasi model Rka kamu
use App\Models\Rka; 

class ProcurementWebController extends Controller
{
    /**
     * Menampilkan Dashboard/List Pengadaan
     */
    public function index()
    {
        // Ambil data pengadaan, urutkan terbaru
        $procurements = Procurement::with('vendor') // Eager load vendor agar performa cepat
            ->latest()
            ->paginate(10);

        return view('procurement.index', compact('procurements'));
    }

    /**
     * Halaman Form Tambah Paket Pengadaan
     */
    public function create()
    {
        // 1. Ambil data Vendor yang statusnya Active (dari DB modul_pengadaan)
        $vendors = Vendor::where('status', 'active')->get();

        // 2. Ambil data RKA yang statusnya Approved (dari DB modul_anggaran)
        // Logika: User memilih anggaran mana yang mau dibelanjakan
        $rkaOptions = Rka::where('status', 'approved') // Sesuaikan logika status 'disahkan' di sistemmu
                         ->get();

        return view('procurement.create', compact('vendors', 'rkaOptions'));
    }

    /**
     * Proses Simpan Data (Store)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'rka_id'            => 'required|integer', // ID RKA (Lintas Modul)
            'package_name'      => 'required|string|max:255',
            'method'            => 'required|in:tender,seleksi,penunjukan_langsung,pengadaan_langsung,epurchasing,swakelola',
            'target_date_start' => 'nullable|date',
            'target_date_end'   => 'nullable|date|after_or_equal:target_date_start',
            'vendor_id'         => 'nullable|exists:modul_pengadaan.vendors,id', // Cek exists di tabel vendors DB pengadaan
        ]);

        // 2. Ambil Data RKA (Untuk Snapshot)
        $rkaSource = Rka::find($request->rka_id);

        if (!$rkaSource) {
            return back()->withInput()->withErrors(['rka_id' => 'Data RKA sumber tidak ditemukan.']);
        }

        // 3. Simpan ke Database Pengadaan
        Procurement::create([
            // --- Link & Snapshot ke RKA ---
            'rka_source_id'        => $rkaSource->id,
            'source_activity_name' => $rkaSource->activity_name, // Snapshot Nama Kegiatan
            'budget_ceiling'       => $rkaSource->total_amount,  // Snapshot Pagu Anggaran
            
            // --- Data Pengadaan ---
            'package_name'         => $request->package_name,
            'method'               => $request->method,
            'target_date_start'    => $request->target_date_start,
            'target_date_end'      => $request->target_date_end,
            'vendor_id'            => $request->vendor_id, // Bisa null jika vendor belum dipilih
            'status'               => 'perencanaan',       // Status awal
        ]);

        return redirect()->route('procurement.index')
            ->with('success', 'Paket pengadaan berhasil dibuat!');
    }

    /**
     * Menampilkan Detail Paket
     */
    public function show($id)
    {
        $procurement = Procurement::with('vendor')->findOrFail($id);
        
        // Kirim data ke view
        // Di view nanti bisa panggil {{ $procurement->rka_live_data }} untuk cek data asli RKA
        return view('procurement.show', compact('procurement'));
    }
}