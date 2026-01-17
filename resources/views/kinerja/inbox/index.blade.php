@extends('kinerja.pohon.index')

@section('content')
{{-- Debug Panel (Bisa dihapus nanti) --}}
<div class="bg-amber-50 p-4 rounded-xl mb-6 text-xs font-mono border border-amber-100 flex items-center gap-3">
    <i class="fas fa-user-shield text-amber-600 text-lg"></i>
    <div>
        <span class="block font-bold text-amber-800">MODE VERIFIKASI</span>
        <span class="text-amber-700">Login sebagai <strong class="uppercase">{{ $role }}</strong>. Menampilkan data status: 
            <span class="bg-amber-200 px-2 rounded text-amber-900 font-bold">
                {{ $role == 'kabid' ? 'PENDING' : ($role == 'kadis' ? 'VERIFIED' : ($role == 'bappeda' ? 'VALIDATED' : 'UNKNOWN')) }}
            </span>
        </span>
    </div>
</div>

<div class="max-w-[1200px] mx-auto space-y-8">
    {{-- Header --}}
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Inbox Verifikasi</h1>
            <p class="text-slate-400 font-medium mt-1 text-sm">Tinjau dan setujui usulan kinerja dari staff.</p>
        </div>
        <div class="bg-white px-6 py-3 rounded-2xl border border-slate-200 shadow-sm text-sm font-bold text-slate-600">
            Total Antrean: <span class="text-indigo-600 text-lg ml-2">{{ $items->count() }}</span>
        </div>
    </div>

    @if($items->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-slate-200">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-double text-slate-300 text-2xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800">Inbox Kosong</h3>
            <p class="text-slate-400 mt-2 text-sm">Semua data usulan telah diproses.</p>
        </div>
    @else
        {{-- List Items --}}
        <div class="grid gap-6">
            @foreach($items as $item)
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8 hover:border-indigo-300 transition-all group relative overflow-hidden">
                
                {{-- Badge Klasifikasi (IKU/IKD/IKK) --}}
                @php
                    $klasifikasi = $item->klasifikasi ?? 'IKK';
                    $badgeColor = match($klasifikasi) {
                        'IKU' => 'bg-rose-500 text-white shadow-rose-200', 
                        'IKD' => 'bg-amber-500 text-white shadow-amber-200', 
                        default => 'bg-slate-100 text-slate-500' 
                    };
                    
                    // Helper Format Angka
                    $fmt = fn($v) => (float)$v == 0 ? '-' : (float)$v;
                @endphp
                <div class="absolute top-0 right-0 px-6 py-3 rounded-bl-[2rem] text-[10px] font-black uppercase tracking-widest shadow-lg {{ $badgeColor }}">
                    {{ $klasifikasi }}
                </div>

                {{-- Judul & Tipe --}}
                <div class="flex justify-between items-start mb-6 pr-20">
                    <div class="space-y-2">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-indigo-100">Sub-Kegiatan</span>
                        <h3 class="text-xl font-black text-slate-800 leading-tight">{{ $item->nama_sub }}</h3>
                        {{-- Parent Info --}}
                        <div class="flex items-center gap-2 text-[10px] text-slate-400 font-bold">
                            <i class="fas fa-level-up-alt rotate-90"></i>
                            <span>Induk: {{ $item->activity->nama_kegiatan ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Grid Data Indikator & Target --}}
                <div class="grid grid-cols-12 gap-4 p-6 bg-slate-50 rounded-3xl mb-6 border border-slate-100">
                    {{-- Indikator --}}
                    <div class="col-span-12 md:col-span-4 border-b md:border-b-0 md:border-r border-slate-200 pb-4 md:pb-0 md:pr-4">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">Indikator Kinerja</span>
                        <p class="text-sm font-bold text-slate-700 leading-relaxed">{{ $item->indikator_sub }}</p>
                    </div>
                    
                    {{-- Baseline --}}
                    <div class="col-span-4 md:col-span-2 text-center">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Baseline</span>
                        <div class="bg-white rounded-xl py-2 border border-slate-200">
                            <p class="text-sm font-bold text-slate-600">{{ $fmt($item->baseline) }}</p>
                        </div>
                    </div>

                    {{-- Target Th. 1 (Awal) --}}
                    <div class="col-span-4 md:col-span-2 text-center">
                        <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest block mb-1">Target Th.1</span>
                        <div class="bg-white rounded-xl py-2 border border-indigo-100">
                            <p class="text-sm font-bold text-indigo-600">{{ $fmt($item->tahun_1) }}</p>
                        </div>
                    </div>

                    {{-- Target Th. 5 (Akhir) --}}
                    <div class="col-span-4 md:col-span-2 text-center">
                        <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest block mb-1">Target Th.5</span>
                        <div class="bg-white rounded-xl py-2 border border-emerald-100">
                            <p class="text-sm font-bold text-emerald-600">{{ $fmt($item->tahun_5) }}</p>
                        </div>
                    </div>

                    {{-- Gap / Satuan --}}
                    <div class="col-span-12 md:col-span-2 flex flex-col justify-center items-center pl-4 border-l border-slate-200">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Satuan</span>
                        <span class="px-3 py-1 bg-slate-200 text-slate-600 rounded-full text-[10px] font-bold">{{ $item->satuan }}</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3 justify-end pt-2">
                    {{-- Form Approve --}}
                    <form action="{{ route('kinerja.inbox.approve', ['level' => 'sub_activity', 'id' => $item->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-emerald-500 text-white px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-600 hover:shadow-emerald-200 transition-all flex items-center gap-2">
                            <i class="fas fa-check-circle text-sm"></i> Setujui
                        </button>
                    </form>
                    
                    {{-- Button Reject --}}
                    <button onclick="openRejectModal({{ $item->id }})" class="bg-white text-rose-500 border-2 border-rose-100 px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-50 hover:border-rose-200 transition-all flex items-center gap-2">
                        <i class="fas fa-times-circle text-sm"></i> Tolak
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- MODAL REJECT (Popup Catatan) --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl animate-fade">
        <form id="rejectForm" method="POST">
            @csrf
            <div class="p-8 border-b border-slate-100 bg-rose-50/30">
                <div class="w-12 h-12 bg-rose-100 text-rose-500 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-undo-alt text-xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Kembalikan Data?</h3>
                <p class="text-slate-500 text-xs mt-2 font-medium leading-relaxed">Data akan dikembalikan ke status <strong class="text-rose-500">Rejected</strong>. Staff perlu memperbaiki data sesuai catatan Anda.</p>
            </div>
            
            <div class="p-8">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Catatan Perbaikan</label>
                <textarea name="catatan" rows="4" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold text-slate-700 focus:border-rose-500 focus:ring-0 outline-none transition-colors" placeholder="Tuliskan bagian mana yang perlu diperbaiki..."></textarea>
            </div>

            <div class="p-6 bg-slate-50 flex gap-3 justify-end border-t border-slate-100">
                <button type="button" onclick="closeRejectModal()" class="px-6 py-3 text-slate-400 font-bold text-xs uppercase hover:text-slate-600 transition-colors">Batal</button>
                <button type="submit" class="bg-rose-500 text-white px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-rose-200 hover:bg-rose-600 transition-all">Kirim Revisi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    function openRejectModal(id) {
        // Update Action URL form secara dinamis
        $('#rejectForm').attr('action', "{{ url('kinerja/inbox/reject/sub_activity') }}/" + id);
        $('#rejectModal').removeClass('hidden').addClass('flex');
    }
    function closeRejectModal() {
        $('#rejectModal').addClass('hidden').removeClass('flex');
    }
</script>
@endpush