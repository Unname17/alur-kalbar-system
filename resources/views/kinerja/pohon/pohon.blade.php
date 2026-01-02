@extends('kinerja.pohon.index')
@section('title', 'Pohon Kinerja')
@section('page_title', 'Visualisasi Cascading')

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
                $currentVision = $visions->first();
                $currentYear = now()->year;
                $showSync = !$currentVision || ($currentYear > $currentVision->tahun_akhir);
            @endphp

            @if($isValidator && $showSync)
                <button onclick="syncData()" id="btnSync" class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-6 py-3 rounded-xl text-[10px] font-black tracking-widest shadow-lg transition-all active:scale-95 cursor-pointer uppercase flex items-center gap-2">
                    <i class="fas fa-cloud-download-alt" id="iconSync"></i> <span>Update Visi-Misi</span>
                </button>
            @elseif($currentVision)
                <div class="px-5 py-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-2">
                    <i class="fas fa-check-circle text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Periode {{ $currentVision->tahun_awal }}-{{ $currentVision->tahun_akhir }}</span>
                </div>
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

        {{-- VIEW MATRIKS --}}
        <div id="vMatriks" class="hidden p-6">
            <div class="rounded-3xl border border-slate-200 overflow-hidden bg-white">
                <div class="grid grid-cols-[150px_1fr_300px_100px] bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest">
                    <div class="px-8 py-5 text-center">Hierarki</div>
                    <div class="px-8 py-5 border-l border-white/10">Uraian Kinerja</div>
                    <div class="px-8 py-5 border-l border-white/10 text-center">Indikator & Target 2025</div>
                    <div class="px-8 py-5 border-l border-white/10 text-center">Aksi</div>
                </div>
                <div class="divide-y divide-slate-100">
                    {{-- PERBAIKAN UTAMA: Menggunakan $treeData dan $startType agar sinkron dengan Controller --}}
                    @forelse($treeData as $node)
                        @include('kinerja.pohon.partial-cascading-row', [
                            'node' => $node, 
                            'type' => $startType, 
                            'level' => 0
                        ])
                    @empty
                        <div class="py-20 text-center text-slate-400 font-bold uppercase text-xs tracking-widest">Data Tidak Ditemukan</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="modalDetail" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative w-full max-w-lg bg-white rounded-[2.5rem] shadow-2xl p-10 border border-slate-200">
        <div class="flex justify-between items-start mb-6">
            <div id="modalLevel" class="px-4 py-1.5 rounded-lg text-[9px] font-black text-white uppercase tracking-widest shadow-sm">LEVEL</div>
            <button onclick="closeModal()" class="text-slate-400 hover:text-rose-500 transition-colors"><i class="fas fa-times text-xl"></i></button>
        </div>
        <h4 id="modalTitle" class="text-xl font-black text-slate-800 leading-tight mb-6">Judul Kinerja</h4>
        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 space-y-4">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Indikator Kinerja</p>
                <p id="modalIndikator" class="text-sm font-bold text-slate-700 leading-relaxed"></p>
            </div>
            <div class="pt-4 border-t border-slate-200 flex justify-between items-center">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase">Target 2025</p>
                    <p id="modalTarget" class="text-sm font-black text-emerald-600 mt-1"></p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Satuan</p>
                    <p id="modalSatuan" class="text-sm font-bold text-slate-700 mt-1"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js"></script>
<script>
    const rawData = {!! json_encode($treeData) !!};
    const startType = "{{ $startType }}";
    let svg, g, zoomBehavior;

    const colorMap = { 
        'root_opd': '#1e293b', 'visi': '#0f172a', 'misi': '#047857', 
        'tujuan': '#1d4ed8', 'sasaran': '#4f46e5', 'program': '#d97706', 
        'kegiatan': '#e11d48', 'sub': '#64748b' 
    };

    function transformToD3(d) {
        if (!d) return null;
        
        let type = 'sub';
        let children = [];

        if (d.visi_text) {
            type = 'visi';
            children = d.missions || [];
        } else if (d.misi_text) {
            type = 'misi';
            children = d.goals || [];
        } else if (d.nama_tujuan) {
            type = 'tujuan';
            children = d.sasaran_strategis || d.sasaranStrategis || [];
        } else if (d.nama_sasaran) {
            type = 'sasaran';
            children = d.programs || [];
        } else if (d.nama_program) {
            type = 'program';
            children = d.activities || [];
        } else if (d.nama_kegiatan) {
            type = 'kegiatan';
            children = d.sub_activities || d.subActivities || [];
        }

        return {
            name: d.visi_text || d.misi_text || d.nama_tujuan || d.nama_sasaran || d.nama_program || d.nama_kegiatan || d.nama_sub || "N/A",
            type: type,
            indikator: d.indikator || d.indikator_sasaran || d.indikator_program || d.indikator_kegiatan || d.indikator_sub || 'N/A',
            target: d.target_2025 || '-',
            satuan: d.satuan || '-',
            children: children.map(transformToD3).filter(c => c !== null)
        };
    }

    function renderTree() {
        if (!rawData || rawData.length === 0) return;
        const container = d3.select("#visualContainer");
        container.selectAll("svg").remove();
        const width = document.getElementById('visualContainer').clientWidth;
        
        let hierarchyData;
        if (startType === 'tujuan' && rawData.length > 1) {
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

        g.selectAll(".link").data(root.links()).enter().append("path")
            .attr("fill", "none").attr("stroke", "#e2e8f0").attr("stroke-width", 2.5)
            .attr("d", d3.linkVertical().x(d => d.x + width/2).y(d => d.y + 100));

        const node = g.selectAll(".node").data(root.descendants()).enter().append("g")
                    .attr("transform", d => `translate(${d.x + width/2},${d.y + 100})`)
                    .on("click", (e, d) => showDetail(d.data));

        node.append("rect").attr("width", 320).attr("height", 85).attr("x", -160).attr("y", -42.5).attr("rx", 22)
            .attr("fill", d => colorMap[d.data.type]).attr("class", "cursor-pointer hover:brightness-110 shadow-md");

        node.append("foreignObject").attr("width", 280).attr("height", 70).attr("x", -140).attr("y", -35)
            .append("xhtml:div").attr("class", "flex items-center justify-center text-center h-full text-[10px] font-black text-white px-2 leading-tight uppercase")
            .html(d => d.data.name);
            
        resetZoom();
    }

    function showDetail(data) {
        if(data.type === 'root_opd') return;
        $('#modalTitle').text(data.name);
        $('#modalIndikator').text(data.indikator);
        $('#modalTarget').text(data.target);
        $('#modalSatuan').text(data.satuan);
        $('#modalLevel').text(data.type).css('background-color', colorMap[data.type]);
        $('#modalDetail').removeClass('hidden').addClass('flex');
    }

    function closeModal() { $('#modalDetail').addClass('hidden'); }
    function zoomIn() { svg.transition().duration(300).call(zoomBehavior.scaleBy, 1.2); }
    function zoomOut() { svg.transition().duration(300).call(zoomBehavior.scaleBy, 0.8); }
    function resetZoom() { svg.transition().duration(500).call(zoomBehavior.transform, d3.zoomIdentity.translate(0, 0).scale(0.5)); }

    function toggleView(v) {
        if(v === 'visual') {
            $('#vVisual').removeClass('hidden'); $('#vMatriks').addClass('hidden');
            $('#btnVisual').addClass('bg-white text-emerald-700 shadow-sm').removeClass('text-slate-500');
            $('#btnMatriks').addClass('text-slate-500').removeClass('bg-white text-emerald-700 shadow-sm');
            renderTree();
        } else {
            $('#vVisual').addClass('hidden'); $('#vMatriks').removeClass('hidden');
            $('#btnMatriks').addClass('bg-white text-emerald-700 shadow-sm').removeClass('text-slate-500');
            $('#btnVisual').addClass('text-slate-500').removeClass('bg-white text-emerald-700 shadow-sm');
        }
    }

    // Fungsi Toggle Node Matriks
    function toggleRow(id) {
        $(`#container-${id}`).toggleClass('hidden animate__animated animate__fadeIn');
        $(`#icon-${id}`).toggleClass('rotate-90 text-blue-600');
    }

    function syncData() {
        if(!confirm('Sinkronkan data Visi-Misi terbaru?')) return;
        $('#btnSync').addClass('opacity-50 pointer-events-none').find('span').text('Loading...');
        $.post("{{ route('kinerja.sync') }}", { _token: "{{ csrf_token() }}" }, function() {
            alert('Sukses!'); location.reload();
        }).fail(function() { alert('Gagal Sinkronisasi'); location.reload(); });
    }

    document.addEventListener("DOMContentLoaded", () => { renderTree(); });
</script>
@endpush