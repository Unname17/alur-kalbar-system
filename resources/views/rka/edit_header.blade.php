@extends('rka.layout')

@section('title', 'Edit Identitas RKA')
@section('header_title', 'Step 1: Edit Identitas Dokumen')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="glass-card p-1 rounded-[3rem] relative bg-slate-900">
        
        <div class="grid grid-cols-12">
            {{-- Info Panel (Kiri) --}}
            <div class="col-span-12 md:col-span-4 bg-slate-800/50 p-10 rounded-l-[3rem] border-r border-slate-800 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
                <div class="relative z-10">
                    <div class="px-3 py-1 bg-amber-500/20 text-amber-400 rounded-lg inline-block text-[10px] font-black uppercase tracking-widest mb-4">Mode Edit</div>
                    <h2 class="text-3xl font-black text-white leading-tight mb-4">Perbarui<br>Identitas</h2>
                    <p class="text-xs text-slate-400 leading-relaxed mb-8">
                        Silakan perbarui informasi dasar RKA. Perubahan ini tidak akan menghapus rincian belanja yang sudah diinput.
                    </p>

                    <div class="bg-slate-900/80 p-6 rounded-3xl border border-slate-700">
                        <div class="text-[10px] text-slate-500 uppercase font-bold mb-2">Kegiatan Terpilih</div>
                        <div class="font-bold text-sm text-white leading-snug">{{ $rka->subActivity->nama_sub }}</div>
                        
                        <div class="mt-4 pt-4 border-t border-slate-800 flex justify-between">
                            <div>
                                <div class="text-[9px] text-slate-500">Pagu Kegiatan</div>
                                <div class="text-xs font-mono text-emerald-400 font-bold">
                                    Rp {{ number_format($rka->subActivity->activity->pagu_anggaran ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Input (Kanan) --}}
            <div class="col-span-12 md:col-span-8 p-10 md:p-14">
                <form action="{{ route('rka.update_header', $rka->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT') {{-- PENTING: Untuk proses update --}}

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Sumber Dana</label>
                        <select name="sumber_dana" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">
                            <option value="PENDAPATAN ASLI DAERAH (PAD)" {{ $rka->sumber_dana == 'PENDAPATAN ASLI DAERAH (PAD)' ? 'selected' : '' }}>PENDAPATAN ASLI DAERAH (PAD)</option>
                            <option value="DANA TRANSFER UMUM (DAU)" {{ $rka->sumber_dana == 'DANA TRANSFER UMUM (DAU)' ? 'selected' : '' }}>DANA TRANSFER UMUM (DAU)</option>
                            <option value="DANA ALOKASI KHUSUS (DAK)" {{ $rka->sumber_dana == 'DANA ALOKASI KHUSUS (DAK)' ? 'selected' : '' }}>DANA ALOKASI KHUSUS (DAK)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Waktu Pelaksanaan</label>
                            <input type="text" name="waktu_pelaksanaan" value="{{ $rka->waktu_pelaksanaan }}" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kelompok Sasaran</label>
                            <input type="text" name="kelompok_sasaran" value="{{ $rka->kelompok_sasaran }}" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Lokasi Kegiatan</label>
                        <textarea name="lokasi_kegiatan" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">{{ $rka->lokasi_kegiatan }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama PPTK</label>
                            <input type="text" name="nama_pptk" value="{{ $rka->nama_pptk }}" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">NIP PPTK</label>
                            <input type="text" name="nip_pptk" value="{{ $rka->nip_pptk }}" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="pt-6 flex gap-4">
                        <a href="{{ route('rka.manage', $rka->id) }}" class="flex-1 py-4 bg-slate-800 text-slate-400 rounded-2xl text-sm font-bold text-center hover:bg-slate-700 transition-all">
                            Batal
                        </a>
                        <button type="submit" class="flex-[2] py-4 bg-indigo-600 text-white rounded-2xl text-sm font-bold shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all">
                            Simpan Perubahan & Lanjut ke Step 2
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection