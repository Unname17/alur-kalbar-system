@extends('kinerja.pohon.index')

@section('content')
<div class="bg-amber-100 p-4 rounded-xl mb-6 text-xs font-mono">
    DEBUG: Role Anda saat ini adalah <strong>"{{ $role }}"</strong>. 
    Sistem sedang mencari data dengan status: 
    <strong>
        {{ $role == 'kabid' ? 'pending' : ($role == 'kadis' ? 'verified' : ($role == 'bappeda' ? 'validated' : 'TIDAK DIKENAL')) }}
    </strong>
</div>

<div class="max-w-[1200px] mx-auto space-y-8">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Inbox Verifikasi</h1>
            <p class="text-slate-400 font-medium mt-1">Anda login sebagai: <span class="text-indigo-600 font-bold uppercase">{{ $role }}</span></p>
        </div>
        <div class="bg-white px-6 py-3 rounded-2xl border border-slate-200 shadow-sm text-sm font-bold text-slate-600">
            Total Antrean: <span class="text-indigo-600">{{ $items->count() }}</span>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-slate-200">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-double text-slate-300 text-2xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800">Inbox Kosong</h3>
            <p class="text-slate-400 mt-2">Semua data usulan dari staff telah diproses.</p>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($items as $item)
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8 hover:border-indigo-300 transition-all group relative overflow-hidden">
                
                {{-- [BARU] Badge Klasifikasi SPK --}}
                @php
                    $klasifikasi = $item->klasifikasi ?? 'IKK';
                    $badgeColor = match($klasifikasi) {
                        'IKU' => 'bg-rose-500 text-white shadow-rose-200', // Prioritas Tinggi
                        'IKD' => 'bg-amber-500 text-white shadow-amber-200', // Prioritas Sedang
                        default => 'bg-slate-100 text-slate-500' // Standar
                    };
                @endphp
                <div class="absolute top-0 right-0 px-6 py-3 rounded-bl-[2rem] text-[10px] font-black uppercase tracking-widest shadow-lg {{ $badgeColor }}">
                    {{ $klasifikasi }}
                </div>

                <div class="flex justify-between items-start mb-6 pr-16">
                    <div class="space-y-1">
                        <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[9px] font-black uppercase tracking-widest">Sub-Kegiatan</span>
                        <h3 class="text-xl font-black text-slate-800 leading-tight">{{ $item->nama_sub }}</h3>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4 p-6 bg-slate-50 rounded-3xl mb-6">
                    <div class="col-span-4 md:col-span-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Indikator</span>
                        <p class="text-sm font-bold text-slate-700">{{ $item->indikator_sub }}</p>
                    </div>
                    
                    {{-- [BARU] Menampilkan Baseline --}}
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Baseline 2024</span>
                        <p class="text-sm font-bold text-slate-500">{{ $item->baseline_2024 ?? 0 }} {{ $item->satuan }}</p>
                    </div>

                    {{-- Target --}}
                    <div>
                        <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest block mb-1">Target 2025</span>
                        <p class="text-sm font-bold text-emerald-700">{{ $item->target_2025 }} {{ $item->satuan }}</p>
                    </div>

                    {{-- [BARU] Gap Kinerja (Hitungan Kasar untuk Info Verifikator) --}}
                    @php
                        $gap = ($item->target_2025 ?? 0) - ($item->baseline_2024 ?? 0);
                        $isPositive = $gap > 0;
                    @endphp
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Gap Kinerja</span>
                        <p class="text-sm font-black {{ $isPositive ? 'text-indigo-600' : 'text-slate-400' }}">
                            {{ $isPositive ? '+' : '' }}{{ $gap }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 justify-end border-t border-slate-100 pt-5">
                     <span class="self-center mr-auto text-[10px] font-bold text-slate-400">
                        Induk: <span class="text-slate-600">{{ $item->activity->nama_kegiatan ?? '-' }}</span>
                     </span>

                    {{-- Tombol Approve --}}
                    <form action="{{ route('kinerja.inbox.approve', ['level' => 'sub_activity', 'id' => $item->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-emerald-500 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-600 transition-all">
                            <i class="fas fa-check me-2"></i> Setujui
                        </button>
                    </form>
                    {{-- Tombol Reject (Trigger Modal) --}}
                    <button onclick="openRejectModal({{ $item->id }})" class="bg-white text-rose-500 border-2 border-rose-100 px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-50 transition-all">
                        <i class="fas fa-times me-2"></i> Tolak/Revisi
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- MODAL REJECT (TETAP SAMA) --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] w-full max-w-lg overflow-hidden shadow-2xl animate-fade">
        <form id="rejectForm" method="POST">
            @csrf
            <div class="p-10">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Berikan Catatan Revisi</h3>
                <p class="text-slate-400 text-sm mt-2 font-medium">Sebutkan alasan penolakan agar staff dapat memperbaiki data.</p>
                <textarea name="catatan" rows="4" class="w-full mt-6 bg-slate-50 border-2 border-slate-100 rounded-2xl p-5 text-sm font-bold focus:border-rose-500 outline-none" placeholder="Contoh: Indikator kurang spesifik, mohon diperbaiki sesuai RKA..."></textarea>
            </div>
            <div class="p-8 bg-slate-50 flex gap-4 justify-end">
                <button type="button" onclick="closeRejectModal()" class="px-8 py-4 text-slate-400 font-bold text-xs uppercase">Batal</button>
                <button type="submit" class="bg-rose-500 text-white px-10 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-rose-200">Kirim Revisi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    function openRejectModal(id) {
        $('#rejectForm').attr('action', "{{ url('kinerja/inbox/reject/sub_activity') }}/" + id);
        $('#rejectModal').removeClass('hidden').addClass('flex');
    }
    function closeRejectModal() {
        $('#rejectModal').addClass('hidden').removeClass('flex');
    }
</script>
@endpush