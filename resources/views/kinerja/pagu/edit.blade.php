@extends('kinerja.pohon.index')


@section('page_title', 'Edit Pagu Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-sm border border-slate-200 rounded-2xl">
                <div class="card-header bg-white py-4 border-b border-slate-100">
                    <h5 class="mb-0 text-slate-800 font-bold flex items-center gap-2">
                        <i class="fas fa-wallet text-emerald-500"></i> Penetapan Pagu Kegiatan
                    </h5>
                </div>
                
                <div class="card-body p-6">
                    {{-- Info Kegiatan --}}
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <strong class="text-slate-700 block text-lg">{{ $kegiatan->nama_kegiatan }}</strong>
                            <div class="text-slate-500 text-sm mt-1">Kode: {{ $kegiatan->kode_kegiatan ?? 'Belum diset' }}</div>
                        </div>
                    </div>

                    <form action="{{ route('kinerja.pagu.update', $kegiatan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-6">
                            <label class="form-label font-bold text-slate-700 mb-3 block">Pagu Anggaran Kegiatan (Rp)</label>
                            
                            {{-- Shortcut Buttons --}}
                            <div class="flex flex-wrap gap-2 mb-4">
                                <button type="button" onclick="setQuickPagu(1000000)" class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-500 hover:text-white text-slate-600 rounded-lg text-xs font-bold transition-all border border-slate-200">
                                    + 1 Juta
                                </button>
                                <button type="button" onclick="setQuickPagu(10000000)" class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-500 hover:text-white text-slate-600 rounded-lg text-xs font-bold transition-all border border-slate-200">
                                    + 10 Juta
                                </button>
                                <button type="button" onclick="setQuickPagu(100000000)" class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-500 hover:text-white text-slate-600 rounded-lg text-xs font-bold transition-all border border-slate-200">
                                    + 100 Juta
                                </button>
                                <button type="button" onclick="setQuickPagu(1000000000)" class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-500 hover:text-white text-slate-600 rounded-lg text-xs font-bold transition-all border border-slate-200">
                                    + 1 Miliar
                                </button>
                                <button type="button" onclick="clearPagu()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-500 hover:text-white text-rose-600 rounded-lg text-xs font-bold transition-all border border-rose-100">
                                    Reset
                                </button>
                            </div>

                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">Rp</span>
                                
                                {{-- Input Visual --}}
                                <input type="text" 
                                       class="w-full pl-12 pr-4 py-4 text-right text-3xl font-bold text-emerald-600 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-inner" 
                                       id="rupiah_visual"
                                       placeholder="0"
                                       value="{{ number_format($kegiatan->pagu_anggaran, 0, ',', '.') }}"
                                       onkeyup="formatRupiah(this)">
                                
                                {{-- Input Asli --}}
                                <input type="hidden" name="pagu_anggaran" id="pagu_anggaran" value="{{ $kegiatan->pagu_anggaran }}">
                            </div>
                            
                            {{-- Terbilang Log (Keterangan angka menjadi teks) --}}
                            <div id="terbilang" class="mt-3 text-sm font-medium text-slate-500 italic">
                                {{-- Teks deskripsi angka muncul di sini --}}
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                            <a href="{{ route('kinerja.pagu.index') }}" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold hover:bg-slate-50 transition-all">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </a>
                            
                            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                                <i class="fas fa-save mr-2"></i> Simpan Pagu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi Shortcut Tombol
    function setQuickPagu(amount) {
        const currentVal = parseInt(document.getElementById('pagu_anggaran').value) || 0;
        const newVal = currentVal + amount;
        
        updatePaguValues(newVal);
    }

    function clearPagu() {
        updatePaguValues(0);
    }

    function updatePaguValues(value) {
        const inputVisual = document.getElementById('rupiah_visual');
        const inputHidden = document.getElementById('pagu_anggaran');
        
        inputHidden.value = value;
        inputVisual.value = formatNumber(value);
        updateTerbilang(value);
    }

    // Fungsi Format saat mengetik manual
    function formatRupiah(element) {
        let originalValue = element.value.replace(/[^,\d]/g, '').toString();
        document.getElementById('pagu_anggaran').value = originalValue;
        
        element.value = formatNumber(originalValue);
        updateTerbilang(originalValue);
    }

    function formatNumber(n) {
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Fungsi untuk mengubah angka menjadi teks deskripsi (Jutaan/Miliar) agar user tidak pusing
    function updateTerbilang(n) {
        const num = parseInt(n);
        const target = document.getElementById('terbilang');
        
        if (!num || num === 0) {
            target.innerHTML = "";
            return;
        }

        let desc = "";
        if (num >= 1000000000) {
            desc = (num / 1000000000).toFixed(2).replace('.', ',') + " Miliar";
        } else if (num >= 1000000) {
            desc = (num / 1000000).toFixed(2).replace('.', ',') + " Juta";
        } else if (num >= 1000) {
            desc = (num / 1000).toFixed(2).replace('.', ',') + " Ribu";
        }

        target.innerHTML = `<i class="fas fa-bullhorn mr-1 text-emerald-500"></i> Konversi: <strong>${desc}</strong>`;
    }

    // Jalankan terbilang saat halaman pertama kali dimuat
    window.onload = function() {
        updateTerbilang(document.getElementById('pagu_anggaran').value);
    };
</script>
@endsection