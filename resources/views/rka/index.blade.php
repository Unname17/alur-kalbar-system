@extends('rka.layout')

{{-- Judul Halaman Dinamis --}}
@section('title')
    @if($mode == 'spk') Hasil Analisis Prioritas
    @elseif($mode == 'rekap') Rekapitulasi Anggaran
    @else Data Kegiatan Mentah
    @endif
@endsection

@section('header_title')
    @if($mode == 'spk') Leaderboard Prioritas (SPK)
    @elseif($mode == 'rekap') Laporan Rekapitulasi
    @else Manajemen Data RKA
    @endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8 relative z-10">

    {{-- NAVIGATION TABS --}}
    <div class="flex flex-wrap gap-4 border-b border-slate-700 pb-1">
        <a href="{{ route('rka.dashboard', ['mode' => 'list']) }}" 
           class="px-6 py-3 rounded-t-xl font-bold text-sm transition-all {{ $mode == 'list' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
           <i class="fas fa-database mr-2"></i> Data Kegiatan
        </a>
        <a href="{{ route('rka.dashboard', ['mode' => 'spk']) }}" 
           class="px-6 py-3 rounded-t-xl font-bold text-sm transition-all {{ $mode == 'spk' ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
           <i class="fas fa-bolt mr-2"></i> Analisis Prioritas
        </a>
        <a href="{{ route('rka.dashboard', ['mode' => 'rekap']) }}" 
           class="px-6 py-3 rounded-t-xl font-bold text-sm transition-all {{ $mode == 'rekap' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
           <i class="fas fa-chart-pie mr-2"></i> Rekap Anggaran
        </a>
    </div>

    {{-- KONTEN: MODE REKAPITULASI --}}
    @if($mode == 'rekap')
        <div class="glass-card p-8 rounded-[2rem] border border-slate-700">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h2 class="text-2xl font-black text-white">Laporan Realisasi Anggaran</h2>
                    <p class="text-slate-400 text-sm mt-1">Total seluruh usulan anggaran berdasarkan level.</p>
                </div>
                
                {{-- Filter Level --}}
                <form action="{{ route('rka.dashboard') }}" method="GET" class="flex gap-2 bg-slate-900 p-1 rounded-xl border border-slate-700">
                    <input type="hidden" name="mode" value="rekap">
                    <button type="submit" name="level" value="program" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $level == 'program' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white' }}">Program</button>
                    <button type="submit" name="level" value="kegiatan" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $level == 'kegiatan' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white' }}">Kegiatan</button>
                </form>
            </div>

            {{-- Grand Total Card --}}
            <div class="mb-8 p-6 bg-gradient-to-r from-emerald-900/50 to-teal-900/50 rounded-2xl border border-emerald-500/30 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center text-black font-bold text-xl">Rp</div>
                    <div>
                        <div class="text-xs text-emerald-300 uppercase font-bold tracking-wider">Grand Total Usulan</div>
                        <div class="text-3xl font-mono font-bold text-white tracking-tight">{{ number_format($grandTotal, 0, ',', '.') }}</div>
                    </div>
                </div>
                {{-- Tahun Anggaran Dinamis (Optional: bisa ambil dari controller jika dikirim) --}}
                <div class="text-right hidden md:block">
                    <div class="text-xs text-slate-400">Status</div>
                    <div class="text-lg font-bold text-white">Aktif</div>
                </div>
            </div>

            {{-- Tabel Rekap --}}
            <div class="overflow-hidden rounded-xl border border-slate-700">
                <table class="w-full text-left text-sm text-slate-400">
                    <thead class="bg-slate-900 text-slate-200 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Nama Uraian ({{ ucfirst(str_replace('_', ' ', $level)) }})</th>
                            <th class="px-6 py-4 text-right">Total Anggaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-800/30">
                        @foreach($rekapData as $item)
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-indigo-400">
                                @if($level == 'program') {{ $item->kode_program }}
                                @else($level == 'kegiatan') {{ $item->kode_kegiatan }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">
                                    @if($level == 'program') {{ $item->nama_program }}
                                    @else($level == 'kegiatan') {{ $item->nama_kegiatan }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-white">
                                Rp {{ number_format($item->total_anggaran, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    {{-- KONTEN: MODE LIST & SPK --}}
    @else
        
        {{-- Hero Section --}}
        <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden text-center border-2 {{ $mode == 'spk' ? 'border-amber-500/20' : 'border-slate-700 border-dashed' }} mb-8">
            @if($mode == 'list')
                <div class="relative z-10 py-4">
                    <h1 class="text-3xl font-black text-white mb-2">Data Kegiatan Siap Dianalisis</h1>
                    <p class="text-slate-400 max-w-lg mx-auto mb-6">
                        Terdapat <strong class="text-white">{{ $rankedItems->count() }} kegiatan</strong>. Klik tab "Analisis Prioritas" untuk melihat perangkingan otomatis.
                    </p>
                </div>
            @else
                <div class="relative z-10">
                    <h1 class="text-3xl font-black text-white">Hasil Perangkingan SPK</h1>
                    <p class="text-slate-400 text-sm mt-2">Diurutkan berdasarkan skor urgensi (IKU/IKD) & Pertumbuhan Target 5 Tahun.</p>
                </div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            @endif
        </div>

        {{-- Looping List Data --}}
        <div class="space-y-4">
            @forelse($rankedItems as $index => $item)
            @php
                if ($mode == 'spk') {
                    $rankBadge = match($index) {
                        0 => 'bg-amber-500 text-black shadow-amber-500/50',
                        1 => 'bg-slate-300 text-slate-900',
                        2 => 'bg-orange-700 text-orange-100',
                        default => 'bg-slate-800 text-slate-500 border border-slate-700'
                    };
                    $glowClass = $index == 0 ? 'border-amber-500/50 shadow-[0_0_40px_-10px_rgba(245,158,11,0.2)] bg-slate-900/80' : 'border-slate-800 bg-slate-900/50';
                    $rankNum = "#" . ($index + 1);
                } else {
                    $rankBadge = 'bg-slate-800 text-slate-600 border border-slate-700';
                    $glowClass = 'border-slate-800 bg-slate-900/30 opacity-80 hover:opacity-100';
                    $rankNum = "-";
                }
            @endphp

            <div class="glass-card p-5 rounded-[2rem] border {{ $glowClass }} transition-all group relative flex flex-col md:flex-row items-center gap-6">
                
                <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center font-black text-xl shadow-lg {{ $rankBadge }} z-10 transition-all">
                    {{ $rankNum }}
                </div>

                <div class="flex-1 min-w-0 text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-3 mb-2">
                        <span class="text-[10px] text-slate-500 font-mono">{{ $item->kode_sub }}</span>
                        @if($item->klasifikasi == 'IKU')
                            <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 text-[9px] font-bold">IKU</span>
                        @elseif($item->klasifikasi == 'IKD')
                            <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 text-[9px] font-bold">IKD</span>
                        @endif
                    </div>
                    <h4 class="text-lg font-bold text-white group-hover:text-indigo-400 transition-colors truncate">{{ $item->nama_sub }}</h4>
                    
                    {{-- UPDATE: Visualisasi Pertumbuhan 5 Tahun --}}
                    @if($mode == 'spk')
                    <div class="mt-3 flex items-center gap-4 text-xs font-mono text-slate-400">
                        <span class="text-[10px] uppercase font-bold tracking-wider">Pertumbuhan (5 Th):</span>
                        <div class="w-24 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            {{-- Visualisasi Persentase Pertumbuhan --}}
                            <div class="h-full bg-gradient-to-r from-blue-500 to-emerald-400" 
                                 style="width: {{ min(max($item->gap_persen, 5), 100) }}%">
                            </div>
                        </div>
                        <span class="{{ $item->gap_persen > 0 ? 'text-emerald-400' : 'text-slate-500' }}">
                            {{ $item->gap_persen > 0 ? '+' : '' }}{{ $item->gap_persen }}%
                        </span>
                    </div>
                    @endif
                </div>

                @if($mode == 'spk')
                <div class="text-center px-6 border-l border-slate-800 hidden md:block">
                    <div class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Score</div>
                    <div class="text-2xl font-black text-white">{{ number_format($item->spk_score, 1) }}</div>
                </div>
                @endif

                <div class="flex-shrink-0 w-full md:w-auto">
                    @if($item->has_rka)
                         <a href="{{ route('rka.manage', $item->rka_id) }}" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 rounded-xl font-bold text-xs hover:bg-emerald-500 hover:text-black transition-all">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @else
                        <a href="{{ route('rka.create', $item->id) }}" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-slate-700 text-slate-300 rounded-xl font-bold text-xs hover:bg-indigo-500 hover:text-white transition-all">
                            <i class="fas fa-plus-circle"></i> Buat RKA
                        </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-slate-500">Belum ada data kegiatan yang disetujui.</div>
            @endforelse
        </div>
    @endif

</div>
@endsection