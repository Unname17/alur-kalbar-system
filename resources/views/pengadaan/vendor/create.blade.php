@extends('pengadaan.layout')

@section('header_title', 'Database Vendor')
@section('title', 'Tambah Penyedia')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <form action="{{ route('pengadaan.vendor.store') }}" method="POST">
        @csrf
        <div class="glass-procurement p-10 rounded-[3rem] border border-cyan-500/20 shadow-2xl bg-slate-900/40">
            
            <div class="mb-10 border-b border-slate-800 pb-8 flex justify-between items-end">
                <div>
                    <h3 class="text-white font-black text-2xl tracking-tight text-emerald-400">Registrasi Penyedia Baru</h3>
                    <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-bold text-[10px]">Identitas Legal & Profil Perusahaan</p>
                </div>
                <i class="fas fa-building text-4xl text-slate-800"></i>
            </div>

            <div class="space-y-12">
                {{-- SEKSI 1: IDENTITAS LEGAL --}}
                <div class="space-y-6">
                    <label class="text-[10px] font-black text-cyan-500 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-id-card"></i> 1. Legalitas Perusahaan
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Nama Perusahaan (Tanpa PT/CV)</label>
                            <input type="text" name="nama_perusahaan" required class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-white focus:border-cyan-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Bentuk Usaha</label>
                            <select name="bentuk_usaha" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-white text-sm outline-none appearance-none">
                                <option value="PT">PT (Perseroan Terbatas)</option>
                                <option value="CV">CV (Commanditaire Vennootschap)</option>
                                <option value="Firma">Firma</option>
                                <option value="Koperasi">Koperasi</option>
                                <option value="Perorangan">Perorangan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">NPWP Perusahaan</label>
                            <input type="text" name="npwp" placeholder="00.000.000.0-000.000" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-white font-mono text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Alamat Kantor</label>
                            <input type="text" name="alamat" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                    </div>
                </div>

                {{-- SEKSI 2: OTORITAS & KONTAK --}}
                <div class="space-y-6">
                    <label class="text-[10px] font-black text-cyan-500 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-user-shield"></i> 2. Penanggung Jawab & Kontak
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-8 bg-slate-950/30 rounded-3xl border border-slate-800">
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Nama Direktur / Pimpinan</label>
                            <input type="text" name="nama_direktur" required class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Jabatan</label>
                            <input type="text" name="jabatan_direktur" value="Direktur" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-sm italic">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Email Perusahaan</label>
                            <input type="email" name="email" placeholder="vendor@email.com" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">No. Telepon / WhatsApp</label>
                            <input type="text" name="no_telepon" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-sm font-mono">
                        </div>
                    </div>
                </div>

                {{-- SEKSI 3: INFORMASI PERBANKAN --}}
                <div class="space-y-6">
                    <label class="text-[10px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-university"></i> 3. Informasi Rekening Pembayaran
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Nama Bank</label>
                            <input type="text" name="nama_bank" placeholder="Contoh: Bank Kalbar" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Nomor Rekening</label>
                            <input type="text" name="no_rekening" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-white font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase mb-2">Nama Pemilik Rekening</label>
                            <input type="text" name="nama_pemilik_rekening" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-white text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex gap-4 border-t border-slate-800 pt-8">
                <a href="{{ route('pengadaan.vendor.index') }}" class="flex-1 py-4 rounded-2xl bg-slate-800 text-slate-400 font-bold text-center text-xs uppercase tracking-widest hover:bg-slate-700 transition-all">Batal</a>
                <button type="submit" class="flex-[2] py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-cyan-600 text-white font-black shadow-xl shadow-emerald-900/40 transition-all uppercase tracking-widest text-xs">
                    Simpan Data Vendor <i class="fas fa-check-circle ml-2"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection