@extends('kinerja.pohon.index')

@section('title', $isBappeda ? 'Monitoring Global' : 'Dashboard OPD')
@section('page_title', $isBappeda ? 'Monitoring Kinerja Provinsi' : 'Ringkasan Kinerja Instansi')

@section('content')
<div class="max-w-[1600px] mx-auto space-y-8">
    
    {{-- HEADER INFORMASI --}}
    <div class="flex justify-between items-end">
        <div>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                {{ $isBappeda ? 'Monitoring Kinerja Provinsi (Global)' : 'Dashboard Kinerja Perangkat Daerah' }}
            </h3>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">
                @if($isBappeda)
                    <i class="fas fa-globe-asia me-2 text-indigo-500"></i> Akumulasi Data Seluruh Perangkat Daerah
                @else
                    <i class="fas fa-university me-2 text-emerald-500"></i> {{ Auth::user()->perangkatDaerah->nama_pd ?? 'Unit Kerja' }}
                @endif
            </p>
        </div>

        @if(!$isBappeda)
            <div class="flex items-center gap-3 px-5 py-3 rounded-2xl {{ $is_locked ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} border shadow-sm">
                <i class="fas {{ $is_locked ? 'fa-lock' : 'fa-lock-open' }} text-xs"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Akses Input: {{ $is_locked ? 'Terkunci' : 'Terbuka' }}</span>
            </div>
        @endif
    </div>

    {{-- STATISTIK DINAMIS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @foreach([
            ['l' => 'Tujuan PD', 'v' => $stats['tujuan'], 'i' => 'fa-bullseye', 'c' => 'text-blue-700', 'b' => 'bg-blue-50'],
            ['l' => 'Total Program', 'v' => $stats['program'], 'i' => 'fa-layer-group', 'c' => 'text-amber-600', 'b' => 'bg-amber-50'],
            ['l' => 'Total Kegiatan', 'v' => $stats['kegiatan'], 'i' => 'fa-tasks', 'c' => 'text-rose-600', 'b' => 'bg-rose-50'],
            ['l' => 'Sub-Kegiatan', 'v' => $stats['sub_kegiatan'], 'i' => 'fa-clipboard-list', 'c' => 'text-slate-600', 'b' => 'bg-slate-100'],
        ] as $item)
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-200 shadow-sm flex items-center gap-5 hover:shadow-md transition-all group">
            <div class="w-14 h-14 {{ $item['b'] }} {{ $item['c'] }} rounded-2xl flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                <i class="fas {{ $item['i'] }} text-xl"></i>
            </div>
            <div>
                <h4 class="text-2xl font-black text-slate-800">{{ number_format($item['v'], 0, ',', '.') }}</h4>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $item['l'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- VISUALISASI POHON (PREVIEW) --}}
    <div class="bg-white rounded-[3rem] border border-slate-200 shadow-sm overflow-hidden p-2">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h4 class="text-xl font-black text-slate-800">Preview Hirarki Kinerja</h4>
                <p class="text-xs text-slate-400 font-medium">Logika "Patokan" Kinerja Berdasarkan Role Akses</p>
            </div>
            <a href="{{ route('kinerja.pohon') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all hover:bg-performance-green shadow-lg shadow-slate-200 no-underline">
                Buka Full Tree <i class="fas fa-project-diagram ms-2"></i>
            </a>
        </div>
        
        <div id="miniTreeContainer" class="h-[550px] bg-slate-50/50 relative cursor-grab active:cursor-grabbing" 
             style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 30px 30px;">
             
             {{-- Legenda Mini --}}
             <div class="absolute bottom-6 left-6 z-10 bg-white/90 p-4 rounded-2xl border border-slate-200 shadow-sm w-48">
                <p class="text-[8px] font-black text-slate-400 uppercase mb-2">Warna Level</p>
                <div class="space-y-1.5">
                    @if($startType == 'visi')
                        <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-slate-900"></span><span class="text-[7px] font-bold text-slate-600 uppercase">Visi/Misi</span></div>
                    @endif
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-700"></span><span class="text-[7px] font-bold text-slate-600 uppercase">Tujuan PD</span></div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-indigo-600"></span><span class="text-[7px] font-bold text-slate-600 uppercase">Sasaran</span></div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-600"></span><span class="text-[7px] font-bold text-slate-600 uppercase">Program</span></div>
                </div>
             </div>

             <div id="loadingTree" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
                 <i class="fas fa-circle-notch fa-spin text-2xl text-slate-300"></i>
             </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js"></script>
<script>
    // Gunakan treeData yang sudah difilter di controller
    const rawData = {!! json_encode($treeData) !!};
    const startType = "{{ $startType }}"; 

    function transformToD3(d) {
        if (!d) return null;
        
        let children = [];
        let type = '';

        // Deteksi Level secara cerdas
        if (d.visi_text) { 
            type = 'visi'; children = d.missions || []; 
        } else if (d.misi_text) { 
            type = 'misi'; children = d.goals || []; 
        } else if (d.nama_tujuan) { 
            type = 'tujuan'; children = d.sasaran_strategis || d.sasaranStrategis || []; 
        } else if (d.nama_sasaran) { 
            type = 'sasaran'; children = d.programs || []; 
        } else if (d.nama_program) { 
            type = 'program'; children = d.activities || []; 
        } else if (d.nama_kegiatan) { 
            type = 'kegiatan'; children = d.sub_activities || d.subActivities || []; 
        } else { 
            type = 'sub'; 
        }

        return {
            name: d.visi_text || d.misi_text || d.nama_tujuan || d.nama_sasaran || d.nama_program || d.nama_kegiatan || d.nama_sub || "N/A",
            type: type,
            children: children.map(transformToD3).filter(c => c !== null)
        };
    }

    function renderMiniTree() {
        if (!rawData || rawData.length === 0) return;
        
        const container = d3.select("#miniTreeContainer");
        const width = document.getElementById('miniTreeContainer').clientWidth;
        
        // LOGIKA VIRTUAL ROOT: Agar jika OPD punya banyak tujuan, tetap jadi satu pohon
        let hierarchyData;
        if (startType === 'tujuan' && rawData.length > 1) {
            hierarchyData = { 
                name: "SASARAN KINERJA OPD", 
                type: 'root', 
                children: rawData.map(transformToD3) 
            };
        } else {
            // Jika Bappeda atau OPD cuma punya 1 tujuan, ambil index pertama
            hierarchyData = transformToD3(rawData[0]);
        }

        const svg = container.append("svg").attr("width", width).attr("height", 550);
        const g = svg.append("g");
        
        svg.call(d3.zoom().scaleExtent([0.1, 2]).on("zoom", (e) => g.attr("transform", e.transform)));

        const treeLayout = d3.tree().nodeSize([300, 200]);
        const root = d3.hierarchy(hierarchyData); 
        treeLayout(root);

        // Render Garis & Kotak (Gunakan colorMap yang sama dengan pohon.blade.php)
        const colorMap = { 
            'root': '#1e293b', 'visi': '#0f172a', 'misi': '#047857', 
            'tujuan': '#1d4ed8', 'sasaran': '#4f46e5', 'program': '#d97706', 
            'kegiatan': '#e11d48', 'sub': '#64748b' 
        };

        g.selectAll(".link").data(root.links()).enter().append("path")
            .attr("fill", "none").attr("stroke", "#cbd5e1").attr("stroke-width", 1.5)
            .attr("d", d3.linkVertical().x(d => d.x + width/2).y(d => d.y + 80));

        const node = g.selectAll(".node").data(root.descendants()).enter().append("g")
                    .attr("transform", d => `translate(${d.x + width/2},${d.y + 80})`);

        node.append("rect").attr("width", 220).attr("height", 60).attr("x", -110).attr("y", -30).attr("rx", 15)
            .attr("fill", d => colorMap[d.data.type]).attr("stroke", "#e2e8f0");

        node.append("foreignObject").attr("width", 200).attr("height", 50).attr("x", -100).attr("y", -25)
            .append("xhtml:div").attr("class", "flex items-center justify-center text-center h-full text-[9px] font-black leading-tight text-white uppercase")
            .html(d => d.data.name);

        document.getElementById('loadingTree').classList.add('hidden');
        
        // Auto-center view
        svg.transition().duration(500).call(d3.zoom().transform, d3.zoomIdentity.translate(0, 0).scale(0.6));
    }

    document.addEventListener("DOMContentLoaded", renderMiniTree);
</script>
@endpush