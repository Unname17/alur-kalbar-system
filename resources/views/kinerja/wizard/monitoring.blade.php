@extends('kinerja.pohon.index')

@section('page_title', 'Monitoring Progres Pengajuan')

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6">
    
    @forelse($allData as $item)
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8 hover:shadow-md transition-all">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            
            {{-- INFO DATA --}}
            <div class="md:w-1/3">
                <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[9px] font-black uppercase tracking-widest mb-2 inline-block">
                    {{ $item->level }}
                </span>
                <h3 class="text-lg font-black text-slate-800 leading-tight">{{ $item->nama }}</h3>
            </div>

            {{-- STEPPER VISUALISASI STATUS --}}
            <div class="flex items-center justify-center flex-1">
                
                {{-- STEP 1: KABID --}}
                @php
                    // Logika: Hijau jika ada NIP Verifier. Merah jika Rejected TAPI NIP Verifier kosong.
                    $s1 = 'default';
                    if ($item->nip_verifier) $s1 = 'success';
                    elseif ($item->status == 'rejected' && !$item->nip_verifier) $s1 = 'rejected';
                @endphp
                <div class="flex flex-col items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 
                        {{ $s1 == 'success' ? 'bg-emerald-500 border-emerald-500 text-white' : ($s1 == 'rejected' ? 'bg-rose-500 border-rose-500 text-white' : 'bg-slate-50 border-slate-200 text-slate-300') }}">
                        <i class="fas {{ $s1 == 'success' ? 'fa-check' : ($s1 == 'rejected' ? 'fa-times' : 'fa-minus') }} text-xs"></i>
                    </div>
                    <span class="text-[9px] font-black uppercase {{ $s1 == 'success' ? 'text-emerald-600' : ($s1 == 'rejected' ? 'text-rose-600' : 'text-slate-300') }}">Kabid</span>
                </div>

                {{-- Garis 1 --}}
                <div class="w-12 h-1 {{ $item->nip_verifier ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>

                {{-- STEP 2: KADIS --}}
                @php
                    // Logika: Hijau jika ada NIP Validator. Merah jika Rejected, NIP Verifier Ada, tapi NIP Validator kosong.
                    $s2 = 'default';
                    if ($item->nip_validator) $s2 = 'success';
                    elseif ($item->status == 'rejected' && $item->nip_verifier && !$item->nip_validator) $s2 = 'rejected';
                @endphp
                <div class="flex flex-col items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 
                        {{ $s2 == 'success' ? 'bg-emerald-500 border-emerald-500 text-white' : ($s2 == 'rejected' ? 'bg-rose-500 border-rose-500 text-white' : 'bg-slate-50 border-slate-200 text-slate-300') }}">
                        <i class="fas {{ $s2 == 'success' ? 'fa-check' : ($s2 == 'rejected' ? 'fa-times' : 'fa-minus') }} text-xs"></i>
                    </div>
                    <span class="text-[9px] font-black uppercase {{ $s2 == 'success' ? 'text-emerald-600' : ($s2 == 'rejected' ? 'text-rose-600' : 'text-slate-300') }}">Kadis</span>
                </div>

                {{-- Garis 2 --}}
                <div class="w-12 h-1 {{ $item->nip_validator ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>

                {{-- STEP 3: BAPPEDA --}}
                @php
                    // Logika: Hijau jika Approved. Merah jika Rejected DAN NIP Validator sudah ada (berarti ditolak di tahap akhir).
                    $s3 = 'default';
                    if ($item->status == 'approved') $s3 = 'success';
                    elseif ($item->status == 'rejected' && $item->nip_validator) $s3 = 'rejected';
                @endphp
                <div class="flex flex-col items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 
                        {{ $s3 == 'success' ? 'bg-emerald-500 border-emerald-500 text-white' : ($s3 == 'rejected' ? 'bg-rose-500 border-rose-500 text-white' : 'bg-slate-50 border-slate-200 text-slate-300') }}">
                        <i class="fas {{ $s3 == 'success' ? 'fa-building' : ($s3 == 'rejected' ? 'fa-times' : 'fa-flag') }} text-xs"></i>
                    </div>
                    <span class="text-[9px] font-black uppercase {{ $s3 == 'success' ? 'text-emerald-600' : ($s3 == 'rejected' ? 'text-rose-600' : 'text-slate-300') }}">Bappeda</span>
                </div>

            </div>

            {{-- BADGE STATUS FINAL --}}
            <div class="md:w-32 text-right">
                @if($item->status == 'approved')
                    <span class="px-4 py-2 bg-emerald-100 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm border border-emerald-200">
                        Approved
                    </span>
                @elseif($item->status == 'rejected')
                    <span class="px-4 py-2 bg-rose-100 text-rose-600 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm border border-rose-200">
                        Rejected
                    </span>
                @else
                    <span class="px-4 py-2 bg-slate-100 text-slate-500 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-200">
                        {{ $item->status ?? 'Draft' }}
                    </span>
                @endif
            </div>

        </div>
    </div>
    @empty
    <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-slate-200">
        <p class="text-slate-400 font-bold">Belum ada data pengajuan kinerja.</p>
    </div>
    @endforelse

</div>
@endsection