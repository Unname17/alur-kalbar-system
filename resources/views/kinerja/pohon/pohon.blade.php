@extends('kinerja.pohon.index')
@section('title', 'Pohon Kinerja')
@section('page_title', 'Visualisasi Cascading')

{{-- 1. LOGIKA PHP: PENENTUAN TAHUN (AMBIL DARI VISI AKTIF) --}}
@php
    $activeVision = $visions->firstWhere('is_active', true) ?? $visions->first();
    $startYear = $activeVision ? (int)$activeVision->tahun_awal : date('Y'); // Default 2025
    $endYear   = $activeVision ? (int)$activeVision->tahun_akhir : $startYear + 4; // Default 2029
    $baselineYear = $startYear - 1; // Default 2024
@endphp

@section('content')
<div class="max-w-[1600px] mx-auto">
    {{-- HEADER KONTROL --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div class="space-y-1">
            <nav class="flex text-[11px] text-slate-400 font-black uppercase tracking-widest">
                <span>PERENCANAAN</span> <span class="mx-2">/</span> <span class="text-performance-green">CASCADING TREE</span>
            </nav>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                {{ $startType == 'visi' ? 'Hirarki Kinerja Daerah' : 'Sasaran Kinerja Perangkat Daerah' }}
            </h3>
            {{-- Info Periode --}}
            <span class="inline-block px-3 py-1 bg-slate-100 rounded text-[10px] font-bold text-slate-500 uppercase tracking-wide">
                Periode RPJMD: {{ $startYear }} - {{ $endYear }}
            </span>
        </div>

        <div class="flex items-center gap-3 bg-white p-1.5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex bg-slate-100 p-1 rounded-xl">
                <button onclick="toggleView('visual')" id="btnVisual" class="px-6 py-2 text-xs font-bold uppercase rounded-lg transition-all bg-white text-emerald-700 shadow-sm border border-slate-200 cursor-pointer">
                    <i class="fas fa-project-diagram me-2"></i> Visual
                </button>
                <button onclick="toggleView('matriks')" id="btnMatriks" class="px-6 py-2 text-xs font-bold uppercase rounded-lg transition-all text-slate-500 hover:text-slate-700 hover:bg-slate-200 border-0 cursor-pointer">
                    <i class="fas fa-list-ul me-2"></i> Matriks
                </button>
            </div>

            @php
                $currentYear = now()->year;
                $showSync = !$activeVision || ($currentYear > $endYear);
            @endphp

            @if($isValidator && $showSync)
                <button onclick="syncData()" id="btnSync" class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-6 py-3 rounded-xl text-[10px] font-black tracking-widest shadow-lg transition-all active:scale-95 cursor-pointer uppercase flex items-center gap-2">
                    <i class="fas fa-cloud-download-alt" id="iconSync"></i> <span>Update Visi-Misi</span>
                </button>
            @endif
        </div>
    </div>

    {{-- KANVAS UTAMA --}}
    <div class="bg-slate-200/50 rounded-[2.5rem] border border-slate-300/50 shadow-inner overflow-hidden min-h-[750px] flex flex-col p-2 relative">
        
        {{-- VIEW VISUAL (D3.js) --}}
        <div id="vVisual" class="flex-1 flex flex-col h-full relative">
            <div class="absolute top-6 right-6 z-10 flex flex-col gap-2">
                <button onclick="zoomIn()" class="w-10 h-10 bg-white border border-slate-200 rounded-xl shadow-sm text-slate-600 hover:text-emerald-600 transition-all cursor-pointer"><i class="fas fa-plus"></i></button>
                <button onclick="zoomOut()" class="w-10 h-10 bg-white border border-slate-200 rounded-xl shadow-sm text-slate-600 hover:text-emerald-600 transition-all cursor-pointer"><i class="fas fa-minus"></i></button>
                <button onclick="resetZoom()" class="w-10 h-10 bg-white border border-slate-200 rounded-xl shadow-sm text-slate-600 hover:text-emerald-600 transition-all cursor-pointer"><i class="fas fa-sync-alt"></i></button>
            </div>

            <div id="visualContainer" class="flex-1 bg-white rounded-[2.2rem] border border-slate-200 overflow-hidden relative" 
                 style="background-image: radial-gradient(#cbd5e1 1.2px, transparent 1.2px); background-size: 25px 25px;">
            </div>

            {{-- LEGENDA LEVEL DINAMIS --}}
            <div class="absolute bottom-6 left-6 z-10 bg-white/95 backdrop-blur-md p-5 rounded-3xl border border-slate-200 shadow-2xl w-[260px]">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Legenda Hirarki</p>
                <div class="space-y-2.5">
                    @if($startType == 'visi')
                        <div class="flex items-center gap-3"><span class="w-4 h-4 rounded-md bg-slate-900"></span><span class="text-[9px] font-bold text-slate-600">1. VISI & MISI PROV</span></div>
                    @endif
                    <div class="flex items-center gap-3"><span class="w-4 h-4 rounded-md bg-blue-700"></span><span class="text-[9px] font-bold text-slate-600">{{ $startType == 'visi' ? '3.' : '1.' }} TUJUAN PD</span></div>
                    <div class="flex items-center gap-3"><span class="w-4 h-4 rounded-md bg-indigo-600"></span><span class="text-[9px] font-bold text-slate-600">{{ $startType == 'visi' ? '4.' : '2.' }} SASARAN STRATEGIS</span></div>
                    <div class="flex items-center gap-3"><span class="w-4 h-4 rounded-md bg-amber-600"></span><span class="text-[9px] font-bold text-slate-600">{{ $startType == 'visi' ? '5.' : '3.' }} PROGRAM</span></div>
                    <div class="flex items-center gap-3"><span class="w-4 h-4 rounded-md bg-rose-600"></span><span class="text-[9px] font-bold text-slate-600">{{ $startType == 'visi' ? '6.' : '4.' }} KEGIATAN</span></div>
                    <div class="flex items-center gap-3"><span class="w-4 h-4 rounded-md bg-slate-500"></span><span class="text-[9px] font-bold text-slate-600">{{ $startType == 'visi' ? '7.' : '5.' }} SUB-KEGIATAN</span></div>
                </div>
            </div>
        </div>

        {{-- VIEW MATRIKS (LIST) --}}
        <div id="vMatriks" class="hidden p-6">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                {{-- Header Matriks (Dinamis Tahun) --}}
                <div class="grid grid-cols-[150px_1fr_400px_100px] bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest">
                    <div class="px-8 py-5 text-center">Hierarki</div>
                    <div class="px-8 py-5 border-l border-white/10">Uraian Kinerja</div>
                    <div class="px-8 py-5 border-l border-white/10 text-center">
                        Target ({{ $baselineYear }} - {{ $endYear }})
                    </div>
                    <div class="px-8 py-5 border-l border-white/10 text-center">Aksi</div>
                </div>
                
                {{-- Content Matriks --}}
                <div class="divide-y divide-slate-100 bg-white min-h-[500px]">
                    {{-- PERBAIKAN: Menggunakan $dataPohon (Bukan $tree atau $treeData) --}}
                    @forelse($treeData as $node)
                        @include('kinerja.pohon.partial-cascading-row', [
                            'node' => $node, 
                            'type' => $startType, 
                            'level' => 0,
                            'startYear' => $startYear, // Kirim variabel tahun ke partial
                            'baselineYear' => $baselineYear
                        ])
                    @empty
                        <div class="flex flex-col items-center justify-center py-20">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-folder-open text-slate-300 text-2xl"></i>
                            </div>
                            <div class="text-slate-400 font-bold uppercase text-xs tracking-widest">Data Tidak Ditemukan</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ====================================================================== --}}
{{-- MODAL DETAIL (BASELINE + 5 TAHUN DINAMIS) --}}
{{-- ====================================================================== --}}
<div id="modalDetailNode" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center transition-opacity">
    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300" id="detailContent">
        
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <span id="detail_badge" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-200 text-slate-600">DETAIL</span>
                <h3 class="text-lg font-bold text-slate-800">Detail Informasi Kinerja</h3>
            </div>
            <button onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center shadow-sm cursor-pointer"><i class="fas fa-times"></i></button>
        </div>

        <div class="p-6 space-y-6">
            {{-- Identitas --}}
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomenklatur Kinerja</label>
                <div id="detail_nama" class="text-sm font-bold text-slate-800 leading-relaxed border-l-4 border-emerald-500 pl-3">-</div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Indikator</label>
                    <div id="detail_indikator" class="text-sm text-slate-600 italic bg-slate-50 p-3 rounded-lg border border-slate-100">-</div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Satuan</label>
                    <div id="detail_satuan" class="text-sm font-bold text-slate-700">-</div>
                </div>
            </div>

            {{-- TARGET 5 TAHUN (LABEL DINAMIS ID) --}}
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                <h4 class="text-xs font-black text-slate-700 uppercase mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-emerald-500"></i> Target Kinerja (<span id="lbl_periode">{{ $startYear }}-{{ $endYear }}</span>)
                </h4>

                <div class="grid grid-cols-7 gap-2">
                    {{-- Baseline --}}
                    <div class="flex flex-col items-center p-2 bg-white rounded border border-slate-200 shadow-sm">
                        {{-- ID untuk Label Baseline Dinamis --}}
                        <span id="lbl_baseline" class="text-[9px] font-bold text-slate-400 uppercase mb-1">Baseline ({{ $baselineYear }})</span>
                        <span id="detail_baseline" class="text-xs font-mono font-bold text-slate-700">-</span>
                    </div>

                    {{-- Tahun 1 --}}
                    <div class="flex flex-col items-center p-2 bg-white rounded border border-slate-100">
                        <span id="lbl_t1" class="text-[9px] font-bold text-slate-400 uppercase mb-1">{{ $startYear }}</span>
                        <span id="detail_t1" class="text-xs font-mono font-bold text-indigo-600">-</span>
                    </div>

                    {{-- Tahun 2 --}}
                    <div class="flex flex-col items-center p-2 bg-white rounded border border-slate-100">
                        <span id="lbl_t2" class="text-[9px] font-bold text-slate-400 uppercase mb-1">{{ $startYear + 1 }}</span>
                        <span id="detail_t2" class="text-xs font-mono font-bold text-slate-600">-</span>
                    </div>

                    {{-- Tahun 3 --}}
                    <div class="flex flex-col items-center p-2 bg-white rounded border border-slate-100">
                        <span id="lbl_t3" class="text-[9px] font-bold text-slate-400 uppercase mb-1">{{ $startYear + 2 }}</span>
                        <span id="detail_t3" class="text-xs font-mono font-bold text-slate-600">-</span>
                    </div>

                    {{-- Tahun 4 --}}
                    <div class="flex flex-col items-center p-2 bg-white rounded border border-slate-100">
                        <span id="lbl_t4" class="text-[9px] font-bold text-slate-400 uppercase mb-1">{{ $startYear + 3 }}</span>
                        <span id="detail_t4" class="text-xs font-mono font-bold text-slate-600">-</span>
                    </div>

                    {{-- Tahun 5 --}}
                    <div class="flex flex-col items-center p-2 bg-emerald-50 rounded border border-emerald-100">
                        <span id="lbl_t5" class="text-[9px] font-bold text-emerald-500 uppercase mb-1">{{ $startYear + 4 }}</span>
                        <span id="detail_t5" class="text-xs font-mono font-bold text-emerald-700">-</span>
                    </div>
                     
                    {{-- Status --}}
                    <div class="flex flex-col items-center justify-center">
                        <span id="detail_status" class="text-[9px] px-2 py-1 rounded-full font-bold bg-slate-200 text-slate-500">-</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 rounded-b-2xl border-t border-slate-100 flex justify-end">
            <button onclick="closeDetailModal()" class="px-6 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all shadow-lg shadow-slate-900/20">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js"></script>
<script>
    // Data dari Controller
    const rawData = {!! json_encode($treeData) !!};
    const startType = "{{ $startType }}";
    
    // Variabel Tahun dari PHP
    const startYear = {{ $startYear }};
    const baselineYear = {{ $baselineYear }};
    
    let svg, g, zoomBehavior;
    const colorMap = { 'root_opd': '#1e293b', 'visi': '#0f172a', 'misi': '#047857', 'tujuan': '#1d4ed8', 'sasaran': '#4f46e5', 'program': '#d97706', 'kegiatan': '#e11d48', 'sub': '#64748b' };

    // --- 1. FUNGSI TRANSFORMASI DATA (PERBAIKAN HIRARKI) ---
    function transformToD3(d) {
        if (!d) return null;
        let type = 'sub';
        let children = [];

        // Deteksi Tipe & Ambil Anak (Support camelCase DAN snake_case)
        if (d.visi_text) { 
            type = 'visi'; 
            children = d.missions || []; 
        } 
        else if (d.misi_text) { 
            type = 'misi'; 
            children = d.goals || []; 
        } 
        else if (d.nama_tujuan) { 
            type = 'tujuan'; 
            // FIX: Cek sasaran_strategis (JSON default) ATAU sasaranStrategis
            children = d.sasaran_strategis || d.sasaranStrategis || []; 
        } 
        else if (d.nama_sasaran) { 
            type = 'sasaran'; 
            children = d.programs || []; 
        } 
        else if (d.nama_program) { 
            type = 'program'; 
            children = d.activities || []; 
        } 
        else if (d.nama_kegiatan) { 
            type = 'kegiatan'; 
            // FIX: Cek sub_activities (JSON default) ATAU subActivities
            children = d.sub_activities || d.subActivities || []; 
        }

        return {
            name: d.visi_text || d.misi_text || d.nama_tujuan || d.nama_sasaran || d.nama_program || d.nama_kegiatan || d.nama_sub || "N/A",
            type: type,
            original_data: d,
            indikator: d.indikator || d.indikator_sasaran || d.indikator_program || d.indikator_kegiatan || d.indikator_sub || 'N/A',
            children: children.map(transformToD3).filter(c => c !== null)
        };
    }

    // --- 2. RENDER VISUAL TREE (D3.js) ---
    function renderTree() {
        if (!rawData || rawData.length === 0) return;
        const container = d3.select("#visualContainer");
        container.selectAll("svg").remove();
        const width = document.getElementById('visualContainer').clientWidth;
        
        let hierarchyData;
        // Jika dimulai dari Tujuan (OPD) dan datanya banyak, buat Root Node buatan
        if (startType === 'tujuan' && rawData.length > 0) {
            hierarchyData = { 
                name: "SASARAN KINERJA PERANGKAT DAERAH", 
                type: 'root_opd', 
                children: rawData.map(transformToD3) 
            };
        } else {
            hierarchyData = transformToD3(rawData[0]);
        }

        svg = container.append("svg").attr("width", width).attr("height", 750);
        zoomBehavior = d3.zoom().scaleExtent([0.1, 3]).on("zoom", (e) => g.attr("transform", e.transform));
        svg.call(zoomBehavior);
        g = svg.append("g");

        const treeLayout = d3.tree().nodeSize([400, 250]);
        const root = d3.hierarchy(hierarchyData); 
        treeLayout(root);

        // Render Link (Garis)
        g.selectAll(".link").data(root.links()).enter().append("path")
            .attr("fill", "none").attr("stroke", "#e2e8f0").attr("stroke-width", 2.5)
            .attr("d", d3.linkVertical().x(d => d.x + width/2).y(d => d.y + 100));

        // Render Node (Kotak)
        const node = g.selectAll(".node").data(root.descendants()).enter().append("g")
                    .attr("transform", d => `translate(${d.x + width/2},${d.y + 100})`)
                    .on("click", (e, d) => { 
                        // Klik Root tidak melakukan apa-apa
                        if(d.data.type !== 'root_opd') showDetailNode(d.data.original_data, d.data.type); 
                    });

        // Kotak Background
        node.append("rect").attr("width", 320).attr("height", 85).attr("x", -160).attr("y", -42.5).attr("rx", 22)
            .attr("fill", d => colorMap[d.data.type]).attr("class", "cursor-pointer hover:brightness-110 shadow-md transition-all");

        // Teks Nama
        node.append("foreignObject").attr("width", 280).attr("height", 70).attr("x", -140).attr("y", -35)
            .append("xhtml:div").attr("class", "flex items-center justify-center text-center h-full text-[10px] font-black text-white px-2 leading-tight uppercase select-none pointer-events-none")
            .html(d => d.data.name);
            
        resetZoom();
    }

    const fmt = (val) => { let n = parseFloat(val); return isNaN(n) || n === 0 ? '-' : n; };

    // --- 3. MODAL DETAIL & LABEL TAHUN ---
    function showDetailNode(data, type) {
        let label = type.toUpperCase();
        let colorClass = 'bg-slate-200 text-slate-600';
        
        if(type === 'visi') { colorClass = 'bg-slate-800 text-white'; }
        else if(type === 'misi') { colorClass = 'bg-emerald-600 text-white'; }
        else if(type === 'tujuan') { colorClass = 'bg-blue-600 text-white'; label = 'TUJUAN PD'; }
        else if(type === 'sasaran') { colorClass = 'bg-indigo-600 text-white'; }
        else if(type === 'program') { colorClass = 'bg-amber-500 text-white'; }
        else if(type === 'kegiatan') { colorClass = 'bg-rose-500 text-white'; }
        else if(type === 'sub') { colorClass = 'bg-slate-500 text-white'; label = 'SUB KEGIATAN'; }

        $('#detail_badge').text(label).attr('class', `px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${colorClass}`);
        $('#detail_nama').text(data.nama_tujuan || data.nama_sasaran || data.nama_program || data.nama_kegiatan || data.nama_sub || data.visi_text || data.misi_text || '-');
        $('#detail_indikator').text(data.indikator || data.indikator_sasaran || data.indikator_program || data.indikator_kegiatan || data.indikator_sub || '-');
        $('#detail_satuan').text(data.satuan || '-');

        // FIX: Tambahkan kata "Tahun" pada label
        $('#lbl_baseline').text('Baseline (' + baselineYear + ')');
        $('#lbl_t1').text('Tahun ' + startYear);
        $('#lbl_t2').text('Tahun ' + (startYear + 1));
        $('#lbl_t3').text('Tahun ' + (startYear + 2));
        $('#lbl_t4').text('Tahun ' + (startYear + 3));
        $('#lbl_t5').text('Tahun ' + (startYear + 4));

        $('#detail_baseline').text(fmt(data.baseline));
        $('#detail_t1').text(fmt(data.tahun_1));
        $('#detail_t2').text(fmt(data.tahun_2));
        $('#detail_t3').text(fmt(data.tahun_3));
        $('#detail_t4').text(fmt(data.tahun_4));
        $('#detail_t5').text(fmt(data.tahun_5));

        if(data.status) {
            let statusColor = data.status === 'approved' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600';
            $('#detail_status').text(data.status.toUpperCase()).attr('class', `text-[9px] px-2 py-1 rounded-full font-bold ${statusColor}`);
        } else {
            $('#detail_status').text('N/A').attr('class', 'text-[9px] px-2 py-1 rounded-full font-bold bg-slate-100 text-slate-400');
        }

        $('#modalDetailNode').removeClass('hidden');
        setTimeout(() => $('#detailContent').removeClass('scale-95').addClass('scale-100'), 10);
    }

    function closeDetailModal() {
        $('#detailContent').removeClass('scale-100').addClass('scale-95');
        setTimeout(() => $('#modalDetailNode').addClass('hidden'), 300);
    }
    
    function zoomIn() { svg.transition().duration(300).call(zoomBehavior.scaleBy, 1.2); }
    function zoomOut() { svg.transition().duration(300).call(zoomBehavior.scaleBy, 0.8); }
    function resetZoom() { svg.transition().duration(500).call(zoomBehavior.transform, d3.zoomIdentity.translate(0, 0).scale(0.5)); }

    function toggleView(v) {
        if(v === 'visual') {
            $('#vVisual').removeClass('hidden'); $('#vMatriks').addClass('hidden');
            $('#btnVisual').addClass('bg-white text-emerald-700 shadow-sm').removeClass('text-slate-500');
            $('#btnMatriks').addClass('text-slate-500').removeClass('bg-white text-emerald-700 shadow-sm');
            renderTree(); // Re-render saat pindah tab
        } else {
            $('#vVisual').addClass('hidden'); $('#vMatriks').removeClass('hidden');
            $('#btnMatriks').addClass('bg-white text-emerald-700 shadow-sm').removeClass('text-slate-500');
            $('#btnVisual').addClass('text-slate-500').removeClass('bg-white text-emerald-700 shadow-sm');
        }
    }

    function toggleRow(id) { 
        $(`#container-${id}`).toggleClass('hidden animate__animated animate__fadeIn'); 
        $(`#icon-${id}`).toggleClass('fa-plus fa-minus'); 
    }

    function syncData() {
        if(!confirm('Sinkronkan data Visi-Misi terbaru?')) return;
        $('#btnSync').addClass('opacity-50 pointer-events-none').find('span').text('Loading...');
        $.post("{{ route('kinerja.sync') }}", { _token: "{{ csrf_token() }}" }, function() { 
            alert('Sukses!'); location.reload(); 
        }).fail(function() { 
            alert('Gagal Sinkronisasi'); location.reload(); 
        });
    }

    document.addEventListener("DOMContentLoaded", () => { renderTree(); });
</script>
@endpush