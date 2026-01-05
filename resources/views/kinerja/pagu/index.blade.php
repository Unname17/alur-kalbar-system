@extends('kinerja.pohon.index')

@section('page_title', 'Manajemen Pagu Kegiatan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-800 text-lg">Daftar Kegiatan (Pagu Indikatif)</h3>
        <p class="text-sm text-slate-500">Tetapkan pagu per kegiatan sebagai batas atas penyusunan RKA Sub-Kegiatan.</p>
    </div>

    <div class="p-0">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                <tr>
                    <th class="px-6 py-4">Nama Kegiatan</th>
                    <th class="px-6 py-4">Program Induk</th>
                    <th class="px-6 py-4 text-right">Pagu Kegiatan</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($data as $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-700">{{ $item->nama_kegiatan }}</div>
                        <div class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                            <i class="fas fa-barcode"></i> {{ $item->kode_kegiatan ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        {{-- Menampilkan nama program jika relasi ada --}}
                        <span class="text-xs text-slate-500 font-medium">
                            {{ $item->program->nama_program ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-mono text-slate-600 font-bold">
                        Rp {{ number_format($item->pagu_anggaran ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('kinerja.pagu.edit', $item->id) }}" 
                           class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                            <i class="fas fa-wallet"></i> Set Pagu
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                        <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                        <p>Tidak ada Kegiatan yang disetujui.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection