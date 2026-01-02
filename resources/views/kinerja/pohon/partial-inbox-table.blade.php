<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-4">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
        <h6 class="m-0 text-sm font-bold text-slate-900 flex items-center gap-2">
            <i class="fas fa-stream text-slate-400"></i> 
            @if(request('q')) Hasil Pencarian: <span class="text-blue-600">"{{ request('q') }}"</span> @else Jalur Pengajuan Kinerja @endif
        </h6>
        <span class="inline-flex items-center px-2.5 py-0.5 bg-red-50 text-red-700 text-[10px] font-bold rounded-full border border-red-100">
            {{ $inbox->count() }} DATA DITEMUKAN
        </span>
    </div>

    <div class="p-0 overflow-x-auto">
        <div class="m-header bg-slate-50 border-b border-slate-100 !grid" 
             style="grid-template-columns: 120px 1fr 250px 120px 150px; min-width: 900px;">
            <div class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Level</div>
            <div class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-l border-slate-100">Uraian Kinerja</div>
            <div class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-l border-slate-100">Indikator</div>
            <div class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-l border-slate-100 text-center">Target</div>
            <div class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-l border-slate-100 text-center">Aksi</div>
        </div>
        
        <div id="pohonContainer" style="min-width: 900px;">
            @if(isset($pohons) && $pohons->count() > 0)
                @foreach($pohons as $node)
                    @include('kinerja.pohon.partial-inbox-row', ['node' => $node, 'level' => 0])
                @endforeach
            @else
                <div class="py-20 text-center text-slate-400 italic text-sm">Data tidak ditemukan.</div>
            @endif
        </div>
    </div>
</div>