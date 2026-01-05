@extends('rka.layout')

@section('title', 'Setup RKA')
@section('header_title', 'Step 1: Identitas Dokumen')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="glass-card p-1 rounded-[3rem] relative bg-slate-900">
        
        <div class="grid grid-cols-12">
            {{-- Info Panel (Kiri) --}}
            <div class="col-span-12 md:col-span-4 bg-slate-800/50 p-10 rounded-l-[3rem] border-r border-slate-800 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
                <div class="relative z-10">
                    <div class="px-3 py-1 bg-indigo-500/20 text-indigo-400 rounded-lg inline-block text-[10px] font-black uppercase tracking-widest mb-4">Langkah 1 dari 2</div>
                    <h2 class="text-3xl font-black text-white leading-tight mb-4">Identitas<br>Kegiatan</h2>
                    <p class="text-xs text-slate-400 leading-relaxed mb-8">
                        Lengkapi informasi dasar dokumen RKA (Rencana Kerja Anggaran) sebelum mengisi rincian biaya.
                    </p>

                    <div class="bg-slate-900/80 p-6 rounded-3xl border border-slate-700">
                        <div class="text-[10px] text-slate-500 uppercase font-bold mb-2">Kegiatan Terpilih</div>
                        {{-- HANYA GUNAKAN $sub, JANGAN $rka --}}
                        <div class="font-bold text-sm text-white leading-snug">{{ $sub->nama_sub }}</div>
                        
                        <div class="mt-4 pt-4 border-t border-slate-800 flex justify-between">
<div>
    <div class="text-[9px] text-slate-500">Pagu Indikatif Kegiatan</div>
    <div class="text-xs font-mono text-emerald-400 font-bold">
        @if($sub->activity && $sub->activity->pagu_anggaran > 0)
            Rp {{ number_format($sub->activity->pagu_anggaran, 0, ',', '.') }}
        @else
            <span class="text-rose-400">Rp 0 (Belum diset)</span>
        @endif
    </div>
</div>
                            <div class="text-right">
                                <div class="text-[9px] text-slate-500">Klasifikasi</div>
                                <div class="text-xs font-mono text-amber-400 font-bold">{{ $sub->klasifikasi }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Input (Kanan) --}}
            <div class="col-span-12 md:col-span-8 p-10 md:p-14">
                <form action="{{ route('rka.store_header') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="sub_activity_id" value="{{ $sub->id }}">

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Sumber Dana</label>
                        <select name="sumber_dana" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all">
                            <option value="PENDAPATAN ASLI DAERAH (PAD)">PENDAPATAN ASLI DAERAH (PAD)</option>
                            <option value="DANA TRANSFER UMUM (DAU)">DANA TRANSFER UMUM (DAU)</option>
                            <option value="DANA ALOKASI KHUSUS (DAK)">DANA ALOKASI KHUSUS (DAK)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Waktu Pelaksanaan</label>
                            <input type="text" name="waktu_pelaksanaan" value="Januari s.d Desember" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kelompok Sasaran</label>
                            <input type="text" name="kelompok_sasaran" placeholder="e.g. Perangkat Daerah" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Lokasi Kegiatan</label>
                        <textarea name="lokasi_kegiatan" rows="2" placeholder="Contoh: Kota Pontianak, Kec. Pontianak Tenggara" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama PPTK</label>
                            <input type="text" name="nama_pptk" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">NIP PPTK</label>
                            <input type="text" name="nip_pptk" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-sm font-bold text-white focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="pt-6">
<form action="{{ route('rka.store_header') }}" method="POST">
    @csrf
    <input type="hidden" name="sub_activity_id" value="{{ $sub->id }}">
    
    <button type="submit">Lanjut ke Rincian <i class="fas fa-arrow-right"></i></button>
</form>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection