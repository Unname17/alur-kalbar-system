@extends('kak.layout')

@section('title', 'Daftar Kegiatan')
@section('header_title', 'Pilih Kegiatan RKA')

@section('content')
<div class="max-w-6xl mx-auto">
    
    {{-- Search / Filter (Visual Saja) --}}
    <div class="flex gap-4 mb-8">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
            <input type="text" placeholder="Cari nama kegiatan..." class="w-full bg-slate-900/50 border border-slate-700 rounded-2xl pl-12 pr-4 py-4 text-sm text-white focus:border-amber-500 outline-none transition-all">
        </div>
        <div class="px-6 py-4 bg-slate-900/50 border border-slate-700 rounded-2xl text-slate-400 text-sm font-bold">
            Tahun 2025
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        @forelse($rkas as $rka)
        <div class="group glass-kak p-6 rounded-[2rem] hover:bg-slate-800/50 transition-all border border-slate-800 hover:border-amber-500/30 relative overflow-hidden">
            
            {{-- Background Glow saat Hover --}}
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all"></div>

            <div class="relative z-10 flex flex-col md:flex-row gap-6 items-center">
                
                {{-- Icon Status --}}
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-black shadow-lg
                    {{ $rka->kak ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-600 border border-slate-700' }}">
                    <i class="fas {{ $rka->kak ? 'fa-check-double' : 'fa-file-signature' }}"></i>
                </div>

                <div class="flex-1 text-center md:text-left">
                    <div class="text-[10px] font-bold text-amber-500 uppercase tracking-widest mb-1">
                        {{ $rka->subActivity->kode_sub ?? 'NO-CODE' }}
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-amber-400 transition-colors">
                        {{ $rka->subActivity->nama_sub }}
                    </h3>
                    <div class="flex flex-wrap gap-4 justify-center md:justify-start text-xs text-slate-400">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-coins text-slate-600"></i>
                            Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-slate-600"></i>
                            {{ Str::limit($rka->lokasi_kegiatan, 30) }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-2 min-w-[140px]">
                    @if($rka->kak)
                        <a href="{{ route('kak.manage', $rka->id) }}" class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold text-center transition-all border border-slate-700">
                            <i class="fas fa-edit mr-1"></i> Edit KAK
                        </a>
                        <a href="{{ route('kak.print', $rka->id) }}" target="_blank" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold text-center transition-all shadow-lg shadow-emerald-900/20">
                            <i class="fas fa-print mr-1"></i> Cetak PDF
                        </a>
                    @else
                        <a href="{{ route('kak.manage', $rka->id) }}" class="px-5 py-4 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold text-center transition-all shadow-lg shadow-amber-900/20 animate-pulse">
                            <i class="fas fa-plus mr-1"></i> Buat KAK
                        </a>
                    @endif
                </div>

            </div>
        </div>
        @empty
        <div class="text-center py-20">
            <div class="inline-block p-6 rounded-full bg-slate-900/50 mb-4">
                <i class="fas fa-inbox text-4xl text-slate-600"></i>
            </div>
            <p class="text-slate-500 font-bold">Belum ada RKA yang difinalisasi.</p>
            <p class="text-xs text-slate-600 mt-2">Silakan selesaikan input anggaran di modul RKA terlebih dahulu.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection