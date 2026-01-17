@extends('pengadaan.layout')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    
    {{-- Progress Steps --}}
    <div class="flex items-center justify-center gap-4 mb-12">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-cyan-500 text-slate-900 flex items-center justify-center font-black shadow-lg shadow-cyan-500/20">1</div>
            <span class="text-cyan-500 font-black text-xs uppercase tracking-widest">Identitas</span>
        </div>
        <div class="h-[2px] w-16 bg-slate-800"></div>
        <div class="flex items-center gap-3 opacity-20">
            <div class="w-10 h-10 rounded-full bg-slate-800 text-white flex items-center justify-center font-black">2</div>
            <span class="text-white font-black text-xs uppercase tracking-widest">Picking</span>
        </div>
    </div>

    <form action="{{ route('pengadaan.store') }}" method="POST">
        @csrf
        <div class="glass-procurement p-10 rounded-[3rem] border border-cyan-500/20 shadow-2xl bg-slate-900/40">
            
            <div class="mb-10 border-b border-slate-800 pb-8">
                <h3 class="text-white font-black text-2xl tracking-tight">Identitas Paket Baru</h3>
                <p class="text-slate-500 text-sm mt-1">Langkah 1: Menentukan wadah pengadaan untuk Dokumen 1.</p>
            </div>

            {{-- Info Box: Bottom-Up Logic --}}
            <div class="bg-cyan-500/5 border border-cyan-500/20 p-6 rounded-3xl mb-10 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 flex items-center justify-center text-cyan-400">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm">Mode Kalkulasi Otomatis</h4>
                    <p class="text-slate-500 text-xs">Pagu anggaran (Doc 1 Bagian 2.1) akan dihitung otomatis setelah Anda memilih item belanja.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Nama Paket --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Nama Paket Pengadaan (Judul Dokumen)</label>
                    <input type="text" name="nama_paket" required 
                        class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-5 text-white focus:border-cyan-500 outline-none transition-all"
                        placeholder="Contoh: Pengadaan ATK dan Meubelair Gabungan">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Jenis Pengadaan --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Jenis Pengadaan</label>
                        <select name="jenis_pengadaan" required class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-5 text-white focus:border-cyan-500 outline-none appearance-none">
                            <option value="Barang">Barang</option>
                            <option value="Jasa Lainnya">Jasa Lainnya</option>
                            <option value="Pekerjaan Konstruksi">Pekerjaan Konstruksi</option>
                        </select>
                    </div>

                    {{-- Metode --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Metode Pemilihan</label>
                        <select name="metode_pemilihan" required class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-5 text-white focus:border-cyan-500 outline-none appearance-none">
                            <option value="E-Purchasing">E-Purchasing (Katalog)</option>
                            <option value="Pengadaan Langsung">Pengadaan Langsung</option>
                        </select>
                    </div>
                </div>

                {{-- Kebijakan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                    <label class="flex items-center gap-4 p-5 rounded-2xl bg-slate-950/50 border border-slate-800 cursor-pointer group">
                        <input type="checkbox" name="is_pdn" value="1" checked class="w-6 h-6 rounded border-slate-700 bg-slate-800 text-cyan-500">
                        <span class="text-sm font-bold text-slate-400 group-hover:text-white transition-colors">Produk Dalam Negeri</span>
                    </label>
                    <label class="flex items-center gap-4 p-5 rounded-2xl bg-slate-950/50 border border-slate-800 cursor-pointer group">
                        <input type="checkbox" name="is_umkm" value="1" checked class="w-6 h-6 rounded border-slate-700 bg-slate-800 text-cyan-500">
                        <span class="text-sm font-bold text-slate-400 group-hover:text-white transition-colors">Usaha Kecil / UMKM</span>
                    </label>
                </div>
            </div>

            <div class="mt-12 flex gap-4">
                <a href="{{ route('pengadaan.index') }}" class="flex-1 py-5 rounded-2xl bg-slate-800 text-slate-500 font-bold text-center text-xs uppercase tracking-widest">Batal</a>
                <button type="submit" class="flex-[2] py-5 rounded-2xl bg-gradient-to-r from-cyan-600 to-emerald-600 text-white font-black shadow-xl shadow-cyan-900/40 transition-all uppercase tracking-widest text-xs">
                    Simpan & Pilih Barang <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection