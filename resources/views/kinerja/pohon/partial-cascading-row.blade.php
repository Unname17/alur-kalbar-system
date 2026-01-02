@php
    $padding = $level * 30; // Indentasi per level
    $childData = null; $nextType = ''; $label = ''; $color = '';
    
    // Konfigurasi Hirarki Berdasarkan Skema Database Terbaru (No Jumping)
    if($type == 'visi') { 
        $childData = $node->missions; $nextType = 'misi'; $label = 'VISI'; $color = 'bg-slate-900 text-white'; 
    } elseif($type == 'misi') { 
        $childData = $node->goals; $nextType = 'tujuan'; $label = 'MISI'; $color = 'bg-emerald-700 text-white'; 
    } elseif($type == 'tujuan') { 
        $childData = $node->sasaranStrategis; $nextType = 'sasaran'; $label = 'TUJUAN PD'; $color = 'bg-blue-700 text-white'; 
    } elseif($type == 'sasaran') { 
        $childData = $node->programs; $nextType = 'program'; $label = 'SASARAN'; $color = 'bg-indigo-600 text-white'; 
    } elseif($type == 'program') { 
        $childData = $node->activities; $nextType = 'kegiatan'; $label = 'PROGRAM'; $color = 'bg-amber-600 text-white'; 
    } elseif($type == 'kegiatan') { 
        $childData = $node->subActivities; $nextType = 'sub'; $label = 'KEGIATAN'; $color = 'bg-rose-600 text-white'; 
    } elseif($type == 'sub') { 
        $childData = null; $nextType = ''; $label = 'SUB-KEG'; $color = 'bg-slate-500 text-white'; 
    }

    // Ambil Teks Utama berdasarkan Model
    $mainText = $node->visi_text ?? $node->misi_text ?? $node->nama_tujuan ?? $node->nama_sasaran ?? $node->nama_program ?? $node->nama_kegiatan ?? $node->nama_sub;
    
    // Ambil Indikator berdasarkan Model
    $indikator = $node->indikator ?? $node->indikator_sasaran ?? $node->indikator_program ?? $node->indikator_kegiatan ?? $node->indikator_sub ?? 'N/A';

    // Logika PATOKAN STATUS (Hanya untuk Sub-Kegiatan)
    $statusColor = 'border-slate-100';
    if($type == 'sub') {
        // Contoh logika sederhana: jika ada target 2025, beri highlight hijau muda
        $statusColor = $node->target_2025 ? 'border-l-4 border-l-emerald-500 bg-emerald-50/30' : 'border-l-4 border-l-rose-500 bg-rose-50/30';
    }
@endphp

<div class="grid grid-cols-[150px_1fr_300px_100px] hover:bg-slate-50 transition-all group border-b border-slate-100 bg-white {{ $statusColor }}">
    {{-- Kolom Label --}}
    <div class="px-6 py-4 flex items-center justify-center border-r border-slate-100">
        <span class="{{ $color }} px-3 py-1 rounded text-[8px] font-bold tracking-widest uppercase shadow-sm w-full text-center">
            {{ $label }}
        </span>
    </div>

    {{-- Kolom Nama/Uraian --}}
    <div class="px-8 py-4 text-sm font-semibold text-slate-700 flex items-center" style="padding-left: {{ $padding + 20 }}px">
        @if($childData && $childData->count() > 0)
            <button onclick="toggleRow('{{ $type }}-{{ $node->id }}')" 
                    class="mr-4 w-6 h-6 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-[10px] text-slate-500 hover:bg-slate-900 hover:text-white transition-all shadow-sm cursor-pointer">
                <i class="fas fa-plus transition-transform duration-300" id="icon-{{ $type }}-{{ $node->id }}"></i>
            </button>
        @else
            <i class="fas fa-circle text-[6px] mr-5 text-slate-300"></i>
        @endif
        
        <div class="flex flex-col">
            <span class="leading-relaxed">{{ $mainText }}</span>
            @if($type == 'sub')
                <span class="text-[10px] text-slate-400 font-normal mt-1">Kode: {{ $node->kode_sub }} | Tipe: {{ $node->tipe_perhitungan }}</span>
            @endif
        </div>
    </div>

    {{-- Kolom Indikator & Target --}}
    <div class="px-8 py-4 text-[11px] text-slate-600 border-x border-slate-100 flex flex-col justify-center">
        <div class="font-medium truncate mb-1" title="{{ $indikator }}">
            <i class="fas fa-chart-line mr-2 text-slate-400"></i>{{ $indikator }}
        </div>
        @if($node->target_2025)
            <div class="flex items-center text-[10px] text-emerald-600 font-bold">
                <span class="bg-emerald-100 px-2 py-0.5 rounded">Target 2025: {{ $node->target_2025 }} {{ $node->satuan ?? '' }}</span>
            </div>
        @endif
    </div>

    {{-- Kolom Aksi --}}
    <div class="px-4 py-4 flex items-center justify-center gap-2">
        <button title="Edit Data" class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 text-slate-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all cursor-pointer">
            <i class="fas fa-pencil-alt text-[10px]"></i>
        </button>
    </div>
</div>

{{-- Container untuk Anak Level (Recursive) --}}
@if($childData && $childData->count() > 0)
    <div id="container-{{ $type }}-{{ $node->id }}" class="hidden">
        @foreach($childData as $child)
            @include('kinerja.pohon.partial-cascading-row', ['node' => $child, 'type' => $nextType, 'level' => $level + 1])
        @endforeach
    </div>
@endif

{{-- Script Global (Hanya load sekali) --}}
@once
<script>
    function toggleRow(id) {
        const container = document.getElementById(`container-${id}`);
        const icon = document.getElementById(`icon-${id}`);
        
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            icon.classList.replace('fa-plus', 'fa-minus');
            icon.classList.add('text-blue-600');
        } else {
            container.classList.add('hidden');
            icon.classList.replace('fa-minus', 'fa-plus');
            icon.classList.remove('text-blue-600');
        }
    }
</script>
@endonce