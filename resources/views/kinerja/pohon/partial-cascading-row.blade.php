@php
    $padding = $level * 30; 
    
    $childData = null; $nextType = ''; $label = ''; $color = '';
    
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
        $childData = null; $nextType = ''; $label = 'SUB KEGIATAN'; $color = 'bg-slate-600 text-white'; 
    }

    $fmt = fn($v) => (float)$v == 0 ? '-' : (float)$v;
@endphp

<div class="mb-2 transition-all duration-300" style="margin-left: {{ $padding }}px">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden flex">
        
        {{-- LABEL --}}
        <div class="{{ $color }} w-28 flex-shrink-0 flex flex-col items-center justify-center p-2 cursor-pointer" onclick="toggleRow('{{ $type }}-{{ $node->id }}')">
            <span class="text-[10px] font-black tracking-widest text-center leading-tight mb-2">{{ $label }}</span>
            @if($childData && $childData->count() > 0)
                <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center">
                    <i id="icon-{{ $type }}-{{ $node->id }}" class="fas fa-plus text-[8px] text-white transition-transform"></i>
                </div>
            @endif
        </div>

        {{-- KONTEN UTAMA --}}
        <div class="p-3 flex-1 border-r border-slate-100 flex flex-col justify-center cursor-pointer hover:bg-slate-50"
             onclick="showDetailNode({{ json_encode($node) }}, '{{ $type }}')">
             
            <div class="font-bold text-slate-800 text-sm leading-snug">
                {{ $node->nama_tujuan ?? $node->nama_sasaran ?? $node->nama_program ?? $node->nama_kegiatan ?? $node->nama_sub ?? $node->visi_text ?? $node->misi_text ?? '-' }}
            </div>

            @if(isset($node->indikator) || isset($node->indikator_sasaran) || isset($node->indikator_program) || isset($node->indikator_kegiatan) || isset($node->indikator_sub))
                <div class="mt-1 flex items-start gap-2 text-xs text-slate-500">
                    <i class="fas fa-chart-pie mt-0.5 text-[10px] text-slate-400"></i>
                    <span class="italic">{{ $node->indikator ?? $node->indikator_sasaran ?? $node->indikator_program ?? $node->indikator_kegiatan ?? $node->indikator_sub ?? '-' }}</span>
                </div>
            @endif

            {{-- TARGET DINAMIS --}}
            @if(isset($node->baseline))
                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center gap-2 overflow-x-auto">
                    <div class="flex flex-col items-center px-2 py-0.5 bg-slate-100 rounded border border-slate-200">
                        <span class="text-[8px] uppercase text-slate-400 font-bold">
                            {{ isset($baselineYear) ? $baselineYear : 'Base' }}
                        </span>
                        <span class="text-[10px] font-mono font-bold text-slate-700">{{ $fmt($node->baseline) }}</span>
                    </div>

                    <i class="fas fa-chevron-right text-[8px] text-slate-300"></i>

                    @for($i = 1; $i <= 5; $i++)
                        @php $val = $node->{"tahun_$i"}; @endphp
                        <div class="flex flex-col items-center px-2 py-0.5 {{ $i==5 ? 'bg-emerald-50 border border-emerald-100' : 'bg-white border border-slate-100' }} rounded">
                            <span class="text-[8px] uppercase {{ $i==5 ? 'text-emerald-400' : 'text-slate-400' }}">
                                {{ isset($startYear) ? ($startYear + $i - 1) : 'Th'.$i }}
                            </span>
                            <span class="text-[10px] font-mono font-bold {{ $i==5 ? 'text-emerald-600' : 'text-slate-600' }}">
                                {{ $fmt($val) }}
                            </span>
                        </div>
                    @endfor
                    
                    <span class="text-[9px] text-slate-400 ml-1 bg-slate-50 px-1.5 rounded">{{ $node->satuan ?? '' }}</span>
                </div>
            @endif
        </div>

        {{-- AKSI --}}
        <div class="w-12 bg-slate-50 flex items-center justify-center border-l border-slate-100">
             <button class="w-8 h-8 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-white hover:shadow-sm transition-all border border-transparent hover:border-slate-200" title="Edit">
                <i class="fas fa-pencil-alt text-xs"></i>
            </button>
        </div>
    </div>
</div>

@if($childData && $childData->count() > 0)
    <div id="container-{{ $type }}-{{ $node->id }}" class="hidden">
        @foreach($childData as $child)
            @include('kinerja.pohon.partial-cascading-row', [
                'node' => $child, 
                'type' => $nextType, 
                'level' => $level + 1,
                'startYear' => $startYear ?? 2025,       // Kirim ke anak
                'baselineYear' => $baselineYear ?? 2024  // Kirim ke anak
            ])
        @endforeach
    </div>
@endif