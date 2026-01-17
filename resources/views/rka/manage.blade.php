@extends('rka.layout')

@section('header_title', 'Step 2: Rincian Belanja')

@section('content')
<div class="space-y-6">
    {{-- INFORMASI TOTAL USULAN RKA (PENGGANTI SISA PAGU) --}}
    <div class="glass-card p-6 rounded-3xl border border-indigo-500/30 flex justify-between items-center bg-indigo-600/5">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-white font-bold text-lg">{{ $rka->subActivity->nama_sub ?? '-' }}</h3>
                <a href="{{ route('rka.edit_header', $rka->id) }}" class="text-indigo-400 hover:text-indigo-300 text-xs font-bold flex items-center gap-1 mt-1">
                    <i class="fas fa-arrow-left"></i> Edit Identitas Dokumen (Step 1)
                </a>
            </div>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Usulan RKA Saat Ini</p>
            {{-- Menggunakan total_anggaran dari model rka untuk menghindari error undefined variable --}}
            <h2 class="text-2xl font-black text-emerald-400 text-glow">
                Rp {{ number_format($rka->total_anggaran ?? 0, 0, ',', '.') }}
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6">
        {{-- FORM INPUT ITEM --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="glass-card p-8 rounded-[2.5rem] sticky top-8 transition-all border border-slate-800">
                <h4 class="text-white font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-cart-plus text-indigo-500"></i> Tambah Item Belanja
                </h4>
                
                @if(session('error'))
                    <div class="mb-4 p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('rka.store_detail', $rka->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Kode Rekening</label>
                        <select name="rekening_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs font-bold text-white outline-none focus:border-indigo-500" required>
                            <option value="">-- Pilih Rekening --</option>
                            @foreach($rekenings as $rek)
                                <option value="{{ $rek->id }}">{{ $rek->kode_rekening }} - {{ $rek->nama_rekening }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Uraian Barang/Jasa</label>
                        <textarea name="uraian_belanja" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white outline-none focus:border-indigo-500" placeholder="Contoh: Map Spesifikasi 5002" required></textarea>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Spesifikasi (Opsional)</label>
                        <input type="text" name="spesifikasi" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white outline-none focus:border-indigo-500" placeholder="e.g. Ukuran A4, Warna Biru">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Koefisien / Vol</label>
                            <input type="number" name="koefisien" step="any" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white outline-none focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Satuan</label>
                            <input type="text" name="satuan" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white outline-none focus:border-indigo-500" placeholder="Box/Rim/Pcs" required>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Harga Satuan (Rp)</label>
                        <input type="number" name="harga_satuan" id="input_harga_satuan" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white outline-none focus:border-indigo-500" required>
                        <div id="harga_satuan_format" class="mt-2 text-[10px] text-amber-400 font-bold italic">Rp 0</div>
                    </div>
                    <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all">
                        Simpan ke Daftar
                    </button>
                </form>
            </div>
        </div>

        {{-- TABEL RINCIAN BELANJA --}}
        <div class="col-span-12 lg:col-span-8">
            <div class="glass-card rounded-[2.5rem] overflow-hidden border border-slate-800">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-800/50 text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Kode / Uraian Belanja</th>
                            <th class="px-6 py-4 text-center">Koefisien</th>
                            <th class="px-6 py-4 text-right">Harga</th>
                            <th class="px-6 py-4 text-right">Jumlah (Rp)</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-800/50">
                        @forelse($details as $detail)
                        <tr class="hover:bg-slate-800/30 transition-all">
                            <td class="px-6 py-4">
                                <div class="text-[10px] font-mono text-indigo-400 mb-1">
                                    {{ $detail->rekening->kode_rekening ?? 'N/A' }}
                                </div>
                                <div class="text-white font-bold">{{ $detail->uraian_belanja }}</div>
                                @if($detail->spesifikasi)
                                    <div class="text-[10px] text-slate-500 italic">{{ $detail->spesifikasi }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-slate-400 font-bold">
                                {{ number_format($detail->koefisien, 0, ',', '.') }} <span class="text-[10px] uppercase">{{ $detail->satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-300 font-mono">
                                {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-emerald-400 font-black font-mono">
                                {{ number_format($detail->sub_total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('rka.destroy_detail', $detail->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center text-slate-500 italic">
                                <i class="fas fa-info-circle mb-2 text-2xl block"></i>
                                Belum ada rincian belanja yang ditambahkan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($details->count() > 0)
                    <tfoot class="bg-slate-800/20 border-t border-slate-800 font-black">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-slate-400 text-[10px] uppercase tracking-widest">Total Keseluruhan</td>
                            <td class="px-6 py-4 text-right text-white text-lg">
                                Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            
            {{-- TOMBOL NAVIGASI LANJUT --}}
            @if($details->count() > 0)
            <div class="mt-8 flex justify-end">
                <a href="{{ route('rka.manage_v3', $rka->id) }}" class="px-10 py-5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-3xl font-black text-sm shadow-xl shadow-indigo-500/20 transition-all flex items-center gap-3">
                    Lanjut ke Step 3 (Tim Anggaran) <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- JAVASCRIPT UNTUK FORMAT MATA UANG --}}
<script>
    document.getElementById('input_harga_satuan').addEventListener('input', function(e) {
        let value = e.target.value;
        let display = document.getElementById('harga_satuan_format');
        
        if (!value || value == 0) {
            display.innerText = "Rp 0";
            return;
        }

        let formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        });

        let formattedValue = formatter.format(value);
        let helperText = "";

        if (value >= 1000000000) helperText = " (Miliar)";
        else if (value >= 1000000) helperText = " (Juta)";
        else if (value >= 1000) helperText = " (Ribu)";

        display.innerText = formattedValue + helperText;
    });
</script>
@endsection