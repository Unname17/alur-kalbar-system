@extends('pengadaan.layout')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="glass-procurement p-8 rounded-[2.5rem] mb-8 border border-cyan-500/30">
        <h2 class="text-white font-black text-xl">🛒 Konsolidasi Item Belanja</h2>
        <p class="text-slate-400 text-sm">Pilih item belanja dari Sub-Kegiatan yang sudah memiliki KAK untuk dimasukkan ke paket: <span class="text-cyan-400 font-bold">{{ $package->nama_paket }}</span></p>
    </div>

    <form action="{{ route('pengadaan.store.picked', $package->id) }}" method="POST">
        @csrf
        <div class="glass-card rounded-3xl overflow-hidden border border-slate-800">
            <table class="w-full text-sm">
                <thead class="bg-slate-950/50 text-slate-500 text-[10px] uppercase font-bold">
                    <tr>
                        <th class="p-5 text-center">Pilih</th>
                        <th class="p-5">Uraian Belanja & Asal Sub-Kegiatan</th>
                        <th class="p-5 text-center">Volume</th>
                        <th class="p-5 text-right">Pagu Anggaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    @foreach($availableItems as $item)
                    <tr class="hover:bg-cyan-500/5 transition-all">
                        <td class="p-5 text-center">
                            <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" class="w-5 h-5 rounded border-slate-700 bg-slate-800 text-cyan-500">
                        </td>
                        <td class="p-5">
                            <div class="text-white font-bold">{{ $item->uraian_belanja }}</div>
                            <div class="text-[10px] text-cyan-500 font-bold uppercase mt-1">
                                <i class="fas fa-tag"></i> {{ $item->rkaMain->subActivity->nama_sub }}
                            </div>
                        </td>
                        <td class="p-5 text-center font-bold">{{ $item->koefisien }} {{ $item->satuan }}</td>
                        <td class="p-5 text-right font-mono text-emerald-400">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="px-10 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-bold shadow-lg shadow-emerald-900/20 transition-all flex items-center gap-3">
                <i class="fas fa-check-double"></i> Masukkan ke Keranjang Pengadaan
            </button>
        </div>
    </form>
</div>
@endsection