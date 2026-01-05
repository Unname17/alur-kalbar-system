@extends('rka.layout')

@section('title', $isSpkMode ? 'Hasil Analisis Prioritas' : 'Data Mentah Kegiatan')
@section('header_title', $isSpkMode ? 'Leaderboard Prioritas' : 'Data Kegiatan (Approved)')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 relative z-10">

    {{-- HERO SECTION: TOMBOL ANALISIS --}}
    <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden text-center border-2 {{ $isSpkMode ? 'border-indigo-500/20' : 'border-slate-700 border-dashed' }}">
        
        @if(!$isSpkMode)
            {{-- TAMPILAN SEBELUM ANALISIS --}}
            <div class="relative z-10 py-4">
                <div class="w-20 h-20 bg-slate-800 rounded-3xl mx-auto flex items-center justify-center mb-6 shadow-xl border border-slate-700 animate-bounce">
                    <i class="fas fa-microchip text-4xl text-indigo-400"></i>
                </div>
                <h1 class="text-3xl font-black text-white mb-2">Data Kegiatan Siap Dianalisis</h1>
                <p class="text-slate-400 max-w-lg mx-auto mb-8">
                    Terdapat <strong class="text-white">{{ $rankedItems->count() }} kegiatan</strong> yang telah disetujui Bappeda. Jalankan algoritma SPK untuk menentukan urutan prioritas penganggaran secara otomatis.
                </p>
                
                <a href="{{ route('rka.dashboard', ['mode' => 'spk']) }}" class="inline-flex items-center gap-3 px-8 py-5 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[2px] text-xs hover:bg-indigo-500 hover:scale-105 hover:shadow-2xl hover:shadow-indigo-500/40 transition-all">
                    <i class="fas fa-bolt text-amber-300"></i> Jalankan Analisis Prioritas
                </a>
            </div>
        @else
            {{-- TAMPILAN SETELAH ANALISIS (Header Hasil) --}}
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-left">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-lg text-[10px] font-black uppercase tracking-widest">
                            <i class="fas fa-check-circle me-1"></i> Analysis Complete
                        </span>
                    </div>
                    <h1 class="text-3xl font-black text-white">Hasil Perangkingan SPK</h1>
                    <p class="text-slate-400 text-sm mt-2">Data diurutkan berdasarkan urgensi IKU/IKD & Gap Kinerja.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rka.dashboard') }}" class="px-6 py-3 bg-slate-800 text-slate-400 rounded-xl font-bold text-xs uppercase hover:bg-slate-700 border border-slate-700">
                        <i class="fas fa-undo me-2"></i> Reset
                    </a>
                </div>
            </div>
            {{-- Background Decoration --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        @endif
    </div>

    {{-- LIST DATA --}}
    <div>
        @if($isSpkMode)
            <div class="flex items-center justify-between mb-6 animate-fade-in-up">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-sm shadow-lg shadow-indigo-500/50"><i class="fas fa-list-ol"></i></span>
                    Leaderboard Prioritas
                </h3>
            </div>
        @else
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-slate-400 flex items-center gap-2">
                    <i class="fas fa-database"></i> Data Mentah (Gelondongan)
                </h3>
            </div>
        @endif

        <div class="space-y-4">
            @forelse($rankedItems as $index => $item)
            @php
                // Logika Tampilan Berbeda Antara Mode SPK vs Raw
                
                if ($isSpkMode) {
                    // TAMPILAN MODE PRIORITAS (Ada Warna, Ada Rank #1, #2)
                    $rankBadge = match($index) {
                        0 => 'bg-amber-500 text-black shadow-amber-500/50',
                        1 => 'bg-slate-300 text-slate-900',
                        2 => 'bg-orange-700 text-orange-100',
                        default => 'bg-slate-800 text-slate-500 border border-slate-700'
                    };
                    $glowClass = $index == 0 ? 'border-amber-500/50 shadow-[0_0_40px_-10px_rgba(245,158,11,0.2)] bg-slate-900/80' : 'border-slate-800 bg-slate-900/50';
                    $rankNum = "#" . ($index + 1);
                } else {
                    // TAMPILAN MODE RAW (Datar, Abu-abu, Tidak ada Rank)
                    $rankBadge = 'bg-slate-800 text-slate-600 border border-slate-700';
                    $glowClass = 'border-slate-800 bg-slate-900/30 opacity-80 hover:opacity-100';
                    $rankNum = "-";
                }
            @endphp

            <div class="glass-card p-5 rounded-[2rem] border {{ $glowClass }} transition-all group relative flex flex-col md:flex-row items-center gap-6">
                
                {{-- Rank Number --}}
                <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center font-black text-xl shadow-lg {{ $rankBadge }} z-10 transition-all">
                    {{ $rankNum }}
                </div>

                {{-- Content Info --}}
                <div class="flex-1 min-w-0 text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-3 mb-2">
                        @if($item->klasifikasi == 'IKU')
                            <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 border border-rose-500/30 text-[9px] font-black uppercase tracking-widest">IKU</span>
                        @elseif($item->klasifikasi == 'IKD')
                            <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 text-[9px] font-black uppercase tracking-widest">IKD</span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-slate-500/20 text-slate-400 border border-slate-500/30 text-[9px] font-black uppercase tracking-widest">IKK</span>
                        @endif
                        <span class="text-[10px] text-slate-500 font-mono">{{ $item->kode_sub }}</span>
                    </div>

                    <h4 class="text-lg font-bold text-white group-hover:text-indigo-400 transition-colors truncate">{{ $item->nama_sub }}</h4>
                    
                    {{-- Progress Bar hanya muncul menonjol saat Mode SPK --}}
                    @if($isSpkMode)
                    <div class="mt-3 flex items-center justify-center md:justify-start gap-4 text-xs font-mono text-slate-400">
                        <span>Gap Kinerja:</span>
                        <div class="w-32 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500" style="width: {{ min($item->gap_persen, 100) }}%"></div>
                        </div>
                        <span class="text-emerald-400">+{{ $item->gap_value }} ({{ $item->gap_persen }}%)</span>
                    </div>
                    @else
                    <div class="mt-1 text-xs text-slate-500">
                         Target: {{ (float)$item->target_2025 }} {{ $item->satuan }}
                    </div>
                    @endif
                </div>

                {{-- Score Display (Hanya Muncul di Mode SPK) --}}
                @if($isSpkMode)
                <div class="text-center px-6 border-l border-slate-800 hidden md:block">
                    <div class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Score</div>
                    <div class="text-2xl font-black text-white text-glow">{{ number_format($item->spk_score, 1) }}</div>
                </div>
                @endif

                {{-- Action Button --}}
                <div class="flex-shrink-0 w-full md:w-auto">
                    @if($item->has_rka)
                         <a href="{{ route('rka.manage', $item->rka_id) }}" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 rounded-xl font-bold text-xs hover:bg-emerald-500 hover:text-black transition-all">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @else
                        <a href="{{ route('rka.create', $item->id) }}" class="flex items-center justify-center gap-2 w-full px-6 py-3 {{ $isSpkMode ? 'bg-indigo-600 text-white' : 'bg-slate-700 text-slate-300' }} rounded-xl font-bold text-xs hover:bg-indigo-500 hover:text-white transition-all">
                            <i class="fas fa-plus-circle"></i> RKA
                        </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-20 bg-slate-800/50 rounded-[3rem] border-2 border-dashed border-slate-700">
                <i class="fas fa-box-open text-4xl text-slate-600 mb-4"></i>
                <h3 class="text-slate-400 font-bold">Belum ada kegiatan Approved dari Modul Kinerja</h3>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection