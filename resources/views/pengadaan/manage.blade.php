@extends('pengadaan.layout')

@section('title', 'Kelola Paket Pengadaan')
@section('header_title', 'Manajemen Paket Pengadaan')

@section('content')
{{-- Update x-data di bagian atas file manage.blade.php --}}
<div class="max-w-7xl mx-auto" x-data="{ 
        tab: '{{ session('tab', 'overview') }}', 
        editDoc2: false, 
        editDoc3: false,
        editDoc45: false,
        editDoc6: false,
        editDoc10: false, {{-- Tambahkan state baru ini --}}
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

{{-- TAB 2: STRATEGI & ANALISIS (DOC 2 & 3) --}}
<div x-show="tab === 'strategi'" class="animate-enter space-y-8">
    
    {{-- 1. GRID UTAMA: RESUME CARD (Muncul jika tidak sedang edit) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-show="!editDoc2 && !editDoc3">
        <div class="flex flex-col gap-8">
            {{-- CARD RESUME DOC 2 --}}
            <div class="glass-card p-6 rounded-2xl border border-slate-700 bg-slate-900/40 shadow-xl transition-all">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-chess-knight text-cyan-500"></i> Resume Strategi (Doc 2)
                    </h3>
                    @if($package->preparation) 
                        <button @click="editDoc2 = true" class="text-[10px] text-cyan-500 font-bold uppercase underline hover:text-cyan-400">Edit Detail</button> 
                    @endif
                </div>

                @if($package->preparation)
                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between border-b border-slate-800 pb-2">
                            <span class="text-slate-500 font-bold uppercase tracking-tighter">Jalur Prioritas</span>
                            <span class="text-white font-bold">{{ $package->preparation->jalur_prioritas }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800 pb-2">
                            <span class="text-slate-500 font-bold uppercase tracking-tighter">Metode Final</span>
                            <span class="text-emerald-400 font-bold uppercase">{{ $package->preparation->jalur_strategis }}</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('pengadaan.print.doc2', $package->id) }}" target="_blank" 
                           class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-[10px] font-black flex items-center justify-center gap-2 transition-all">
                            <i class="fas fa-file-pdf"></i> CETAK DOC 2 (STRATEGI)
                        </a>
                    </div>
                @else
                    <button @click="editDoc2 = true" class="w-full py-8 border-2 border-dashed border-slate-800 text-slate-500 rounded-2xl text-[10px] font-bold uppercase hover:border-cyan-500/50 hover:text-cyan-500 transition-all group">
                        <i class="fas fa-plus-circle mb-2 text-xl block group-hover:scale-110 transition-transform"></i>
                        Mulai Input Strategi (Doc 2)
                    </button>
                @endif
            </div>

            {{-- CARD RESUME DOC 3 --}}
            <div class="glass-card p-6 rounded-2xl border border-slate-700 bg-slate-900/40 shadow-xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-file-invoice text-emerald-500"></i> Resume Analisis (Doc 3)
                    </h3>
                    @if(isset($analysis)) 
                        <button @click="editDoc3 = true" class="text-[10px] text-emerald-500 font-bold uppercase underline hover:text-emerald-400">Edit Detail</button> 
                    @endif
                </div>

                @if(isset($analysis))
                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between border-b border-slate-800 pb-2">
                            <span class="text-slate-500 font-bold uppercase tracking-tighter">Penyedia</span>
                            <span class="text-white font-bold">{{ $analysis->nama_calon_penyedia }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800 pb-2">
                            <span class="text-slate-500 font-bold uppercase tracking-tighter">Harga Tayang</span>
                            <span class="text-emerald-400 font-bold">Rp {{ number_format($analysis->harga_tayang_katalog, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('pengadaan.print.doc3', $package->id) }}" target="_blank" 
                           class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-[10px] font-black flex items-center justify-center gap-2 transition-all">
                            <i class="fas fa-print"></i> CETAK DOC 3 (ANALISIS)
                        </a>
                    </div>
                @else
                    <button @click="editDoc3 = true" class="w-full py-8 border-2 border-dashed border-slate-800 text-slate-500 rounded-2xl text-[10px] font-bold uppercase hover:border-emerald-500/50 hover:text-emerald-500 transition-all group">
                        <i class="fas fa-search-dollar mb-2 text-xl block group-hover:scale-110 transition-transform"></i>
                        Mulai Input Analisis (Doc 3)
                    </button>
                @endif
            </div>
        </div>

        {{-- KOLOM KANAN: STATUS PENYELESAIAN --}}
        <div class="glass-card p-8 rounded-2xl border border-slate-700 flex flex-col items-center justify-center text-center bg-slate-900/20 shadow-xl">
            @if($package->preparation && isset($analysis))
                <div class="w-20 h-20 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-3xl mb-6 border border-emerald-500/20 animate-pulse">
                    <i class="fas fa-check-double"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Tahap Strategi Selesai</h3>
                <p class="text-slate-400 text-sm mb-8 max-w-xs">Strategi dan analisis persiapan sudah diverifikasi. Anda dapat melanjutkan ke penyusunan Spek & HPS.</p>
                <button @click="tab = 'spek'" class="px-10 py-4 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-cyan-900/40 hover:scale-105 transition-all">
                    Lanjut ke Tab Spek & HPS <i class="fas fa-arrow-right ml-2"></i>
                </button>
            @else
                <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-slate-500 text-2xl mb-4 border border-slate-700">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="text-white font-bold mb-2">Menunggu Kelengkapan</h3>
                <p class="text-slate-400 text-sm max-w-xs">Pastikan Dokumen 2 dan Dokumen 3 telah diisi lengkap untuk melanjutkan ke tahapan berikutnya.</p>
            @endif
        </div>
    </div>

    {{-- 2. FULL FORM DOC 2 (MUNCUL SAAT EDIT) --}}
    <div x-show="editDoc2" class="glass-card p-10 rounded-[3rem] border border-slate-700 bg-slate-900/60 shadow-2xl relative animate-enter">
        <div class="flex justify-between items-center mb-10">
            <h3 class="text-xl font-black text-white tracking-tight uppercase">Analisis Strategi Pemilihan (Doc 2)</h3>
            <button @click="editDoc2 = false" class="text-rose-500 hover:text-rose-400 text-2xl transition-all">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>
        
        <form action="{{ route('pengadaan.update.strategi', $package->id) }}" method="POST" class="space-y-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block">1. Alasan Penetapan Metode</label>
                    <textarea name="alasan_metode" class="w-full bg-slate-950 border border-slate-700 rounded-2xl p-4 text-white text-xs outline-none focus:border-cyan-500 transition-all" rows="3">{{ $package->preparation?->alasan_metode ?? 'Metode E-Purchasing dipilih karena item tersedia dalam Katalog Elektronik dengan harga kompetitif.' }}</textarea>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block">2. Kriteria Barang/Jasa</label>
                    <select name="kriteria_barang_jasa" class="w-full bg-slate-950 border border-slate-700 rounded-2xl p-4 text-white text-xs outline-none focus:border-cyan-500 appearance-none">
                        <option value="Standar" {{ ($package->preparation?->kriteria_barang_jasa ?? '') == 'Standar' ? 'selected' : '' }}>Standar / Umum</option>
                        <option value="Kompleks" {{ ($package->preparation?->kriteria_barang_jasa ?? '') == 'Kompleks' ? 'selected' : '' }}>Kompleks / Khusus</option>
                    </select>
                </div>
            </div>

            <div class="space-y-6">
                <label class="text-[10px] font-black text-cyan-500 uppercase tracking-widest block">B. Penentuan Jalur Strategis Pengadaan</label>
                <div class="grid grid-cols-1 gap-4">
                    @foreach([
                        'Prioritas 1: Jalur Wajib Regulasi' => 'Produk Dalam Negeri (PDN)',
                        'Prioritas 2: Jalur Utama (Strategis)' => 'Produk Impor',
                        'Prioritas 3: Jalur Pengecualian' => 'Produk Pengecualian'
                    ] as $label => $val)
                        <label class="flex items-center gap-5 p-5 rounded-2xl border transition-all cursor-pointer group" 
                               :class="jalur == '{{ $val }}' ? 'bg-cyan-900/20 border-cyan-500 ring-1 ring-cyan-500' : 'bg-slate-950 border-slate-800 hover:border-slate-600'">
                            <input type="radio" name="jalur_prioritas" value="{{ $val }}" x-model="jalur" class="hidden" 
                                   {{ ($package->preparation?->jalur_prioritas ?? '') == $val ? 'checked' : '' }}>
                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all" 
                                 :class="jalur == '{{ $val }}' ? 'border-cyan-400 bg-cyan-400' : 'border-slate-700'">
                                <div class="w-2 h-2 rounded-full bg-slate-900" x-show="jalur == '{{ $val }}'"></div>
                            </div>
                            <span class="text-white font-bold text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block">Metode Pemilihan</label>
                    <select name="jalur_strategis" class="w-full bg-slate-950 border border-slate-700 rounded-2xl p-4 text-white text-xs outline-none focus:border-cyan-500 appearance-none">
                        <option value="Negosiasi" {{ ($package->preparation?->jalur_strategis ?? '') == 'Negosiasi' ? 'selected' : '' }}>Negosiasi Harga</option>
                        <option value="Mini Kompetisi" {{ ($package->preparation?->jalur_strategis ?? '') == 'Mini Kompetisi' ? 'selected' : '' }}>Mini Kompetisi</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block">Justifikasi Pilihan</label>
                    <textarea name="justifikasi_pilihan" class="w-full bg-slate-950 border border-slate-700 rounded-2xl p-4 text-white text-xs outline-none focus:border-cyan-500 transition-all" rows="2">{{ $package->preparation?->justifikasi_pilihan ?? '' }}</textarea>
                </div>
            </div>

            <button type="submit" class="w-full py-5 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-xl hover:shadow-cyan-900/40 transition-all">
                Simpan Analisis Strategi (Doc 2)
            </button>
        </form>
    </div>

    {{-- 3. FULL FORM DOC 3 (MUNCUL SAAT EDIT) --}}
    <div x-show="editDoc3" class="glass-card p-10 rounded-[3rem] border border-slate-700 bg-slate-900/60 shadow-2xl relative animate-enter">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-black text-white tracking-tight uppercase">Kertas Kerja Analisis Persiapan (Doc 3)</h3>
            <button @click="editDoc3 = false" class="text-rose-500 hover:text-rose-400 text-2xl transition-all">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>

        <form action="{{ route('pengadaan.update.doc3', $package->id) }}" method="POST" class="space-y-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-8 bg-slate-950/40 rounded-[2rem] border border-slate-800 shadow-inner">
                <div class="space-y-2">
                    <label class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Nama Calon Penyedia</label>
                    <input type="text" name="nama_calon_penyedia" value="{{ $analysis->nama_calon_penyedia ?? '' }}" 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white text-xs focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[9px] text-slate-500 uppercase font-black tracking-widest">ID Produk Katalog</label>
                    <input type="text" name="produk_katalog" value="{{ $analysis->produk_katalog ?? '' }}" 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white text-xs focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Harga Tayang (Rp)</label>
                    <input type="number" name="harga_tayang_katalog" value="{{ $analysis->harga_tayang_katalog ?? '' }}" 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-emerald-400 font-black focus:border-emerald-500 outline-none transition-all">
                </div>
            </div>

            <div class="space-y-6">
                <label class="text-[10px] text-cyan-500 font-black uppercase tracking-widest block">Evaluasi Kelayakan Produk Katalog</label>
                <div class="space-y-3">
                    @foreach(['Teknis' => 'eval_teknis', 'Harga' => 'eval_harga'] as $label => $name)
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center p-4 bg-slate-900/40 rounded-2xl border border-slate-800/50">
                            <div class="md:col-span-6 text-xs text-slate-300">Apakah Aspek **{{ $label }}** Sudah Memenuhi Syarat?</div>
                            <div class="md:col-span-6">
                                <select name="{{ $name }}[status]" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white outline-none focus:border-emerald-500">
                                    <option value="Sesuai">Sesuai / Wajar</option>
                                    <option value="Tidak">Tidak Sesuai</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="w-full py-5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-xl hover:shadow-emerald-900/40 transition-all">
                Simpan Kertas Kerja (Doc 3)
            </button>
        </form>
    </div>
</div>

    {{-- TAB 3: SPEK & HPS (DOC 4-7) --}}
{{-- TAB 3: SPEK & HPS (PEMBARUAN STRUKTUR) --}}
    <div x-show="tab === 'spek'" class="animate-enter space-y-8">
        
        {{-- A. TAMPILAN RESUME (Muncul jika tidak sedang edit) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-show="!editDoc45 && !editDoc6">
            <div class="flex flex-col gap-8">
                {{-- CARD RESUME DOC 4 & 5 --}}
                <div class="glass-card p-6 rounded-2xl border border-slate-700 bg-slate-900/40 shadow-xl transition-all">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-white font-bold flex items-center gap-2">
                            <i class="fas fa-list-check text-cyan-500"></i> Resume Spesifikasi (Doc 4 & 5)
                        </h3>
                        @if($package->items->first() && $package->items->first()->merk_tipe)
                            <button @click="editDoc45 = true" class="text-[10px] text-cyan-500 font-bold uppercase underline">Edit Detail</button>
                        @endif
                    </div>

                    @if($package->items->first() && $package->items->first()->merk_tipe)
                        <div class="space-y-3">
                            @foreach($package->items->take(2) as $item)
                                <div class="p-3 rounded-xl bg-slate-950/50 border border-slate-800">
                                    <div class="text-[10px] text-slate-500 uppercase font-black tracking-tighter">{{ $item->nama_item }}</div>
                                    <div class="text-xs text-white font-bold mt-1">{{ $item->merk_tipe }}</div>
                                </div>
                            @endforeach
                            @if($package->items->count() > 2)
                                <p class="text-[10px] text-slate-600 italic">+ {{ $package->items->count() - 2 }} item lainnya...</p>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-6">
                            <a href="{{ route('pengadaan.print.doc4', $package->id) }}" target="_blank" class="py-3 bg-slate-800 text-white rounded-xl text-[10px] font-black text-center uppercase tracking-tighter hover:bg-slate-700"><i class="fas fa-print mr-1"></i> Cetak Doc 4</a>
                            <a href="{{ route('pengadaan.print.doc5', $package->id) }}" target="_blank" class="py-3 bg-slate-800 text-white rounded-xl text-[10px] font-black text-center uppercase tracking-tighter hover:bg-slate-700"><i class="fas fa-print mr-1"></i> Cetak Doc 5</a>
                        </div>
                    @else
                        <button @click="editDoc45 = true" class="w-full py-8 border-2 border-dashed border-slate-800 text-slate-500 rounded-2xl text-[10px] font-bold uppercase hover:border-cyan-500/50 hover:text-cyan-500 transition-all group">
                            <i class="fas fa-plus-circle mb-2 text-xl block group-hover:scale-110"></i>
                            Isi Detail Spesifikasi Teknis
                        </button>
                    @endif
                </div>

                {{-- CARD RESUME DOC 6 --}}
                <div class="glass-card p-6 rounded-2xl border border-slate-700 bg-slate-900/40 shadow-xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-white font-bold flex items-center gap-2">
                            <i class="fas fa-search-dollar text-emerald-500"></i> Analisis Harga (Doc 6)
                        </h3>
                        @if($package->price_references->count() > 0)
                            <button @click="editDoc6 = true" class="text-[10px] text-emerald-500 font-bold uppercase underline">Edit Detail</button>
                        @endif
                    </div>

                    @if($package->price_references->count() > 0)
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-4 bg-slate-950 rounded-xl border border-slate-800">
                                <div class="text-[8px] text-slate-500 uppercase font-black">Rata-Rata Pembanding</div>
                                <div class="text-sm font-mono font-bold text-emerald-400">Rp {{ number_format($package->hps_hitung_rata_rata ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="p-4 bg-slate-950 rounded-xl border border-slate-800">
                                <div class="text-[8px] text-slate-500 uppercase font-black">Total Bukti Survei</div>
                                <div class="text-sm font-bold text-white">{{ $package->price_references->count() }} Referensi</div>
                            </div>
                        </div>
                        <a href="{{ route('pengadaan.print.doc6', $package->id) }}" target="_blank" class="w-full py-4 bg-emerald-600 text-white rounded-xl text-[10px] font-black flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/20 uppercase tracking-widest transition-all hover:bg-emerald-500">
                            <i class="fas fa-file-pdf text-lg"></i> CETAK DOC 6 (ANALISIS HARGA)
                        </a>
                    @else
                        <button @click="editDoc6 = true" class="w-full py-8 border-2 border-dashed border-slate-800 text-slate-500 rounded-2xl text-[10px] font-bold uppercase hover:border-emerald-500/50 hover:text-emerald-500 transition-all group">
                            <i class="fas fa-calculator mb-2 text-xl block group-hover:scale-110"></i>
                            Mulai Analisis Referensi Harga
                        </button>
                    @endif
                </div>
            </div>

            {{-- KOLOM KANAN: STATUS VISUAL --}}
            <div class="glass-card p-8 rounded-2xl border border-slate-700 flex flex-col items-center justify-center text-center bg-slate-900/20 shadow-xl">
                @if($package->items->first() && $package->items->first()->merk_tipe && $package->price_references->count() > 0)
                    <div class="w-20 h-20 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-3xl mb-6 border border-emerald-500/20 animate-pulse">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Tahap Spek & HPS Selesai</h3>
                    <p class="text-slate-400 text-sm mb-8 max-w-xs">Data spesifikasi dan analisis harga sudah lengkap. Lanjutkan ke penerbitan Surat Pesanan (Doc 10).</p>
                    <button @click="tab = 'kontrak'" class="px-10 py-4 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-cyan-900/40 hover:scale-105 transition-all">
                        Lanjut ke Tab Kontrak <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                @else
                    <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-slate-500 text-2xl mb-4 border border-slate-700">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="text-white font-bold mb-2">Penyusunan Berlangsung</h3>
                    <p class="text-slate-400 text-sm max-w-xs">Lengkapi detail spesifikasi teknis dan minimal referensi harga pasar untuk melanjutkan.</p>
                @endif
            </div>
        </div>

        {{-- B. AREA FORM INPUT (Hanya muncul saat tombol Edit/Isi diklik) --}}
        
        {{-- Form Doc 4 & 5 --}}
        <div x-show="editDoc45" class="animate-enter" x-cloak>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-white font-bold uppercase text-xs tracking-widest italic"><i class="fas fa-edit mr-2 text-cyan-500"></i> Input Spesifikasi Teknis (Doc 4 & 5)</h3>
                <button @click="editDoc45 = false" class="px-4 py-2 bg-rose-900/30 text-rose-500 rounded-lg text-[10px] font-black uppercase border border-rose-500/20 hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fas fa-times mr-1"></i> Tutup Form
                </button>
            </div>
            
            <form action="{{ route('pengadaan.update.items_bulk', $package->id) }}" method="POST" class="space-y-6">
                @csrf
                <div class="glass-card p-8 rounded-[2rem] border border-slate-700 bg-slate-900/40">
                    <div class="space-y-12">
                        @foreach($package->items as $item)
                        <div class="relative p-6 rounded-3xl bg-slate-950/50 border border-slate-800">
                            <div class="absolute -top-3 left-6 px-4 py-1 bg-cyan-600 text-white text-[10px] font-black rounded-full uppercase italic">Item #{{ $loop->iteration }}: {{ $item->nama_item }}</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div class="md:col-span-2 flex justify-between items-end gap-4">
                                    <div class="flex-1">
                                        <label class="text-[9px] text-cyan-500 uppercase font-black mb-1 block">Link E-Katalog (Doc 4)</label>
                                        <input type="text" name="items[{{ $item->id }}][link_produk_katalog]" value="{{ $item->link_produk_katalog }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-cyan-400">
                                    </div>
                                    <a href="https://katalog.inaproc.id/search?keyword={{ urlencode($item->merk_tipe ?? $item->nama_item) }}" target="_blank" class="mb-1 px-4 py-3 bg-slate-800 text-cyan-400 border border-cyan-500/30 rounded-xl text-[10px] font-black hover:bg-cyan-600 transition-all"><i class="fas fa-search mr-1"></i> Cari Inaproc</a>
                                </div>
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
        </div>

        {{-- Form Doc 6 --}}
        <div x-show="editDoc6" class="animate-enter" x-cloak>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-white font-bold uppercase text-xs tracking-widest italic"><i class="fas fa-search-dollar mr-2 text-emerald-500"></i> Analisis Harga Pasar (Doc 6)</h3>
                <button @click="editDoc6 = false" class="px-4 py-2 bg-rose-900/30 text-rose-500 rounded-lg text-[10px] font-black uppercase border border-rose-500/20 hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fas fa-times mr-1"></i> Tutup Form
                </button>
            </div>
            
            <div class="glass-card p-8 rounded-[2rem] border border-slate-700 bg-slate-900/40" x-data="{ type: 'market', searchKey: '' }">
                {{-- Seluruh area Form Doc 6 dari file asli Anda dipindahkan ke sini --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div><h3 class="text-white font-bold flex items-center gap-2">Survei Pasar & Justifikasi Harga</h3></div>
                    <div class="flex bg-slate-950 p-1 rounded-xl border border-slate-800">
                        @foreach(['qualitative' => 'A.1 Kualitatif', 'market' => 'A.2 Pasar', 'sbu' => 'B. SBU', 'contract' => 'C. Kontrak'] as $k => $v)
                        <button @click="type = '{{ $k }}'" :class="type === '{{ $k }}' ? 'bg-cyan-600 text-white' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase transition-all">{{ $v }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Smart Search Helper --}}
                <div class="mb-8 p-6 bg-slate-950/60 rounded-3xl border border-slate-800 shadow-inner">
                    <label class="text-[9px] text-emerald-500 uppercase font-black mb-3 block tracking-widest italic">Smart Search Helper</label>
                    <div class="flex flex-col md:flex-row gap-4">
                        <input type="text" x-model="searchKey" placeholder="Ketik nama produk..." class="flex-1 bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs">
                        <div class="flex gap-2">
                            <a :href="'https://katalog.inaproc.id/search?q=' + encodeURIComponent(searchKey)" target="_blank" class="px-4 py-3 bg-slate-800 text-cyan-400 border border-cyan-500/30 rounded-xl text-[9px] font-black hover:bg-cyan-600">LKPP</a>
                            <a :href="'https://www.tokopedia.com/search?q=' + encodeURIComponent(searchKey)" target="_blank" class="px-4 py-3 bg-slate-800 text-emerald-400 border border-emerald-500/30 rounded-xl text-[9px] font-black hover:bg-emerald-600">TOKOPEDIA</a>
                        </div>
                    </div>
                </div>

                <form action="{{ route('pengadaan.store.price_ref', $package->id) }}" method="POST" enctype="multipart/form-data" class="p-8 bg-slate-950 rounded-3xl border border-slate-800 mb-8 shadow-2xl">
                    @csrf <input type="hidden" name="type" :value="type">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div x-show="type === 'qualitative' || type === 'market'"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Merek & Model</label><input type="text" name="merek_model" :value="searchKey" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs"></div>
                        <div><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Sumber / Toko / Nama Dokumen</label><input type="text" name="sumber_nama" placeholder="Contoh: Tokopedia / Pergub 88" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-white text-xs"></div>
                        <div x-show="type === 'qualitative' || type === 'market'"><label class="text-[9px] text-cyan-500 uppercase font-black mb-2 block tracking-widest">Tautan URL Aktif</label><input type="text" name="link_url" placeholder="https://..." class="w-full bg-slate-900 border border-cyan-900/30 rounded-xl p-3 text-cyan-400 text-xs"></div>
                        <div x-show="type !== 'qualitative'"><label class="text-[9px] text-emerald-500 uppercase font-black mb-2 block tracking-widest">Harga Satuan (Rp)</label><input type="number" name="harga_satuan" class="w-full bg-slate-900 border border-emerald-900/50 rounded-xl p-3 text-emerald-400 font-black text-xs"></div>
                        <div><label class="text-[9px] text-amber-500 uppercase font-black mb-2 block tracking-widest">Unggah Screenshot Bukti (SS)</label><input type="file" name="file_bukti" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2.5 text-slate-400 text-[10px]"></div>
                        
                        <div class="lg:col-span-3 flex justify-end pt-4 border-t border-slate-800">
                            <button type="submit" class="bg-emerald-600 text-white px-10 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-emerald-900/30 italic"><i class="fas fa-plus-circle mr-2"></i> Tambah Referensi <span x-text="type"></span></button>
                        </div>
                    </div>
                </form>

                {{-- Tabel Data Survei --}}
                <div class="mt-8">
                    <h4 class="text-white text-[10px] font-black uppercase mb-4 tracking-widest flex items-center justify-between"><span>Daftar Bukti Survei Terdata</span></h4>
                    <div class="overflow-x-auto rounded-2xl border border-slate-800">
                        <table class="w-full text-left">
                            <thead class="bg-slate-900">
                                <tr><th class="p-4 text-[9px] text-slate-500 uppercase font-black">Tipe</th><th class="p-4 text-[9px] text-slate-500 uppercase font-black">Merek / Detail</th><th class="p-4 text-[9px] text-slate-500 uppercase font-black text-center">Bukti SS</th><th class="p-4 text-[9px] text-slate-500 uppercase font-black text-right">Harga Satuan</th><th class="p-4 text-[9px] text-slate-500 uppercase font-black text-center">Aksi</th></tr>
                            </thead>
                            <tbody class="bg-slate-950/50 text-xs text-white">
                                @forelse($package->price_references as $ref)
                                <tr class="border-t border-slate-900">
                                    <td class="p-4"><span class="px-2 py-0.5 rounded bg-slate-800 text-cyan-400 text-[8px] font-black uppercase">{{ $ref->type }}</span></td>
                                    <td class="p-4"><div class="font-bold">{{ $ref->merek_model ?? $ref->sumber_nama }}</div><div class="text-[9px] text-slate-500">{{ $ref->sumber_nama }}</div></td>
                                    <td class="p-4 text-center">@if($ref->file_bukti)<a href="{{ asset('storage/bukti_harga/' . $ref->file_bukti) }}" target="_blank" class="text-emerald-400"><i class="fas fa-image text-lg"></i></a>@endif</td>
                                    <td class="p-4 text-right font-mono font-bold text-emerald-400">Rp {{ number_format($ref->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="p-4 text-center">
                                        <form action="{{ route('pengadaan.destroy.price_ref', $ref->id) }}" method="POST">@csrf @method('DELETE')<button class="text-rose-500 hover:text-rose-400"><i class="fas fa-trash"></i></button></form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="p-8 text-center text-slate-600 italic">Belum ada data referensi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Justifikasi Akhir & Unggah PDF --}}
                <form action="{{ route('pengadaan.update.price_justification', $package->id) }}" method="POST" enctype="multipart/form-data" class="mt-12 p-8 bg-slate-950/40 rounded-[2.5rem] border border-slate-800 shadow-2xl">
                    @csrf <h4 class="text-white text-[11px] font-black uppercase mb-6 tracking-widest italic">Justifikasi Akhir & Lampiran Utama</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2"><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Narasi Kesimpulan Kewajaran Harga</label><textarea name="kesimpulan_analisis_harga" rows="3" class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 text-white text-xs outline-none focus:border-amber-500 transition-all">{{ $package->kesimpulan_analisis_harga }}</textarea></div>
                        <div><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Unggah Dokumen SBU (PDF)</label><input type="file" name="file_sbu" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2 text-xs text-slate-400"></div>
                        <div><label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Unggah Salinan Kontrak Terdahulu (PDF)</label><input type="file" name="file_kontrak_lama" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2 text-xs text-slate-400"></div>
                    </div>
                    <div class="mt-8 flex items-center justify-between p-4 bg-amber-950/20 border border-amber-900/30 rounded-2xl">
                        <p class="text-[8px] text-amber-500 italic uppercase font-bold tracking-tighter leading-tight">Disclaimer: Dokumen ini disusun untuk tujuan internal persiapan e-purchasing.</p>
                        <button type="submit" class="px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl">Simpan Justifikasi Akhir</button>
                    </div>
                </form>

                {{-- Ringkasan Harga --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                    <div class="p-6 bg-slate-950 rounded-2xl border border-slate-800"><span class="text-[9px] text-slate-500 uppercase font-black block mb-2 tracking-tighter italic">Harga Terendah</span><div class="text-2xl font-mono font-bold text-white">Rp {{ number_format($package->hps_terendah ?? 0, 0, ',', '.') }}</div></div>
                    <div class="p-6 bg-slate-950 rounded-2xl border border-slate-800"><span class="text-[9px] text-slate-500 uppercase font-black block mb-2 tracking-tighter italic">Harga Tertinggi</span><div class="text-2xl font-mono font-bold text-white">Rp {{ number_format($package->hps_tertinggi ?? 0, 0, ',', '.') }}</div></div>
                    <div class="p-6 bg-emerald-900/20 rounded-2xl border border-emerald-500/30 shadow-inner group transition-all"><span class="text-[9px] text-emerald-500 uppercase font-black block mb-2 tracking-tighter italic">Rata-Rata Pembanding</span><div class="text-2xl font-mono font-bold text-emerald-400">Rp {{ number_format($package->hps_hitung_rata_rata ?? 0, 0, ',', '.') }}</div></div>
                </div>
            </div>
        </div>
    </div>

{{-- TAB 4: KONTRAK / SURAT PESANAN (DOC 10) --}}
<div x-show="tab === 'kontrak'" class="animate-enter space-y-8">
    @php 
        $contractData = $package->contract;
        $totalItemsValue = $package->items->sum('total_hps');
    @endphp

    {{-- VALIDASI: Cek Survei Pasar --}}
    @if($package->price_references->where('type', 'market')->count() == 0)
        <div class="glass-card p-12 text-center border border-rose-500/20 bg-rose-950/5 rounded-[2rem]">
            <div class="w-16 h-16 rounded-full bg-rose-900/30 flex items-center justify-center text-rose-500 text-2xl mx-auto mb-6">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="text-white font-bold mb-2">Survei Pasar Belum Lengkap</h3>
            <p class="text-slate-400 text-sm max-w-sm mx-auto">Harap lengkapi analisis harga pasar pada Tab Spek & HPS sebelum menerbitkan Surat Pesanan.</p>
        </div>
    @else
        
        {{-- A. TAMPILAN RESUME (Muncul jika data sudah ada dan tidak sedang edit) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-show="!editDoc10">
            <div class="flex flex-col gap-8">
                <div class="glass-card p-6 rounded-2xl border border-slate-700 bg-slate-900/40 shadow-xl transition-all">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-white font-bold flex items-center gap-2">
                            <i class="fas fa-file-signature text-emerald-500"></i> Resume Surat Pesanan (Doc 10)
                        </h3>
                        @if($contractData)
                            <button @click="editDoc10 = true" class="text-[10px] text-emerald-500 font-bold uppercase underline">Edit Detail</button>
                        @endif
                    </div>

                    @if($contractData)
                        <div class="space-y-4 text-xs">
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-500 font-bold uppercase tracking-tighter">Nomor SP</span>
                                <span class="text-white font-mono font-bold">{{ $contractData->nomor_sp }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-500 font-bold uppercase tracking-tighter">Tanggal</span>
                                <span class="text-white font-bold">{{ $contractData->tanggal_sp }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-500 font-bold uppercase tracking-tighter">Penyedia</span>
                                <span class="text-cyan-400 font-bold">{{ $contractData->vendor->nama_perusahaan ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-500 font-bold uppercase tracking-tighter">Nilai Kontrak</span>
                                <span class="text-emerald-400 font-black">Rp {{ number_format($contractData->nilai_kontrak_final, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('pengadaan.print.doc10', $package->id) }}" target="_blank" 
                               class="w-full py-4 bg-emerald-600 text-white rounded-xl text-[10px] font-black flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/20 uppercase tracking-widest transition-all hover:bg-emerald-500">
                                <i class="fas fa-print text-lg"></i> CETAK SURAT PESANAN (DOC 10)
                            </a>
                        </div>
                    @else
                        {{-- Tampilan Tombol Besar jika belum diisi --}}
                        <button @click="editDoc10 = true" class="w-full py-12 border-2 border-dashed border-slate-800 text-slate-500 rounded-2xl text-[10px] font-bold uppercase hover:border-emerald-500/50 hover:text-emerald-500 transition-all group">
                            <i class="fas fa-file-signature mb-2 text-3xl block group-hover:scale-110"></i>
                            Mulai Mengisi Penerbitan Surat Pesanan
                        </button>
                    @endif
                </div>

                {{-- Card Pihak II (Penyedia) --}}
                @if($contractData && $contractData->vendor)
                <div class="glass-card p-6 rounded-2xl border border-slate-700 bg-slate-900/40">
                    <h4 class="text-slate-500 text-[10px] font-black uppercase mb-6 tracking-widest italic">Data Pihak II (Penyedia)</h4>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-900/50 flex items-center justify-center text-emerald-400 border border-emerald-700 shadow-lg"><i class="fas fa-building"></i></div>
                        <div>
                            <div class="text-white font-bold text-base leading-tight">{{ $contractData->vendor->nama_direktur }}</div>
                            <div class="text-[9px] text-slate-500 mt-1 uppercase font-bold tracking-tighter">
                                {{ $contractData->vendor->nama_perusahaan }} ({{ $contractData->vendor->bentuk_usaha }})
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- KOLOM KANAN: STATUS VISUAL --}}
            <div class="glass-card p-8 rounded-2xl border border-slate-700 flex flex-col items-center justify-center text-center bg-slate-900/20 shadow-xl">
                @if($contractData)
                    <div class="w-20 h-20 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-3xl mb-6 border border-emerald-500/20">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Persiapan Kontrak Selesai</h3>
                    <p class="text-slate-400 text-sm mb-8 max-w-xs">Seluruh dokumen persiapan pengadaan telah diterbitkan. Silakan unduh Dokumen 1 s.d 10 melalui menu Laporan & Arsip.</p>
                @else
                    <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-slate-500 text-2xl mb-4 border border-slate-700">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3 class="text-white font-bold mb-2">Penerbitan Surat Pesanan</h3>
                    <p class="text-slate-400 text-sm max-w-xs">Finalisasi nomor surat, tanggal, dan penetapan penyedia untuk mencetak Surat Pesanan (Doc 10).</p>
                @endif
            </div>
        </div>

        {{-- B. AREA FORM INPUT (Hanya muncul saat tombol Edit/Isi diklik) --}}
        <div x-show="editDoc10" class="animate-enter" x-cloak>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-white font-bold uppercase text-xs tracking-widest italic"><i class="fas fa-edit mr-2 text-emerald-500"></i> Form Penerbitan Surat Pesanan</h3>
                <button @click="editDoc10 = false" class="px-4 py-2 bg-rose-900/30 text-rose-500 rounded-lg text-[10px] font-black uppercase border border-rose-500/20 hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fas fa-times mr-1"></i> Tutup Form
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <form action="{{ route('pengadaan.store.contract', $package->id) }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="glass-card p-8 rounded-[2rem] border border-slate-700 bg-slate-900/40">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Nomor Surat Pesanan</label>
                                    <input type="text" name="nomor_sp" value="{{ $contractData->nomor_sp ?? '' }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white focus:border-cyan-500 outline-none" placeholder="027/.../SP/KOMINFO/2026" required>
                                </div>
                                <div>
                                    <label class="text-[9px] text-slate-500 uppercase font-black mb-2 block tracking-widest">Tanggal Surat Pesanan</label>
                                    <input type="date" name="tanggal_sp" value="{{ $contractData->tanggal_sp ?? date('Y-m-d') }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white focus:border-cyan-500 outline-none" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[9px] text-cyan-500 uppercase font-black mb-2 block tracking-widest">Alamat Penyerahan Pekerjaan</label>
                                    <textarea name="alamat_penyerahan" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white focus:border-cyan-500 outline-none" required>{{ $contractData->alamat_penyerahan ?? $package->lokasi_pekerjaan }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[9px] text-emerald-500 uppercase font-black mb-2 block tracking-widest">Pilih Penyedia Terpilih</label>
                                    <select name="vendor_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-white focus:border-emerald-500 outline-none" required>
                                        <option value="">-- Pilih Vendor Berdasarkan Hasil Survei --</option>
                                        @foreach($vendors as $v)
                                            <option value="{{ $v->id }}" {{ (isset($contractData) && $contractData->vendor_id == $v->id) ? 'selected' : '' }}>{{ $v->nama_perusahaan }} ({{ $v->bentuk_usaha }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            {{-- Parameter Kewajiban (Syarat Khusus) --}}
                            <div class="p-6 bg-amber-950/10 border border-amber-900/30 rounded-2xl space-y-6">
                                <h4 class="text-[10px] text-amber-500 font-black uppercase tracking-widest flex items-center gap-2"><i class="fas fa-gavel text-amber-500"></i> Syarat Khusus & Jaminan</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-[9px] text-slate-400 uppercase font-black mb-2 block tracking-widest">Nilai Jaminan Pelaksanaan (Rp)</label>
                                        <input type="number" name="nilai_jaminan_pelaksanaan" value="{{ $contractData->nilai_jaminan_pelaksanaan ?? ($totalItemsValue * 0.05) }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-amber-400 font-bold text-sm focus:border-amber-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="text-[9px] text-slate-400 uppercase font-black mb-2 block tracking-widest">Penerbit Jaminan</label>
                                        <input type="text" name="penerbit_jaminan" value="{{ $contractData->penerbit_jaminan ?? '' }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-sm text-white focus:border-amber-500 outline-none" placeholder="Contoh: Bank Kalbar">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full mt-8 py-5 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl hover:scale-[1.01] transition-all">SIMPAN & TERBITKAN SURAT PESANAN</button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-1">
                    <div class="p-6 rounded-[2rem] bg-slate-950 border border-slate-800 shadow-inner">
                        <h4 class="text-slate-500 text-[10px] font-black uppercase mb-4 tracking-widest italic">Pratinjau Nilai Kontrak</h4>
                        <div class="text-2xl font-mono font-bold text-emerald-400 mb-6">Rp {{ number_format($contractData->nilai_kontrak_final ?? $totalItemsValue, 0, ',', '.') }}</div>
                        
                        <div class="space-y-3">
                            @foreach($package->items as $item)
                                <div class="p-3 rounded-xl bg-slate-900/40 border border-slate-800/50">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-[9px] text-white font-bold leading-tight">{{ $item->nama_item }}</span>
                                        <span class="text-[8px] text-emerald-400 font-mono font-bold">Rp {{ number_format($item->total_hps, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
@endsection