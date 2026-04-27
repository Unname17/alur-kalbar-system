@extends('pengadaan.layout')

@section('header_title', 'Database Vendor')
@section('title', 'Edit Penyedia')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <form action="{{ route('pengadaan.vendor.update', $vendor->id) }}" method="POST">
        @csrf
        <div class="glass-procurement p-10 rounded-[3rem] border border-amber-500/20 shadow-2xl bg-slate-900/40">
            
            <div class="mb-10 border-b border-slate-800 pb-8 flex justify-between items-end text-amber-500">
                <div>
                    <h3 class="text-white font-black text-2xl tracking-tight">Perbarui Data Vendor</h3>
                    <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-bold text-[10px]">ID: VDR-{{ str_pad($vendor->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <i class="fas fa-edit text-4xl opacity-20"></i>
            </div>

            <div class="space-y-12">
                {{-- SEKSI 1: IDENTITAS LEGAL --}}
                <div class="space-y-6">
                    <label class="text-[10px] font-bold text-amber-500 uppercase tracking-widest">1. Legalitas Perusahaan</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" value="{{ $vendor->nama_perusahaan }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white outline-none focus:border-amber-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Bentuk Usaha</label>
                            <input type="text" name="bentuk_usaha" value="{{ $vendor->bentuk_usaha }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">NPWP Perusahaan</label>
                            <input type="text" name="npwp" value="{{ $vendor->npwp }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white font-mono text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Alamat Kantor</label>
                            <input type="text" name="alamat" value="{{ $vendor->alamat }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                    </div>
                </div>

                {{-- SEKSI 2: OTORITAS --}}
                <div class="space-y-6">
                    <label class="text-[10px] font-bold text-amber-500 uppercase tracking-widest">2. Penanggung Jawab & Kontak</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-8 bg-slate-950/30 rounded-3xl border border-slate-800">
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Nama Direktur / Pimpinan</label>
                            <input type="text" name="nama_direktur" value="{{ $vendor->nama_direktur }}" required class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Jabatan</label>
                            <input type="text" name="jabatan_direktur" value="{{ $vendor->jabatan_direktur }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                    </div>
                </div>

                {{-- SEKSI 3: PERBANKAN --}}
                <div class="space-y-6">
                    <label class="text-[10px] font-bold text-amber-500 uppercase tracking-widest">3. Rekening Pembayaran</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Nama Bank</label>
                            <input type="text" name="nama_bank" value="{{ $vendor->nama_bank }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Nomor Rekening</label>
                            <input type="text" name="no_rekening" value="{{ $vendor->no_rekening }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white font-mono text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex gap-4 border-t border-slate-800 pt-8">
                <a href="{{ route('pengadaan.vendor.index') }}" class="flex-1 py-4 rounded-2xl bg-slate-800 text-slate-400 font-bold text-center text-xs uppercase tracking-widest">Batal</a>
                <button type="submit" class="flex-[2] py-4 rounded-2xl bg-amber-600 text-white font-black shadow-xl transition-all uppercase tracking-widest text-xs">
                    Simpan Perubahan <i class="fas fa-save ml-2"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection