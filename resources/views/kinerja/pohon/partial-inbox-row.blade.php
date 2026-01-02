@php
    $padding = $level * 30;
    $childData = null;
    $nextType = '';
    $label = '';
    $bg = '';

    // LOGIKA HIRARKI DINAMIS
    if($type == 'visi') { 
        $childData = $node->missions; $nextType = 'misi'; $label = 'VISI'; $bg = 'bg-slate-900 text-white'; 
    } elseif($type == 'misi') { 
        $childData = $node->goals; $nextType = 'tujuan'; $label = 'MISI'; $bg = 'bg-emerald-700 text-white'; 
    } elseif($type == 'tujuan') { 
        // Titik awal bagi OPD
        $childData = $node->sasaranStrategis; 
        $nextType = 'sasaran'; 
        $label = 'TUJUAN PD'; 
        $bg = 'bg-blue-700 text-white'; 
    } elseif($type == 'sasaran') { 
        $childData = $node->programs; 
        $nextType = 'program'; 
        $label = 'SASARAN'; 
        $bg = 'bg-indigo-600 text-white'; 
    } elseif($type == 'program') { 
        $childData = $node->activities; 
        $nextType = 'kegiatan'; 
        $label = 'PROGRAM'; 
        $bg = 'bg-amber-600 text-white'; 
    } elseif($type == 'kegiatan') { 
        $childData = $node->subActivities; 
        $nextType = 'sub'; 
        $label = 'KEGIATAN'; 
        $bg = 'bg-rose-600 text-white'; 
    } elseif($type == 'sub') { 
        $childData = null; $nextType = ''; $label = 'SUB-KEG'; $bg = 'bg-slate-500 text-white'; 
    }

    // Pendeteksian Teks agar tidak error saat level berbeda
    $mainText = $node->visi_text ?? $node->misi_text ?? $node->nama_tujuan ?? $node->nama_sasaran ?? $node->nama_program ?? $node->nama_kegiatan ?? $node->nama_sub;
    $indikator = $node->indikator ?? $node->indikator_sasaran ?? $node->indikator_program ?? $node->indikator_kegiatan ?? $node->indikator_sub ?? 'N/A';
@endphp

{{-- Baris Matriks --}}
<div class="grid grid-cols-[140px_1fr_250px_100px] border-b border-slate-100 hover:bg-slate-50 transition-all bg-white group">
    <div class="px-4 py-4 flex items-center justify-center border-r border-slate-50">
        <span class="px-2 py-1 {{ $bg }} text-[8px] font-black rounded uppercase tracking-tighter w-full text-center shadow-sm">
            {{ $label }}
        </span>
    </div>

    <div class="px-6 py-4 text-xs font-bold text-slate-700 flex items-center" style="padding-left: {{ $padding + 20 }}px">
        @if($childData && $childData->count() > 0)
            <button onclick="toggleRow('{{ $type }}-{{ $node->id }}')" 
                    class="mr-3 w-6 h-6 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-[10px] text-slate-400 hover:text-blue-600 transition-all cursor-pointer">
                <i class="fas fa-plus transition-transform duration-300" id="icon-{{ $type }}-{{ $node->id }}"></i>
            </button>
        @else
            <i class="fas fa-dot-circle me-4 text-slate-300 ml-2"></i>
        @endif
        <span class="leading-relaxed">{{ $mainText }}</span>
    </div>

    <div class="px-6 py-4 text-[10px] text-slate-500 border-l border-slate-50 flex flex-col justify-center italic">
        <span>{{ $indikator }}</span>
        @if($node->target_2025)
            <span class="text-emerald-600 font-black mt-1 not-italic text-[9px]">Target 2025: {{ $node->target_2025 }}</span>
        @endif
    </div>

    <div class="px-4 py-4 text-center border-l border-slate-50 flex items-center justify-center">
        <button class="text-slate-400 hover:text-blue-600 bg-transparent border-0 cursor-pointer"><i class="fas fa-edit"></i></button>
    </div>
</div>

{{-- Rekursi Anak --}}
@if($childData && $childData->count() > 0)
    <div id="container-{{ $type }}-{{ $node->id }}" class="hidden">
        @foreach($childData as $child)
            @include('kinerja.pohon.partial-cascading-row', ['node' => $child, 'type' => $nextType, 'level' => $level + 1])
        @endforeach
    </div>
@endif