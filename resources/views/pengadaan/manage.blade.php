@extends('pengadaan.layout')

@section('title', 'Kelola Paket Pengadaan')
@section('header_title', 'Manajemen Paket Pengadaan')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ 
        tab: '{{ session('tab', 'overview') }}', 
        editDoc2: false, 
        editDoc3: false,
        jalur: '{{ $package->preparation?->jalur_prioritas ?? '' }}',
        metode: '{{ $package->preparation?->jalur_strategis ?? 'Negosiasi Harga' }}'
     }">

    {{-- HEADER PAKET: Ringkasan Identitas & Pagu --}}
    <div class="glass-card p-8 rounded-[2.5rem] border border-slate-700 bg-gradient-to-r from-slate-900 to-slate-800 relative overflow-hidden mb-8">
        <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between md:items-start gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full bg-cyan-900/50 border border-cyan-700 text-[10px] font-bold uppercase text-cyan-400 tracking-wider">{{ $package->jenis_pengadaan }}</span>
                    <span class="px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-[10px] font-bold uppercase text-slate-400 tracking-wider">{{ $package->metode_pemilihan }}</span>
                </div>
                <h1 class="text-3xl font-black text-white leading-tight mb-2">{{ $package->nama_paket }}</h1>
                <p class="text-slate-400 text-sm flex items-center gap-2"><i class="fas fa-history text-slate-500"></i> Status: Dokumen 1 Ver. {{ $package->perubahan_ke ?? 0 }} (Disusun: {{ $package->tanggal_penyusunan }})</p>
            </div>
            <div class="text-left md:text-right">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Pagu Pengadaan</div>
                <div class="text-3xl font-mono font-bold text-emerald-400 mb-4">Rp {{ number_format($package->pagu_paket, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- TAB NAVIGATION --}}
    <div class="flex flex-wrap gap-2 mb-8 border-b border-slate-800 pb-1">
        <button @click="tab = 'overview'" :class="tab === 'overview' ? 'text-cyan-400 border-b-2 border-cyan-500 bg-cyan-950/30' : 'text-slate-500'" class="px-6 py-3 rounded-t-xl text-sm font-bold transition-all flex items-center gap-2"><i class="fas fa-info-circle"></i> 1. Identifikasi (Doc 1)</button>
        <button @click="tab = 'strategi'" :class="tab === 'strategi' ? 'text-cyan-400 border-b-2 border-cyan-500 bg-cyan-950/30' : 'text-slate-500'" class="px-6 py-3 rounded-t-xl text-sm font-bold transition-all flex items-center gap-2"><i class="fas fa-chess-knight"></i> 2. Strategi (Doc 2-3)</button>
        <button @click="tab = 'spek'" :class="tab === 'spek' ? 'text-cyan-400 border-b-2 border-cyan-500 bg-cyan-950/30' : 'text-slate-500'" class="px-6 py-3 rounded-t-xl text-sm font-bold transition-all flex items-center gap-2"><i class="fas fa-list-check"></i> 3. Spek & HPS (Doc 4-7)</button>
        <button @click="tab = 'kontrak'" :class="tab === 'kontrak' ? 'text-cyan-400 border-b-2 border-cyan-500 bg-cyan-950/30' : 'text-slate-500'" class="px-6 py-3 rounded-t-xl text-sm font-bold transition-all flex items-center gap-2"><i class="fas fa-file-signature"></i> 5. Kontrak (Doc 10)</button>
    </div>

    {{-- TAB 1: IDENTIFIKASI (DOC 1) --}}
    <div x-show="tab === 'overview'" class="animate-enter">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="glass-card p-6 rounded-2xl border border-slate-700 bg-slate-900/40">
                <h3 class="text-white font-bold mb-6 flex items-center justify-between">
                    <span><i class="fas fa-file-alt text-cyan-500 mr-2"></i> Resume Identifikasi (Doc 1)</span>
                    <a href="{{ route('pengadaan.edit.doc1', $package->id) }}" class="text-[10px] text-cyan-500 hover:underline font-bold uppercase tracking-widest">Edit Detail</a>
                </h3>
                <div class="space-y-4 text-xs">
                    <div class="flex justify-between border-b border-slate-800 pb-2"><span class="text-slate-500">Prioritas PDN</span><span class="text-white font-bold">Opsi {{ $package->opsi_pdn ?? '-' }}</span></div>
                    <div class="flex justify-between border-b border-slate-800 pb-2"><span class="text-slate-500">Kode KBKI</span><span class="text-white font-mono">{{ $package->kode_kbki ?? '-' }}</span></div>
                    <div class="flex justify-between border-b border-slate-800 pb-2"><span class="text-slate-500">Penyusunan</span><span class="text-white">{{ $package->tanggal_penyusunan ?? '-' }}</span></div>
                </div>
                <div class="mt-8">
                    <a href="{{ route('pengadaan.print.doc1', $package->id) }}" target="_blank" class="w-full py-4 bg-emerald-600 text-white rounded-xl text-xs font-black flex items-center justify-center gap-3 shadow-lg shadow-emerald-900/20"><i class="fas fa-file-pdf text-lg"></i> CETAK DOC 1</a>
                </div>
            </div>
            <div class="glass-card p-8 rounded-2xl border border-slate-700 flex flex-col items-center justify-center text-center bg-slate-900/20 shadow-xl">
                <div class="w-16 h-16 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-2xl mb-4 border border-emerald-500/20"><i class="fas fa-check-double"></i></div>
                <h3 class="text-white font-bold mb-2">Identifikasi Selesai</h3>
                <p class="text-slate-400 text-sm mb-6 max-w-xs">Data identifikasi sudah selaras. Lanjutkan ke strategi pengadaan (Doc 2 & 3).</p>
                <button @click="tab = 'strategi'" class="px-8 py-3 bg-cyan-600 text-white rounded-xl text-sm font-bold shadow-lg">Lanjut ke Tab Strategi <i class="fas fa-arrow-right ml-2"></i></button>
            </div>
        </div>
    </div>

    {{-- TAB 2: STRATEGI & ANALISIS (DOC 2 & 3) - GRID 2 KOLOM --}}
    <div x-show="tab === 'strategi'" class="animate-enter space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-show="!editDoc2 && !editDoc3">
            <div class="flex flex-col gap-8">
                {{-- CARD RESUME DOC 2 --}}
                <div class="glass-card p-6 rounded-2xl border border-slate-700 bg-slate-900/40 shadow-xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-white font-bold flex items-center gap-2"><i class="fas fa-chess-knight text-cyan-500"></i> Resume Strategi (Doc 2)</h3>
                        @if($package->preparation) <button @click="editDoc2 = true" class="text-[10px] text-cyan-500 font-bold uppercase underline">Edit Detail</button> @endif
                    </div>
                    @if($package->preparation)
                        <div class="space-y-4 text-xs">
                            <div class="flex justify-between border-b border-slate-800 pb-2"><span class="text-slate-500 font-bold uppercase tracking-tighter">Jalur Prioritas</span><span class="text-white font-bold">Prioritas {{ $package->preparation->jalur_prioritas }}</span></div>
                            <div class="flex justify-between border-b border-slate-800 pb-2"><span class="text-slate-500 font-bold uppercase tracking-tighter">Metode Final</span><span class="text-emerald-400 font-bold uppercase">{{ $package->preparation->jalur_strategis }}</span></div>
                        </div>
                        <div class="mt-6"><a href="{{ route('pengadaan.print.doc2', $package->id) }}" target="_blank" class="w-full py-3 bg-emerald-600 text-white rounded-xl text-[10px] font-black flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/20"><i class="fas fa-file-pdf"></i> CETAK DOC 2 (STRATEGI)</a></div>
                    @else
                        <button @click="editDoc2 = true" class="w-full py-4 border-2 border-dashed border-slate-800 text-slate-500 rounded-xl text-[10px] font-bold uppercase">Mulai Input Strategi (Doc 2)</button>
                    @endif
                </div>
                {{-- CARD RESUME DOC 3 --}}
                <div class="glass-card p-6 rounded-2xl border border-slate-700 bg-slate-900/40 shadow-xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-white font-bold flex items-center gap-2"><i class="fas fa-file-invoice text-emerald-500"></i> Resume Analisis (Doc 3)</h3>
                        @if(isset($analysis)) <button @click="editDoc3 = true" class="text-[10px] text-emerald-500 font-bold uppercase underline">Edit Detail</button> @endif
                    </div>
                    @if(isset($analysis))
                        <div class="space-y-4 text-xs">
                            <div class="flex justify-between border-b border-slate-800 pb-2"><span class="text-slate-500 font-bold uppercase tracking-tighter">Penyedia</span><span class="text-white font-bold">{{ $analysis->nama_calon_penyedia }}</span></div>
                            <div class="flex justify-between border-b border-slate-800 pb-2"><span class="text-slate-500 font-bold uppercase tracking-tighter">Harga Tayang</span><span class="text-emerald-400 font-bold">Rp {{ number_format($analysis->harga_tayang_katalog, 0, ',', '.') }}</span></div>
                        </div>
                        <div class="mt-6"><a href="{{ route('pengadaan.print.doc3', $package->id) }}" target="_blank" class="w-full py-3 bg-emerald-600 text-white rounded-xl text-[10px] font-black flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/20"><i class="fas fa-print"></i> CETAK DOC 3 (ANALISIS)</a></div>
                    @else
                        <button @click="editDoc3 = true" class="w-full py-4 border-2 border-dashed border-slate-800 text-slate-500 rounded-xl text-[10px] font-bold uppercase">Mulai Input Analisis (Doc 3)</button>
                    @endif
                </div>
            </div>
            <div class="glass-card p-8 rounded-2xl border border-slate-700 flex flex-col items-center justify-center text-center bg-slate-900/20 shadow-xl">
                @if($package->preparation && isset($analysis))
                    <div class="w-16 h-16 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-2xl mb-4 border border-emerald-500/20"><i class="fas fa-check-double"></i></div>
                    <h3 class="text-white font-bold mb-2">Tahap Strategi Selesai</h3>
                    <p class="text-slate-400 text-sm mb-6 max-w-xs">Strategi dan analisis persiapan (Doc 2 & 3) sudah diverifikasi. Lanjutkan ke penyusunan Spek & HPS.</p>
                    <button @click="tab = 'spek'" class="px-8 py-3 bg-cyan-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-cyan-900/20">Lanjut ke Tab Spek & HPS <i class="fas fa-arrow-right ml-2"></i></button>
                @else
                    <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-slate-500 text-2xl mb-4 border border-slate-700"><i class="fas fa-clock"></i></div>
                    <h3 class="text-white font-bold mb-2">Menunggu Kelengkapan</h3>
                    <p class="text-slate-400 text-sm mb-6 max-w-xs">Pastikan Dokumen 2 dan Dokumen 3 telah diisi lengkap untuk melanjutkan ke tahapan berikutnya.</p>
                @endif
            </div>
        </div>

{{-- FULL FORM DOC 2 (MUNCUL SAAT EDIT)  - }}
        <div x-show="editDoc2" class="glass-card p-10 rounded-[3rem] border border-slate-700 bg-slate-900/60 shadow-2xl relative animate-enter">
            <div class="flex justify-between items-center mb-10"><h3 class="text-xl font-black text-white tracking-tight">Analisis Strategi Pemilihan (Doc 2)</h3><button @click="editDoc2 = false" class="text-rose-500 text-2xl"><i class="fas fa-times-circle"></i></button></div>
            <form action="{{ route('pengadaan.update.strategi', $package->id) }}" method="POST" class="space-y-10">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div><label class="text-[10px] font-black text-slate-500 uppercase mb-3 block tracking-widest">1. Alasan Penetapan Metode</label><textarea name="alasan_metode" class="w-full bg-slate-950 border border-slate-700 rounded-2xl p-4 text-white text-xs outline-none focus:border-cyan-500" rows="3">{{ $package->preparation?->alasan_metode ?? '' }}</textarea></div>
                    <div><label class="text-[10px] font-black text-slate-500 uppercase mb-3 block tracking-widest">2. Kriteria Barang/Jasa</label><select name="kriteria_barang_jasa" class="w-full bg-slate-950 border border-slate-700 rounded-2xl p-4 text-white text-xs outline-none focus:border-cyan-500 appearance-none"><option value="Standar" {{ ($package->preparation?->kriteria_barang_jasa ?? '') == 'Standar' ? 'selected' : '' }}>Standar / Umum</option><option value="Kompleks" {{ ($package->preparation?->kriteria_barang_jasa ?? '') == 'Kompleks' ? 'selected' : '' }}>Kompleks / Khusus</option></select></div>
                </div>
                <div><label class="text-[10px] font-black text-cyan-500 uppercase mb-6 block tracking-widest">B. Penentuan Jalur Strategis Pengadaan</label><div class="grid grid-cols-1 gap-4">@foreach([1 => 'Jalur Wajib Regulasi', 2 => 'Jalur Utama (Strategis)', 3 => 'Jalur Pengecualian'] as $val => $txt)<label class="flex items-center gap-5 p-5 rounded-2xl border transition-all cursor-pointer group" :class="jalur == '{{ $val }}' ? 'bg-cyan-900/20 border-cyan-500 ring-1 ring-cyan-500' : 'bg-slate-950 border-slate-800 hover:border-slate-600'"><input type="radio" name="jalur_prioritas" value="{{ $val }}" x-model="jalur" class="hidden" {{ ($package->preparation?->jalur_prioritas ?? '') == $val ? 'checked' : '' }}><div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all" :class="jalur == '{{ $val }}' ? 'border-cyan-400 bg-cyan-400' : 'border-slate-700'"><div class="w-2 h-2 rounded-full bg-slate-900" x-show="jalur == '{{ $val }}'"></div></div><span class="text-white font-bold text-sm">Prioritas {{ $val }}: {{ $txt }}</span></label>@endforeach</div></div>
                <button type="submit" class="w-full py-5 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl transition-all">Simpan Analisis Strategi (Doc 2)</button>
            </form>
        </div>

        {{-- FULL FORM DOC 3 (MUNCUL SAAT EDIT) --}}
        <div x-show="editDoc3" class="glass-card p-10 rounded-[3rem] border border-slate-700 bg-slate-900/60 shadow-2xl relative animate-enter">
            <div class="flex justify-between items-center mb-8"><h3 class="text-xl font-black text-white tracking-tight">Kertas Kerja Analisis Persiapan (Doc 3)</h3><button @click="editDoc3 = false" class="text-rose-500 text-2xl"><i class="fas fa-times-circle"></i></button></div>
            <form action="{{ route('pengadaan.update.doc3', $package->id) }}" method="POST" class="space-y-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-slate-950/40 rounded-3xl border border-slate-800 shadow-inner">
                    <div class="md:col-span-1"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Nama Calon Penyedia</label><input type="text" name="nama_calon_penyedia" value="{{ $analysis->nama_calon_penyedia ?? '' }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white text-xs"></div>
                    <div><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">ID Produk Katalog</label><input type="text" name="produk_katalog" value="{{ $analysis->produk_katalog ?? '' }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white text-xs"></div>
                    <div><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Harga Tayang (Rp)</label><input type="number" name="harga_tayang_katalog" value="{{ $analysis->harga_tayang_katalog ?? '' }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-emerald-400 font-black"></div>
                </div>
                {{-- Evaluasi Detail Doc 3 --}}
                <div class="space-y-4"><label class="text-[10px] text-cyan-500 font-black uppercase tracking-widest block">Evaluasi Kertas Kerja </label><div class="space-y-2">@foreach(['Teknis' => 'eval_teknis', 'Harga' => 'eval_harga'] as $label => $name)<div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center p-3 bg-slate-900/40 rounded-xl border border-slate-800/50"><div class="md:col-span-6 text-xs text-slate-300">Kesesuaian Spesifikasi {{ $label }}</div><div class="md:col-span-6"><select name="{{ $name }}[status]" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"><option value="Sesuai">Sesuai/Wajar</option><option value="Tidak">Tidak Sesuai</option></select></div></div>@endforeach</div></div>
                <button type="submit" class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl transition-all">Simpan Kertas Kerja (Doc 3)</button>
            </form>
        </div>
    </div>

    {{-- TAB 3: SPEK & HPS (DOC 4-7) --}}
    <div x-show="tab === 'spek'" class="animate-enter space-y-8">
        {{-- Grup Tombol Cetak --}}
        <div class="flex flex-wrap gap-3 p-4 bg-slate-900/50 rounded-2xl border border-slate-800">
            <a href="{{ route('pengadaan.print.doc4', $package->id) }}" target="_blank" class="px-6 py-3 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase"><i class="fas fa-print mr-1"></i> Cetak Doc 4</a>
            <a href="{{ route('pengadaan.print.doc5', $package->id) }}" target="_blank" class="px-6 py-3 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase"><i class="fas fa-print mr-1"></i> Cetak Doc 5</a>
            <a href="{{ route('pengadaan.print.doc6', $package->id) }}" target="_blank" class="px-6 py-3 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase shadow-lg shadow-emerald-900/20"><i class="fas fa-file-pdf mr-1"></i> Cetak Doc 6 (Analisis Harga)</a>
        </div>

        {{-- FORM DOC 4 & 5 (BULK UPDATE)  --}}
        <form action="{{ route('pengadaan.update.items_bulk', $package->id) }}" method="POST" class="space-y-6">
            @csrf
            <div class="glass-card p-8 rounded-[2rem] border border-slate-700 bg-slate-900/40">
                <h3 class="text-white font-bold mb-8 flex items-center gap-2"><i class="fas fa-list-check text-cyan-500"></i> Detail Spesifikasi Teknis (Doc 4 & 5)</h3>
                <div class="space-y-12">
                    @foreach($package->items as $item)
                    <div class="relative p-6 rounded-3xl bg-slate-950/50 border border-slate-800">
                        <div class="absolute -top-3 left-6 px-4 py-1 bg-cyan-600 text-white text-[10px] font-black rounded-full uppercase italic">Item #{{ $loop->iteration }}: {{ $item->nama_item }}</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div class="md:col-span-2 flex justify-between items-end gap-4"><div class="flex-1"><label class="text-[9px] text-cyan-500 uppercase font-black mb-1 block">Link E-Katalog (Doc 4)</label><input type="text" name="items[{{ $item->id }}][link_produk_katalog]" value="{{ $item->link_produk_katalog }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-cyan-400"></div><a href="https://katalog.inaproc.id/search?keyword={{ urlencode($item->merk_tipe) }}" target="_blank" class="mb-1 px-4 py-3 bg-slate-800 text-cyan-400 border border-cyan-500/30 rounded-xl text-[10px] font-black hover:bg-cyan-600 transition-all"><i class="fas fa-search mr-1"></i> Cari Inaproc</a></div>
                            <div><label class="text-[9px] text-slate-500 uppercase font-black mb-1 block">Merk / Tipe (Doc 4)</label><input type="text" name="items[{{ $item->id }}][merk_tipe]" value="{{ $item->merk_tipe }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-white"></div>
                            <div><label class="text-[9px] text-slate-500 uppercase font-black mb-1 block">Standar Mutu / TKDN (Doc 4)</label><input type="text" name="items[{{ $item->id }}][standar_mutu]" value="{{ $item->standar_mutu }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-white"></div>
                            <div><label class="text-[9px] text-amber-500 uppercase font-black mb-1 block">Masa Garansi (Doc 5 Bagian 2)</label><input type="text" name="items[{{ $item->id }}][masa_garansi]" value="{{ $item->masa_garansi }}" class="w-full bg-slate-900 border border-amber-900/50 rounded-xl p-3 text-xs text-white"></div>
                            <div><label class="text-[9px] text-amber-500 uppercase font-black mb-1 block">Aspek Pemeliharaan (Doc 5 Bagian 6)</label><input type="text" name="items[{{ $item->id }}][aspek_pemeliharaan]" value="{{ $item->aspek_pemeliharaan }}" class="w-full bg-slate-900 border border-amber-900/50 rounded-xl p-3 text-xs text-white"></div>
                            <div class="md:col-span-2"><label class="text-[9px] text-slate-500 uppercase font-black mb-1 block">Fungsi & Kinerja (Doc 5 Bagian 3)</label><textarea name="items[{{ $item->id }}][fungsi_kinerja]" rows="2" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-white">{{ $item->fungsi_kinerja }}</textarea></div>
                            <div class="md:col-span-2"><label class="text-[9px] text-slate-500 uppercase font-black mb-1 block">Uraian Spesifikasi Detail (Doc 4)</label><textarea name="items[{{ $item->id }}][deskripsi_spesifikasi]" rows="3" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-white">{{ $item->deskripsi_spesifikasi }}</textarea></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="submit" class="w-full mt-8 py-5 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl transition-all">Simpan Semua Spek (Doc 4 & 5)</button>
            </div>
        </form>

        {{-- FORM ANALISIS HARGA (DOC 6) --}}
        <div class="glass-card p-8 rounded-[2rem] border border-slate-700 bg-slate-900/40" x-data="{ type: 'market', searchKey: '' }">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div><h3 class="text-white font-bold flex items-center gap-2"><i class="fas fa-search-dollar text-emerald-500"></i> Analisis Referensi Harga (Doc 6)</h3><p class="text-[10px] text-slate-500 uppercase tracking-widest mt-1 italic">Survei Pasar & Justifikasi Harga</p></div>
                <div class="flex bg-slate-950 p-1 rounded-xl border border-slate-800 shadow-inner">
                    @foreach(['qualitative' => 'A.1 Kualitatif', 'market' => 'A.2 Pasar', 'sbu' => 'B. SBU', 'contract' => 'C. Kontrak'] as $k => $v)
                    <button @click="type = '{{ $k }}'" :class="type === '{{ $k }}' ? 'bg-cyan-600 text-white shadow-lg' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase transition-all flex items-center gap-2">{{ $v }} @php $count = $package->price_references->where('type', $k)->count(); @endphp @if($count > 0) <span class="bg-white/20 px-1.5 py-0.5 rounded text-[8px]">{{ $count }}</span> @endif</button>
                    @endforeach
                </div>
            </div>

            {{-- Smart Search --}}
            <div class="mb-8 p-6 bg-slate-950/60 rounded-3xl border border-slate-800 shadow-inner"><label class="text-[9px] text-emerald-500 uppercase font-black mb-3 block tracking-widest italic">Smart Search Helper</label><div class="flex flex-col md:flex-row gap-4"><input type="text" x-model="searchKey" placeholder="Ketik nama produk..." class="flex-1 bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs outline-none focus:border-cyan-500 transition-all"><div class="flex gap-2"><a :href="'https://katalog.inaproc.id/search?q=' + encodeURIComponent(searchKey)" target="_blank" class="px-4 py-3 bg-slate-800 text-cyan-400 border border-cyan-500/30 rounded-xl text-[9px] font-black hover:bg-cyan-600 transition-all"><i class="fas fa-search"></i> LKPP</a><a :href="'https://www.tokopedia.com/search?q=' + encodeURIComponent(searchKey)" target="_blank" class="px-4 py-3 bg-slate-800 text-emerald-400 border border-emerald-500/30 rounded-xl text-[9px] font-black hover:bg-emerald-600 transition-all"><i class="fas fa-shopping-bag"></i> TOKOPEDIA</a></div></div></div>

            {{-- Form Input Doc 6 --}}
            <form action="{{ route('pengadaan.store.price_ref', $package->id) }}" method="POST" enctype="multipart/form-data" class="p-8 bg-slate-950 rounded-3xl border border-slate-800 mb-8 shadow-2xl">
                @csrf <input type="hidden" name="type" :value="type">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div x-show="type === 'qualitative' || type === 'market'"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Merek & Model</label><input type="text" name="merek_model" :value="searchKey" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs"></div>
                    <div><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Sumber / Toko / Nama Dokumen</label><input type="text" name="sumber_nama" placeholder="Contoh: Tokopedia / Pergub 88" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs"></div>
                    <div x-show="type === 'qualitative' || type === 'market'"><label class="text-[9px] text-cyan-500 uppercase font-black mb-2 block tracking-widest italic">Tautan URL Aktif</label><input type="text" name="link_url" placeholder="https://..." class="w-full bg-slate-900 border border-cyan-900/30 rounded-xl p-3 text-cyan-400 text-xs"></div>
                    <div x-show="type !== 'qualitative'"><label class="text-[9px] text-emerald-500 uppercase font-black mb-2 block tracking-widest italic">Harga Satuan (Rp)</label><input type="number" name="harga_satuan" class="w-full bg-slate-900 border border-emerald-900/50 rounded-xl p-3 text-emerald-400 font-black text-xs"></div>
                    
                    {{-- FIXED: UPLOAD BUKTI UNTUK SEMUA TAB --}}
                    <div><label class="text-[9px] text-amber-500 uppercase font-black mb-2 block tracking-widest italic">Unggah Screenshot Bukti (SS)</label><input type="file" name="file_bukti" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2.5 text-slate-400 text-[10px] file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-cyan-600 file:text-white cursor-pointer shadow-lg"></div>

                    <div x-show="type === 'sbu'"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Nomor/Tgl Dokumen</label><input type="text" name="nomor_tanggal_dok" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs"></div>
                    <div x-show="type === 'sbu'"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Catatan Relevansi</label><input type="text" name="catatan_relevansi" placeholder="Misal: Sebagai batas atas..." class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs"></div>
                    <div x-show="type === 'contract'"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Tahun Anggaran</label><input type="text" name="tahun_anggaran" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs"></div>
                    <div x-show="type === 'contract'"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Penyesuaian (Inflasi/Spek)</label><input type="text" name="catatan_penyesuaian" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs"></div>
                    <div x-show="type === 'qualitative'" class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4"><textarea name="kelebihan" placeholder="Ringkasan Kelebihan Utama..." class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs" rows="2"></textarea><textarea name="kekurangan" placeholder="Ringkasan Kekurangan Utama..." class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs" rows="2"></textarea></div>
                    <div x-show="type === 'market'"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Garansi & Layanan Purna Jual</label><input type="text" name="garansi_layanan" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs"></div>

                    <div class="lg:col-span-3 flex justify-end pt-4 border-t border-slate-800"><button type="submit" class="bg-emerald-600 text-white px-10 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-emerald-900/30 italic"><i class="fas fa-plus-circle mr-2"></i> Tambah Referensi <span x-text="type"></span></button></div>
                </div>
            </form>

            {{-- Tabel Data Tersimpan--}}
            <div class="mt-8"><h4 class="text-white text-[10px] font-black uppercase mb-4 tracking-widest flex items-center justify-between"><span><i class="fas fa-list text-cyan-500"></i> Daftar Bukti Survei Terdata</span></h4><div class="overflow-x-auto rounded-2xl border border-slate-800 shadow-2xl"><table class="w-full text-left"><thead class="bg-slate-900"><tr><th class="p-4 text-[9px] text-slate-500 uppercase font-black tracking-widest italic">Tipe</th><th class="p-4 text-[9px] text-slate-500 uppercase font-black tracking-widest italic">Merek / Detail</th><th class="p-4 text-[9px] text-slate-500 uppercase font-black text-center tracking-widest italic">Bukti SS</th><th class="p-4 text-[9px] text-slate-500 uppercase font-black text-right tracking-widest italic">Harga Satuan</th><th class="p-4 text-[9px] text-slate-500 uppercase font-black text-center tracking-widest italic">Aksi</th></tr></thead><tbody class="bg-slate-950/50 text-xs">@forelse($package->price_references as $ref)<tr class="border-t border-slate-900 hover:bg-slate-800/20 transition-all"><td class="p-4"><span class="px-2 py-0.5 rounded bg-slate-800 text-cyan-400 text-[8px] font-black uppercase">{{ $ref->type }}</span></td><td class="p-4"><div class="text-white font-bold">{{ $ref->merek_model ?? $ref->sumber_nama }}</div><div class="text-[9px] text-slate-500 italic">{{ $ref->sumber_nama }}</div></td><td class="p-4 text-center">@if($ref->file_bukti)<a href="{{ asset('storage/bukti_harga/' . $ref->file_bukti) }}" target="_blank" class="text-emerald-400 hover:text-emerald-300"><i class="fas fa-image text-lg"></i></a>@else <span class="text-slate-700">-</span> @endif</td><td class="p-4 text-right font-mono font-bold text-emerald-400 italic">Rp {{ number_format($ref->harga_satuan, 0, ',', '.') }}</td><td class="p-4 text-center"><form action="{{ route('pengadaan.destroy.price_ref', $ref->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-400 transition-colors p-2"><i class="fas fa-trash"></i></button></form></td></tr>@empty<tr><td colspan="5" class="p-8 text-center text-slate-600 italic">Belum ada data referensi.</td></tr>@endforelse</tbody></table></div></div>

            {{-- Justifikasi Akhir & Unggah PDF --}}
            <form action="{{ route('pengadaan.update.price_justification', $package->id) }}" method="POST" enctype="multipart/form-data" class="mt-12 p-8 bg-slate-950/40 rounded-[2.5rem] border border-slate-800 shadow-2xl">@csrf <h4 class="text-white text-[11px] font-black uppercase mb-6 tracking-widest flex items-center gap-2 italic"><i class="fas fa-gavel text-amber-500"></i> Justifikasi Akhir & Lampiran Utama</h4><div class="grid grid-cols-1 md:grid-cols-2 gap-8"><div class="md:col-span-2"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Narasi Kesimpulan Kewajaran Harga</label><textarea name="kesimpulan_analisis_harga" rows="3" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-white text-xs outline-none focus:border-amber-500 transition-all">{{ $package->kesimpulan_analisis_harga }}</textarea></div><div><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Unggah Dokumen SBU (PDF)</label><div class="flex items-center gap-3"><input type="file" name="file_sbu" class="flex-1 bg-slate-900 border border-slate-700 rounded-xl p-2 text-xs text-slate-400 file:bg-slate-800 file:text-white file:border-0 cursor-pointer">@if($package->file_sbu) <a href="{{ asset('storage/dokumen_pendukung/' . $package->file_sbu) }}" target="_blank" class="text-emerald-400"><i class="fas fa-file-pdf text-2xl"></i></a> @endif</div></div><div><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest italic">Unggah Salinan Kontrak Terdahulu (PDF)</label><div class="flex items-center gap-3"><input type="file" name="file_kontrak_lama" class="flex-1 bg-slate-900 border border-slate-700 rounded-xl p-2 text-xs text-slate-400 file:bg-slate-800 file:text-white file:border-0 cursor-pointer">@if($package->file_kontrak_lama) <a href="{{ asset('storage/dokumen_pendukung/' . $package->file_kontrak_lama) }}" target="_blank" class="text-emerald-400"><i class="fas fa-file-pdf text-2xl"></i></a> @endif</div></div></div><div class="mt-8 flex items-center justify-between p-4 bg-amber-950/20 border border-amber-900/30 rounded-2xl"><p class="text-[8px] text-amber-500 italic max-w-lg uppercase font-bold tracking-tighter leading-tight">Disclaimer: Dokumen ini disusun untuk tujuan internal persiapan e-purchasing.</p><button type="submit" class="px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl">Simpan Justifikasi Akhir</button></div></form>

            {{-- Ringkasan Harga --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12"><div class="p-6 bg-slate-950 rounded-2xl border border-slate-800 shadow-inner group transition-all hover:border-emerald-500/30"><span class="text-[9px] text-slate-500 uppercase font-black block mb-2 tracking-tighter italic">Harga Terendah</span><div class="text-2xl font-mono font-bold text-white group-hover:text-emerald-400 transition-colors">Rp {{ number_format($package->hps_terendah ?? 0, 0, ',', '.') }}</div></div><div class="p-6 bg-slate-950 rounded-2xl border border-slate-800 shadow-inner group transition-all hover:border-emerald-500/30"><span class="text-[9px] text-slate-500 uppercase font-black block mb-2 tracking-tighter italic">Harga Tertinggi</span><div class="text-2xl font-mono font-bold text-white group-hover:text-emerald-400 transition-colors">Rp {{ number_format($package->hps_tertinggi ?? 0, 0, ',', '.') }}</div></div><div class="p-6 bg-emerald-900/20 rounded-2xl border border-emerald-500/30 shadow-inner group transition-all"><span class="text-[9px] text-emerald-500 uppercase font-black block mb-2 tracking-tighter italic">Rata-Rata Pembanding</span><div class="text-2xl font-mono font-bold text-emerald-400">Rp {{ number_format($package->hps_hitung_rata_rata ?? 0, 0, ',', '.') }}</div></div></div>
        </div>
    </div>

    {{-- 2. PEMBARUAN KONTEN TAB KONTRAK (DOC 10) --}}
    <div x-show="tab === 'kontrak'" class="animate-enter">
        @php 
            // Logika Deteksi Pemenang: Cek Survei Pasar Doc 6 atau Kontrak yang ada
            $marketRef = $package->price_references->where('type', 'market')->first();
            $contractData = $package->contract;
        @endphp

        @if(!$marketRef && !$contractData)
            {{-- Pesan Jika Survei Pasar Doc 6 Belum Diisi --}}
            <div class="glass-card p-12 text-center border border-rose-500/20 bg-rose-950/5 rounded-[2rem]">
                <div class="w-16 h-16 rounded-full bg-rose-900/30 flex items-center justify-center text-rose-500 text-2xl mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="text-white font-bold mb-2">Referensi Harga Pasar Belum Tersedia</h3>
                <p class="text-slate-400 text-sm max-w-sm mx-auto">Harap lengkapi survei harga pasar pada <strong>Tab Spek & HPS (Doc 6 A.2)</strong> untuk menentukan calon penyedia dan harga kontrak sebelum menerbitkan Surat Pesanan.</p>
            </div>
        @else
            <div class="glass-card p-8 rounded-[2.5rem] border border-slate-700 bg-slate-900/40 shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20"><i class="fas fa-file-signature text-xl"></i></div>
                        <div>
                            <h3 class="text-white font-bold">Penerbitan Surat Pesanan (Doc 10)</h3>
                            <p class="text-slate-500 text-xs mt-1 italic">Finalisasi detail kontrak berdasarkan harga wajar dari Doc 6.</p>
                        </div>
                    </div>
                    <a href="{{ route('pengadaan.print.doc10', $package->id) }}" target="_blank" class="px-6 py-3 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase shadow-lg shadow-emerald-900/20 transition-all hover:scale-105"><i class="fas fa-print mr-2"></i> CETAK DOC 10</a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Form Input Detail Kontrak Baru --}}
                    <div class="lg:col-span-2">
                        <form action="{{ route('pengadaan.store.contract', $package->id) }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Nomor Surat Pesanan</label>
                                    <input type="text" name="nomor_sp" value="{{ $contractData->nomor_sp ?? '' }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white focus:border-cyan-500 outline-none" placeholder="Contoh: 027/001/SP/KOMINFO/2026">
                                </div>
                                <div>
                                    <label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Tanggal Surat Pesanan</label>
                                    <input type="date" name="tanggal_sp" value="{{ $contractData->tanggal_sp ?? date('Y-m-d') }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white focus:border-cyan-500 outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[9px] text-cyan-500 uppercase font-black mb-2 block tracking-widest">Alamat Penyerahan / Lokasi Pekerjaan</label>
                                    <textarea name="alamat_penyerahan" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white focus:border-cyan-500 outline-none" placeholder="Tuliskan alamat lengkap lokasi pengiriman barang...">{{ $contractData->alamat_penyerahan ?? 'Dinas Komunikasi dan Informatika Prov. Kalbar' }}</textarea>
                                </div>
                                <div>
                                    <label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Waktu Penyelesaian (Hari)</label>
                                    <input type="number" name="waktu_penyelesaian" value="{{ $contractData->waktu_penyelesaian ?? 30 }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white">
                                </div>
                                <div>
                                    <label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Sumber Dana</label>
                                    <input type="text" name="sumber_dana" value="{{ $contractData->sumber_dana ?? 'APBD Provinsi Kalimantan Barat TA 2026' }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white">
                                </div>
                                <div class="md:col-span-2">
    <label class="text-[9px] text-cyan-500 uppercase font-black mb-2 block tracking-widest">Pilih Vendor Pemenang</label>
    <select name="vendor_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white focus:border-cyan-500 outline-none">
        <option value="">-- Pilih Vendor Terdaftar --</option>
        @foreach($vendors as $v)
            <option value="{{ $v->id }}" {{ (isset($contractData) && $contractData->vendor_id == $v->id) ? 'selected' : '' }}>
                {{ $v->nama_perusahaan }} ({{ $v->bentuk_usaha }})
            </option>
        @endforeach
    </select>
    <p class="text-[9px] text-slate-500 mt-2 italic">*Vendor ini akan muncul sebagai pihak kedua dalam Surat Pesanan.</p>
</div>
                            </div>
                            

                            <div class="p-6 bg-slate-950/60 rounded-3xl border border-slate-800 flex justify-between items-center shadow-inner">
                                <div>
                                    <div class="text-[9px] text-emerald-500 font-black uppercase mb-1">Nilai Kontrak Final (Berdasarkan Doc 6)</div>
                                    <div class="text-3xl font-mono font-bold text-white">Rp {{ number_format($contractData->nilai_kontrak_final ?? ($marketRef->harga_satuan ?? 0), 0, ',', '.') }}</div>
                                </div>
                                <button type="submit" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl transition-all">Simpan & Finalisasi Kontrak</button>
                            </div>
                        </form>
                    </div>
                    
                    {{-- Ringkasan Pemenang --}}
                    <div class="lg:col-span-1">
                        <div class="p-8 rounded-[2rem] bg-slate-950 border border-slate-800 h-full shadow-inner relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl -mr-8 -mt-8"></div>
                            <h4 class="text-slate-500 text-[10px] font-black uppercase mb-6 tracking-widest italic">Identitas Calon Penyedia</h4>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 rounded-full bg-indigo-900/50 flex items-center justify-center text-indigo-400 border border-indigo-700 shadow-lg"><i class="fas fa-building"></i></div>
                                <div>
                                    <div class="text-white font-bold text-base leading-tight">{{ $contractData->vendor->nama_perusahaan ?? ($marketRef->sumber_nama ?? 'Penyedia Belum Dipilih') }}</div>
                                    <div class="text-[9px] text-slate-500 mt-1 uppercase font-bold tracking-tighter">Status: Calon Terpilih</div>
                                </div>
                            </div>
                            <div class="space-y-4 pt-6 border-t border-slate-800/50">
                                <div class="flex justify-between items-center">
                                    <span class="text-[9px] text-slate-600 font-black uppercase">Merek Produk</span>
                                    <span class="text-[10px] text-white font-bold">{{ $marketRef->merek_model ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[9px] text-slate-600 font-black uppercase">Jaminan Pelaksanaan</span>
                                    <span class="px-2 py-0.5 rounded bg-emerald-900/30 text-emerald-400 text-[8px] font-black uppercase italic">Tidak Wajib (< 200jt)</span>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection