@extends('dashboard.layout')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* --- STYLE DASAR --- */
    .dashboard-card { 
        background: #151E2E; 
        border: 1px solid #293548; 
        border-radius: 16px; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .project-row {
        background: #151E2E;
        border: 1px solid #293548;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .project-row:hover {
        border-color: #6366F1;
        background: #1A2436;
        transform: translateY(-2px);
    }
    
    .row-bottleneck { border-left: 4px solid #F43F5E !important; background: rgba(244, 63, 94, 0.05) !important; }
    .badge-bottleneck { background: rgba(244, 63, 94, 0.15); color: #F43F5E; border: 1px solid rgba(244, 63, 94, 0.3); }

    /* --- WIZARD ICONS --- */
    .step-icon { 
        width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px;
        background: #0F172A; border: 1px solid #334155; color: #475569; /* Default Abu */
        transition: all 0.3s; position: relative; z-index: 10;
    }
    
    /* 1. KINERJA (Biru Tua/Indigo) */
    .step-icon.step-indigo.active { background: rgba(99, 102, 241, 0.15); color: #818CF8; border-color: #6366F1; box-shadow: 0 0 10px rgba(99, 102, 241, 0.3); }
    
    /* 2. RKA (Kuning/Amber) */
    .step-icon.step-amber.active { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border-color: #F59E0B; box-shadow: 0 0 10px rgba(245, 158, 11, 0.3); }
    
    /* 3. KAK (Biru Muda/Cyan) */
    .step-icon.step-cyan.active { background: rgba(34, 211, 238, 0.15); color: #22D3EE; border-color: #06B6D4; box-shadow: 0 0 10px rgba(34, 211, 238, 0.3); }
    
    /* 4. PENGADAAN BERJALAN (Hijau/Emerald) */
    .step-icon.step-emerald.active { background: rgba(16, 185, 129, 0.15); color: #34D399; border-color: #10B981; box-shadow: 0 0 10px rgba(16, 185, 129, 0.3); }
    
    /* 5. PENGADAAN SELESAI/LENGKAP (GANTI JADI UNGU/VIOLET AGAR BEDA DARI KUNING) */
    .step-icon.step-finish.active { 
        background: rgba(168, 85, 247, 0.15); /* Purple-500 Opacity */
        color: #C084FC; /* Purple-400 */
        border-color: #A855F7; /* Purple-500 */
        box-shadow: 0 0 15px rgba(168, 85, 247, 0.6); /* Glow Ungu */
    }

    /* Bottleneck (Merah Kedip) */
    .step-bottleneck { background: rgba(244, 63, 94, 0.15) !important; color: #F43F5E !important; border-color: #F43F5E !important; animation: pulse-red 2s infinite; }

    .step-connector { flex: 1; height: 2px; background: #1E293B; margin: 0 4px; }
    .step-connector.active { background: #6366F1; box-shadow: 0 0 5px rgba(99, 102, 241, 0.5); }

    @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(244, 63, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(244, 63, 94, 0); } }
</style>

<div x-data="pimpinanDashboard">    
    
    {{-- HEADER --}}
    <div class="dashboard-card p-6 mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                <i class="fas fa-chart-pie text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">EXECUTIVE DASHBOARD</h2>
                <p class="text-slate-400 text-sm font-medium">Monitoring Keterlaksanaan & Validasi Kinerja</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('executive.sync') }}" class="px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase transition flex items-center gap-2 shadow-lg shadow-indigo-500/30">
                <i class="fas fa-sync-alt"></i> Update Data
            </a>
        </div>
    </div>

    {{-- GRAFIK DONUT --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div class="dashboard-card p-6 relative overflow-hidden group hover:border-indigo-500/30 transition">
            <h3 class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-4">Status Fisik Kegiatan (Unit)</h3>
            <div class="flex items-center gap-6">
                <div id="chartKegiatan" class="w-40 relative z-10"></div>
                <div class="space-y-3 flex-1 relative z-10">
                    <div class="flex justify-between text-sm items-center border-b border-slate-700/50 pb-2">
                        <span class="text-emerald-400 flex items-center gap-2"><i class="fas fa-circle text-[8px]"></i>Selesai</span> 
                        <span class="text-white font-bold font-mono">{{ $completedItems }}</span>
                    </div>
                    <div class="flex justify-between text-sm items-center border-b border-slate-700/50 pb-2">
                        <span class="text-blue-400 flex items-center gap-2"><i class="fas fa-circle text-[8px]"></i>Berjalan</span> 
                        <span class="text-white font-bold font-mono">{{ $partialItems }}</span>
                    </div>
                    <div class="flex justify-between text-sm items-center">
                        <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-circle text-[8px]"></i>Belum</span> 
                        <span class="text-white font-bold font-mono">{{ $pendingItems }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card p-6 relative overflow-hidden group hover:border-indigo-500/30 transition">
            <h3 class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-4">Proporsi Serapan Anggaran</h3>
            <div class="flex items-center gap-6">
                <div id="chartKeuangan" class="w-40 relative z-10"></div>
                <div class="space-y-3 flex-1 relative z-10">
                    <div class="flex justify-between text-sm items-center border-b border-slate-700/50 pb-2">
                        <span class="text-indigo-400 flex items-center gap-2"><i class="fas fa-circle text-[8px]"></i>Realisasi</span> 
                        <span class="text-white font-bold font-mono">Rp {{ number_format($totalRealisasi/1000000000, 2) }} M</span>
                    </div>
                    <div class="flex justify-between text-sm items-center">
                        <span class="text-slate-600 flex items-center gap-2"><i class="fas fa-circle text-[8px]"></i>Sisa Pagu</span> 
                        <span class="text-white font-bold font-mono">Rp {{ number_format(($totalPagu - $totalRealisasi)/1000000000, 2) }} M</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LIST MONITORING --}}
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-white font-bold text-lg border-l-4 border-indigo-500 pl-3">Monitoring Keterlaksanaan</h3>
            <p class="text-slate-500 text-xs ml-4 mt-1">Alur: Kinerja &rarr; RKA &rarr; KAK &rarr; Pengadaan</p>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($progressKegiatan as $item)
        <div class="project-row p-5 flex flex-col md:flex-row items-center gap-6 {{ $item->is_bottleneck ? 'row-bottleneck' : '' }}">
            
            {{-- Status Icon Utama --}}
            <div class="w-12 h-12 rounded-xl flex shrink-0 items-center justify-center {{ $item->is_bottleneck ? 'bg-rose-500/20 text-rose-500' : ($item->status_pengadaan == 'lengkap' ? 'bg-purple-500/20 text-purple-400' : ($item->status_pengadaan == 'sebagian' ? 'bg-emerald-500/20 text-emerald-500' : 'bg-slate-700 text-slate-500')) }}">
                <i class="fas fa-{{ $item->is_bottleneck ? 'triangle-exclamation' : ($item->status_pengadaan == 'lengkap' ? 'check-double' : 'spinner') }} text-xl"></i>
            </div>

            {{-- Detail Text --}}
            <div class="flex-1 min-w-0 text-center md:text-left">
                <div class="flex items-center gap-2 justify-center md:justify-start mb-1">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $item->klasifikasi == 'IKU' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-slate-700 text-slate-400' }}">{{ $item->klasifikasi }}</span>
                    <h4 class="text-white font-bold text-sm truncate">{{ $item->nama_sub_kegiatan }}</h4>
                </div>
                
                @if($item->is_bottleneck)
                    <div class="inline-flex items-center gap-2 px-2 py-1 rounded badge-bottleneck mt-1">
                        <span class="text-[10px] font-bold uppercase tracking-wide">BOTTLENECK</span>
                        <span class="text-[10px] opacity-80">Anggaran Ada, Pengadaan Belum</span>
                    </div>
                @else
                    <button type="button" @click="getDetails({{ $item->sub_activity_id }}, {{ json_encode($item->nama_sub_kegiatan) }})" 
                        class="text-xs text-slate-400 hover:text-indigo-400 flex items-center gap-1 mt-1 transition-colors cursor-pointer group justify-center md:justify-start">
                        <i class="fas fa-search-plus text-[10px] group-hover:text-indigo-400"></i>
                        <span class="group-hover:underline">Lihat Rincian Kontrak & RKA</span>
                    </button>
                @endif
            </div>

            {{-- WIZARD STEPS (DIPERBAIKI WARNANYA) --}}
            <div class="flex items-center w-full md:w-auto md:justify-end gap-1 px-4 mt-4 md:mt-0">
                
                {{-- 1. KINERJA (Biru/Indigo) --}}
                <div class="flex flex-col items-center gap-1 group" title="Perencanaan Kinerja">
                    <div class="step-icon {{ $item->status_kinerja == 'approved' ? 'step-indigo active' : '' }}">
                        <i class="fas fa-sitemap"></i>
                    </div>
                </div>
                <div class="step-connector {{ $item->pagu_rka > 0 ? 'active' : '' }}"></div>

                {{-- 2. RKA (Kuning/Amber) --}}
                <div class="flex flex-col items-center gap-1 group" title="Anggaran (RKA)">
                    <div class="step-icon {{ $item->pagu_rka > 0 ? 'step-amber active' : '' }}">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
                <div class="step-connector {{ $item->has_kak ? 'active' : '' }}"></div>

                {{-- 3. KAK (Biru Muda/Cyan) --}}
                <div class="flex flex-col items-center gap-1 group" title="Kerangka Acuan Kerja (KAK)">
                    <div class="step-icon {{ $item->has_kak ? 'step-cyan active' : '' }}">
                        <i class="fas fa-file-contract"></i>
                    </div>
                </div>
                <div class="step-connector {{ $item->status_pengadaan != 'belum' ? 'active' : '' }}"></div>

                {{-- 4. PENGADAAN (UNGU = SELESAI, HIJAU = PROSES, MERAH = MACET) --}}
                @php
                    $pengadaanClass = ''; 
                    if ($item->is_bottleneck) {
                        $pengadaanClass = 'step-bottleneck'; 
                    } elseif ($item->status_pengadaan == 'lengkap') {
                        $pengadaanClass = 'step-finish active'; // UNGU (Ganti dari Gold)
                    } elseif ($item->status_pengadaan == 'sebagian' || $item->status_pengadaan != 'belum') {
                        $pengadaanClass = 'step-emerald active'; // Hijau
                    }
                @endphp
                <div class="flex flex-col items-center gap-1 group" title="Pengadaan">
                    <div class="step-icon {{ $pengadaanClass }}">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>

            {{-- TOMBOL VALIDASI --}}
            <div class="pl-6 border-l border-slate-700/50 flex gap-3">
                 <button @click="activeSubId = {{ $item->sub_activity_id }}; title = {{ json_encode($item->nama_sub_kegiatan) }}; openDirective = true" 
                    class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-indigo-600 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-2 border border-slate-600 hover:border-indigo-500">
                    <i class="fas fa-pen-to-square"></i> Validasi
                </button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- MODAL DETAIL --}}
    <template x-teleport="body">
        <div x-show="openDetail" class="fixed inset-0 z-[9999] flex items-center justify-center p-6" x-cloak>
            <div @click="openDetail = false" class="fixed inset-0 bg-black/80 backdrop-blur-sm" style="z-index: -1;"></div>
            <div class="dashboard-card w-full max-w-5xl p-8 relative shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto border border-indigo-500/30">
                <div class="flex justify-between items-start mb-8 border-b border-slate-700 pb-4">
                    <div>
                        <h3 class="text-white font-black uppercase text-xl" x-text="title"></h3>
                        <p class="text-indigo-400 text-xs mt-1 font-bold italic uppercase tracking-wider">Intelligence Trace</p>
                    </div>
                    <button @click="openDetail = false" class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 hover:text-white transition"><i class="fas fa-times"></i></button>
                </div>
                
                {{-- Context --}}
                <template x-if="selectedItems.context">
                    <div class="mb-8 p-6 rounded-2xl bg-indigo-500/5 border border-indigo-500/10 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-sitemap text-6xl text-indigo-400"></i></div>
                        <div class="relative z-10">
                            <span class="px-2 py-1 rounded bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest mb-3 inline-block" x-text="selectedItems.context.klasifikasi"></span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-2">
                                <div><p class="text-[10px] text-slate-500 uppercase font-black mb-1">Misi</p><p class="text-sm text-indigo-100 font-medium" x-text="selectedItems.context.misi"></p></div>
                                <div><p class="text-[10px] text-slate-500 uppercase font-black mb-1">Visi</p><p class="text-sm text-indigo-100 font-medium" x-text="selectedItems.context.visi"></p></div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Tabel Kontrak --}}
                <div x-show="selectedItems.contracted.length > 0" class="mb-6">
                    <h4 class="text-emerald-400 font-bold uppercase text-xs mb-3 flex items-center gap-2">
                        <i class="fas fa-check-double"></i> Realisasi Pengadaan (Terkontrak)
                    </h4>
                    <div class="rounded-xl border border-slate-700 overflow-hidden">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-800 text-slate-400 text-xs uppercase font-bold">
                                <tr><th class="px-6 py-3">Uraian</th><th class="px-6 py-3 text-center">Vol</th><th class="px-6 py-3 text-right">Nilai RKA</th><th class="px-6 py-3 text-right">Nilai Kontrak</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700 bg-slate-900/50">
                                <template x-for="item in selectedItems.contracted">
                                    <tr class="hover:bg-slate-800/30">
                                        <td class="py-4 px-6 text-white" x-text="item.nama"></td>
                                        <td class="py-4 text-center" x-text="item.volume"></td>
                                        <td class="py-4 text-right font-mono text-slate-400" x-text="'Rp '+item.harga_rka"></td>
                                        <td class="py-4 text-right text-emerald-400 font-mono font-bold" x-text="'Rp '+item.total_realisasi"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabel Sisa RKA --}}
                <div x-show="selectedItems.pending.length > 0" class="mb-4">
                    <h4 class="text-amber-400 font-bold uppercase text-xs mb-3 flex items-center gap-2 mt-4">
                        <i class="fas fa-clock"></i> Masih Dalam RKA (Belum Pengadaan)
                    </h4>
                    <div class="rounded-xl border border-slate-700 overflow-hidden opacity-90">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-800 text-slate-400 text-xs uppercase font-bold">
                                <tr><th class="px-6 py-3">Uraian Rencana</th><th class="px-6 py-3 text-center">Vol</th><th class="px-6 py-3 text-right">Estimasi Biaya</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700 bg-slate-900/50">
                                <template x-for="item in selectedItems.pending">
                                    <tr class="hover:bg-slate-800/30">
                                        <td class="py-4 px-6 text-slate-400" x-text="item.nama"></td>
                                        <td class="py-4 text-center" x-text="item.volume"></td>
                                        <td class="py-4 text-right font-mono text-amber-400" x-text="'Rp '+item.total_rencana"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="loading" class="py-10 text-center"><i class="fas fa-circle-notch fa-spin text-3xl text-indigo-500"></i><p class="text-indigo-400 text-xs font-bold mt-2 animate-pulse">Mengambil data...</p></div>
                <div x-show="selectedItems.contracted.length === 0 && selectedItems.pending.length === 0 && !loading" class="py-10 text-center text-slate-500"><p class="text-sm">Tidak ada rincian data tersedia.</p></div>
            </div>
        </div>
    </template>

    {{-- MODAL INSTRUKSI --}}
    <template x-teleport="body">
        <div x-show="openDirective" class="fixed inset-0 z-[10000] flex items-center justify-center p-6" x-cloak>
            <div @click="openDirective = false" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="dashboard-card w-full max-w-lg p-8 relative z-10 shadow-2xl bg-[#151E2E]">
                <h3 class="text-white font-bold text-lg mb-1">Catatan Validasi Pimpinan</h3>
                <p class="text-indigo-400 text-xs font-mono mb-4" x-text="title"></p>
                <p class="text-slate-400 text-xs mb-2">Instruksi ini akan masuk ke notifikasi sistem modul terkait.</p>
                
                <textarea x-model="directiveText" class="w-full h-32 bg-slate-900 border border-slate-700 rounded-xl p-4 text-white text-sm focus:border-indigo-500 outline-none transition" placeholder="Tulis arahan revisi atau persetujuan..."></textarea>
                
                <div class="flex gap-3 mt-6 justify-end">
                    <button @click="openDirective = false" class="px-4 py-2 rounded-lg text-slate-400 text-sm hover:text-white">Batal</button>
                    <button @click="sendSystemInstruction()" class="px-6 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim ke Sistem
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options1 = {
            series: [{{ $completedItems }}, {{ $partialItems }}, {{ $pendingItems }}],
            labels: ['Selesai', 'Berjalan', 'Belum'],
            chart: { type: 'donut', height: 160, background: 'transparent' },
            colors: ['#10B981', '#3B82F6', '#64748B'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: { pie: { donut: { size: '70%' } } },
            stroke: { show: false },
            tooltip: { theme: 'dark' }
        };
        new ApexCharts(document.querySelector("#chartKegiatan"), options1).render();

        var options2 = {
            series: [{{ $persenSerapan }}, {{ $persenSisa }}],
            labels: ['Realisasi', 'Sisa'],
            chart: { type: 'donut', height: 160, background: 'transparent' },
            colors: ['#6366F1', '#334155'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: { pie: { donut: { size: '70%' } } },
            stroke: { show: false },
            tooltip: { theme: 'dark' }
        };
        new ApexCharts(document.querySelector("#chartKeuangan"), options2).render();
    });

    document.addEventListener('alpine:init', () => {
        Alpine.data('pimpinanDashboard', () => ({
            openDetail: false, openDirective: false, loading: false, 
            title: '', activeSubId: null, directiveText: '',
            selectedItems: { context: null, contracted: [], pending: [] },

            async getDetails(id, nama) {
                this.title = nama; this.openDetail = true; this.loading = true; this.selectedItems = { context: null, contracted: [], pending: [] };
                try {
                    const url = "{{ route('executive.details', ['id' => ':id']) }}".replace(':id', id);
                    const response = await fetch(url);
                    this.selectedItems = await response.json();
                } catch (e) { alert('Gagal memuat data'); } finally { this.loading = false; }
            },

            async sendSystemInstruction() {
                if (!this.directiveText) return alert('Isi catatan validasi!');
                try {
                    const response = await fetch('{{ route("executive.note.store") }}', { 
                        method: 'POST', 
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, 
                        body: JSON.stringify({ sub_activity_id: this.activeSubId, instruction: this.directiveText }) 
                    });
                    const data = await response.json();
                    if(data.success) {
                        alert('Validasi Terkirim! Modul terkait telah menerima notifikasi revisi.');
                        this.openDirective = false; this.directiveText = '';
                    }
                } catch (e) { alert('Gagal mengirim ke sistem'); }
            }
        }));
    });
</script>
@endsection