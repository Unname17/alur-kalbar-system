@extends('pengadaan.layout')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <form action="{{ route('pengadaan.update.doc1', $package->id) }}" method="POST">
        @csrf
        <div class="glass-procurement p-10 rounded-[3rem] border border-emerald-500/20 shadow-2xl bg-slate-900/40">
            
            <div class="mb-10 border-b border-slate-800 pb-8">
                <h3 class="text-white font-black text-2xl">Finalisasi Perencanaan (Doc 1)</h3>
                <p class="text-slate-500 text-sm mt-1">Lengkapi informasi administratif untuk identifikasi kebutuhan pengadaan.</p>
            </div>

            <div class="space-y-8">
                {{-- 1. INFORMASI UMUM & IDENTITAS --}}
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">1.1 Perubahan Ke-</label>
                            <input type="number" name="perubahan_ke" value="{{ $package->perubahan_ke ?? 0 }}" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Tanggal Perubahan</label>
                            <input type="date" name="tanggal_perubahan" value="{{ $package->tanggal_perubahan }}" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-white">
                        </div>
                    </div>

                    <div class="bg-slate-950/50 p-6 rounded-3xl border border-slate-800">
                        <label class="text-[10px] font-black text-cyan-500 uppercase block mb-4">1.2 Identitas Organisasi (Konsolidasi)</label>
                        @foreach($identitasOrganisasi as $index => $org)
                            <div class="mb-4 {{ !$loop->last ? 'border-b border-slate-800 pb-4' : '' }}">
                                <div class="text-[9px] text-emerald-500 font-bold mb-1">SUMBER #{{ $index + 1 }}</div>
                                <div class="grid grid-cols-1 gap-1 text-xs">
                                    <div class="text-slate-500">Program: <span class="text-white font-bold">{{ $org->program }}</span></div>
                                    <div class="text-slate-500">Kegiatan: <span class="text-white font-bold">{{ $org->kegiatan }}</span></div>
                                    <div class="text-slate-500">Output: <span class="text-white font-bold">{{ $org->output }}</span></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 2. ANGGARAN --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">2.2 Pertimbangan Penggunaan Akun</label>
                    <textarea name="pertimbangan_akun" rows="2" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-4 text-white text-sm" placeholder="Contoh: Belanja modal untuk peremajaan aset..."></textarea>
                </div>

                {{-- 3. PDN & ALASAN --}}
                <div class="p-6 bg-slate-950/30 border border-slate-800 rounded-3xl">
                    <label class="block text-[10px] font-black text-cyan-500 uppercase mb-4">3.2 Prioritas Produk Dalam Negeri</label>
                    <div class="space-y-3 text-sm text-slate-400">
                        @foreach([1=>'Barang TKDN > 40%', 2=>'Barang PDN < 25%', 3=>'Barang PDN (Info TKDN -)', 4=>'Barang Impor'] as $val => $txt)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="opsi_pdn" value="{{ $val }}" {{ ($package->opsi_pdn ?? 1) == $val ? 'checked' : '' }}> {{ $txt }}
                            </label>
                        @endforeach
                    </div>
                    <textarea name="alasan_pdn" rows="2" class="mt-4 w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs" placeholder="Alasan pemilihan..."></textarea>
                </div>

                {{-- 4. KBKI & CARA PENGADAAN --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-900/40 p-6 rounded-2xl border border-slate-800">
                    <div>
                        <label class="block text-[10px] font-black text-cyan-500 uppercase mb-2">4.3.3 Kode KBKI Master</label>
                        <select id="kbki_selector" name="kode_kbki" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white text-sm outline-none">
                            <option value="">-- Pilih Kode KBKI --</option>
                            @foreach($kbkiList as $k)
                                <option value="{{ $k->kode_kbki }}" {{ $package->kode_kbki == $k->kode_kbki ? 'selected' : '' }}>
                                    {{ $k->kode_kbki }} - {{ Str::limit($k->deskripsi_kbki, 40) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Deskripsi KBKI (Auto-fill)</label>
                        <input type="text" id="kbki_description" name="deskripsi_kbki" value="{{ $package->deskripsi_kbki }}" readonly 
                               class="w-full bg-slate-950/30 border border-slate-800 rounded-xl p-3 text-slate-400 text-sm">
                    </div>
                </div>

                {{-- 5. LOKASI & JADWAL (STATIS) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-3">5.1 Lokasi Pekerjaan</label>
                        <input type="text" name="lokasi_pekerjaan" value="{{ $package->lokasi_pekerjaan ?? $lokasiDefault }}" class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-4 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-3">5.1 Jadwal Pelaksanaan</label>
                        <input type="text" name="jadwal_pelaksanaan" value="{{ $jadwalDefault }}" readonly class="w-full bg-slate-900 border border-slate-800 rounded-2xl p-4 text-slate-500 text-sm cursor-not-allowed">
                    </div>
                </div>

                {{-- 6. PENGESAHAN --}}
                <div class="bg-slate-950/50 p-8 rounded-[2rem] border border-slate-800 space-y-6">
                    <label class="block text-[10px] font-black text-cyan-500 uppercase tracking-widest mb-2">6. PENGESAHAN</label>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] text-slate-500 uppercase mb-2">Pengguna Anggaran (PA)</p>
                            <input type="text" name="nama_pa_kpa" value="{{ $package->nama_pa_kpa ?? 'Samuel, S.E., M.Si.' }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white text-sm">
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 uppercase mb-2">NIP PA</p>
                            <input type="text" name="nip_pa_kpa" value="{{ $package->nip_pa_kpa ?? '197005121996031004' }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12">
                <button type="submit" class="w-full py-5 rounded-2xl bg-gradient-to-r from-emerald-600 to-cyan-600 text-white font-black uppercase text-xs shadow-xl shadow-emerald-900/40">
                    Selesaikan Dokumen 1 <i class="fas fa-check-circle ml-2"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('kbki_selector').addEventListener('change', function() {
        const kode = this.value;
        const descInput = document.getElementById('kbki_description');
        
        if (kode) {
            descInput.value = 'Sedang mengambil data...';
            
            // Gunakan template literal dengan path absolut
            fetch('/api/kbki/' + kode)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data && data.deskripsi_kbki) {
                        descInput.value = data.deskripsi_kbki;
                    } else {
                        descInput.value = 'Deskripsi tidak ditemukan';
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    descInput.value = 'Gagal mengambil data (Periksa Controller/Route)';
                });
        } else {
            descInput.value = '';
        }
    });
</script>
@endsection