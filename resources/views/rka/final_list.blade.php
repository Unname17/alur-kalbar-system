@extends('rka.layout')

@section('header_title', 'RKA Terfinalisasi')

@section('content')
<div class="glass-card rounded-[2.5rem] overflow-hidden border border-slate-800">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-800/50 text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-slate-800">
            <tr>
                <th class="px-6 py-4">Kode Sub Kegiatan</th>
                <th class="px-6 py-4">Nama Sub Kegiatan</th>
                <th class="px-6 py-4 text-right">Total Anggaran</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-slate-800/50">
            @forelse($finalRka as $data)
            <tr class="hover:bg-slate-800/30 transition-all">
                <td class="px-6 py-4 font-mono text-indigo-400 text-xs">
                    {{ $data->subActivity->kode_sub ?? '-' }}
                </td>
                <td class="px-6 py-4">
                    <div class="text-white font-bold">{{ $data->subActivity->nama_sub ?? '-' }}</div>
                    <div class="text-[10px] text-slate-500">{{ $data->sumber_dana ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 text-right text-emerald-400 font-black">
                    Rp {{ number_format($data->total_anggaran ?? 0, 2, ',', '.') }}
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="{{ route('rka.print', $data->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600/10 text-emerald-500 hover:bg-emerald-600 hover:text-white rounded-xl text-xs font-bold transition-all">
                        <i class="fas fa-print"></i> Cetak PDF
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-20 text-center text-slate-500 italic">Belum ada RKA yang difinalisasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection