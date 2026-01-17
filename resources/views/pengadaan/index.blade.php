@extends('pengadaan.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-black text-white">Monitoring Pengadaan</h2>
        <a href="{{ route('pengadaan.create') }}" class="px-6 py-3 bg-cyan-600 hover:bg-cyan-500 text-white rounded-2xl font-bold shadow-lg shadow-cyan-900/20 transition-all">
            + Paket Pengadaan Baru
        </a>
    </div>

    <div class="glass-card rounded-[2rem] overflow-hidden border border-slate-800 bg-slate-900/40">
        <table class="w-full text-sm text-left">
            <thead class="text-[10px] text-slate-500 uppercase bg-slate-950/50 font-black">
                <tr>
                    <th class="px-6 py-5">Nama Paket</th>
                    <th class="px-6 py-5">Item Belanja</th>
                    <th class="px-6 py-5">Pagu Total</th>
                    <th class="px-6 py-5">Progress Dokumen (1-10)</th>
                    <th class="px-6 py-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @foreach($packages as $pkg)
                <tr class="hover:bg-cyan-500/5 transition-all">
                    <td class="px-6 py-5">
                        <div class="font-bold text-white">{{ $pkg->nama_paket }}</div>
                        <div class="text-[10px] text-slate-500">{{ $pkg->jenis_pengadaan }} | {{ $pkg->metode_pemilihan }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <span class="px-2 py-1 bg-slate-800 rounded text-cyan-400 font-bold text-[10px]">
                            {{ $pkg->items->count() }} Item Gabungan
                        </span>
                    </td>
                    <td class="px-6 py-5 font-mono font-bold text-emerald-400">
                        Rp {{ number_format($pkg->pagu_paket, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-5">
                        {{-- PROGRESS TRACKER DOKUMEN --}}
                        <div class="flex items-center gap-1">
                            @php
                                // Pemetaan Status ke Urutan Dokumen
                                $statusMap = [
                                    'identifikasi' => 1,
                                    'strategi' => 3,
                                    'spek_hps' => 7,
                                    'pemilihan' => 9,
                                    'kontrak' => 10
                                ];
                                $currentDoc = $statusMap[$pkg->status_tahapan] ?? 1;
                            @endphp
                            @for($i = 1; $i <= 10; $i++)
                                <div class="w-2.5 h-2.5 rounded-full {{ $i <= $currentDoc ? 'bg-cyan-500' : 'bg-slate-700' }}" title="Doc {{ $i }}"></div>
                            @endfor
                            <span class="ml-2 text-[10px] text-slate-400 font-bold uppercase">Doc {{ $currentDoc }}/10</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <a href="{{ route('pengadaan.manage', $pkg->id) }}" class="p-3 bg-slate-800 hover:bg-cyan-600 rounded-xl text-white transition-all">
                            <i class="fas fa-cog"></i> Kelola
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection