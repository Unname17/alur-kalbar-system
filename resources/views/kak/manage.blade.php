@extends('kak.layout')

@section('title', 'Penyusunan KAK')
@section('header_title', 'Penyusunan Kerangka Acuan Kerja')

@section('content')
<div class="max-w-6xl mx-auto" x-data="kakHandler()">
    
    {{-- Header Info --}}
    <div class="glass-card p-6 rounded-3xl mb-6 flex justify-between items-center border border-slate-800">
        <div>
            <div class="text-[10px] uppercase text-slate-500 font-bold tracking-widest">Sub Kegiatan</div>
            <h2 class="text-xl font-bold text-white">{{ $rka->subActivity->nama_sub }}</h2>
        </div>
        <div class="text-right flex gap-3">
             <a href="{{ route('kak.print', $rka->id) }}" target="_blank" class="px-4 py-2 bg-rose-600 rounded-xl text-white text-xs font-bold hover:bg-rose-700 transition-all flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>
            <div class="text-[10px] uppercase text-slate-500 font-bold tracking-widest text-right">
                Pagu: <span class="text-emerald-400 text-sm font-mono">Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('kak.store', $rka->id) }}" method="POST">
        @csrf
        
        {{-- TAB NAVIGATION --}}
        <div class="flex gap-4 mb-6 border-b border-slate-800 pb-1">
            <button type="button" @click="tab = 'narasi'" :class="tab === 'narasi' ? 'text-indigo-400 border-b-2 border-indigo-500' : 'text-slate-500 hover:text-slate-300'" class="pb-3 px-4 text-sm font-bold transition-all">1. Narasi & Dasar Hukum</button>
            <button type="button" @click="tab = 'pelaksanaan'" :class="tab === 'pelaksanaan' ? 'text-indigo-400 border-b-2 border-indigo-500' : 'text-slate-500 hover:text-slate-300'" class="pb-3 px-4 text-sm font-bold transition-all">2. Pelaksanaan & Jadwal</button>
            <button type="button" @click="tab = 'preview'" :class="tab === 'preview' ? 'text-indigo-400 border-b-2 border-indigo-500' : 'text-slate-500 hover:text-slate-300'" class="pb-3 px-4 text-sm font-bold transition-all">3. Finalisasi (Preview)</button>
        </div>

        {{-- TAB 1: NARASI --}}
        <div x-show="tab === 'narasi'" class="space-y-6" x-transition>
            
            {{-- A. LATAR BELAKANG --}}
            <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                <label class="text-xs font-bold text-white uppercase mb-4 block">A. Latar Belakang</label>
                <textarea x-model="latarBelakang" name="latar_belakang" rows="8" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-4 text-sm text-slate-200 focus:border-indigo-500 outline-none leading-relaxed" placeholder="Uraikan latar belakang kegiatan..."></textarea>
            </div>

            {{-- DASAR HUKUM --}}
            <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                <label class="text-xs font-bold text-white uppercase block mb-4">Dasar Hukum</label>
                <div class="space-y-2">
                    <template x-for="(hukum, index) in dasarHukum" :key="index">
                        <div class="flex gap-2">
                            <input type="text" :name="'dasar_hukum[]'" x-model="dasarHukum[index]" class="flex-1 bg-slate-800 border border-slate-700 rounded-xl p-3 text-sm text-white focus:border-indigo-500 outline-none" placeholder="Contoh: UU No. 23 Tahun 2014...">
                            <button type="button" @click="removeHukum(index)" class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all"><i class="fas fa-trash"></i></button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="addHukum()" class="mt-4 text-xs font-bold text-indigo-400 hover:text-white transition-colors">+ Tambah Dasar Hukum</button>
            </div>

            {{-- C. MAKSUD & TUJUAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- C.1 MAKSUD (KEMBALI KE TEXTAREA BIASA) --}}
                <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                    <label class="text-xs font-bold text-white uppercase block mb-4">C.1. Maksud</label>
                    <textarea x-model="maksud" name="maksud" rows="6" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-4 text-sm text-slate-200 focus:border-indigo-500 outline-none leading-relaxed" placeholder="Uraikan maksud kegiatan secara umum..."></textarea>
                </div>

                {{-- C.2 TUJUAN (TETAP LIST DINAMIS) --}}
                <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                    <label class="text-xs font-bold text-white uppercase block mb-4">C.2. Tujuan</label>
                    <div class="space-y-2">
                        <template x-for="(item, index) in listTujuan" :key="index">
                            <div class="flex gap-2">
                                <div class="w-6 pt-3 text-center text-slate-500 text-xs font-bold" x-text="String.fromCharCode(97 + index) + '.'"></div> {{-- a., b., c. --}}
                                <textarea :name="'tujuan[]'" x-model="listTujuan[index]" rows="2" class="flex-1 bg-slate-800 border border-slate-700 rounded-xl p-3 text-sm text-white focus:border-indigo-500 outline-none" placeholder="Uraikan tujuan..."></textarea>
                                <button type="button" @click="removeTujuan(index)" class="w-10 h-10 mt-1 rounded-xl bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all"><i class="fas fa-trash"></i></button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addTujuan()" class="mt-4 text-xs font-bold text-indigo-400 hover:text-white transition-colors">+ Tambah Tujuan</button>
                </div>

            </div>

            {{-- B. PENERIMA MANFAAT --}}
            <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                <label class="text-xs font-bold text-white uppercase block mb-4">B. Penerima Manfaat / Outcome</label>
                <textarea x-model="penerimaManfaat" name="penerima_manfaat" rows="4" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-4 text-sm text-slate-200 focus:border-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="button" @click="tab = 'pelaksanaan'" class="px-6 py-3 bg-indigo-600 rounded-xl text-white font-bold text-xs hover:bg-indigo-700 transition-all">
                    Lanjut <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        {{-- TAB 2: PELAKSANAAN --}}
        <div x-show="tab === 'pelaksanaan'" class="space-y-6" style="display: none;" x-transition>
            {{-- ... (Bagian Pelaksanaan SAMA PERSIS seperti sebelumnya) ... --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass-card p-6 rounded-[2rem] bg-slate-900 border border-slate-800">
                    <label class="text-xs font-bold text-white uppercase block mb-4">Metode Pelaksanaan</label>
                    <select x-model="metode" name="metode_pelaksanaan" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-4 text-sm text-white focus:border-indigo-500 outline-none">
                        <option value="Swakelola">Swakelola</option>
                        <option value="Penyedia">Penyedia</option>
                        <option value="Swakelola & Penyedia">Gabungan</option>
                    </select>
                </div>
                <div class="glass-card p-6 rounded-[2rem] bg-slate-900 border border-slate-800">
                    <label class="text-xs font-bold text-white uppercase block mb-4">Tempat Pelaksanaan</label>
                    <input type="text" x-model="tempat" name="tempat_pelaksanaan" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-4 text-sm text-white focus:border-indigo-500 outline-none">
                </div>
            </div>
            <div class="glass-card p-6 rounded-[2rem] bg-slate-900 border border-slate-800 opacity-80">
                <label class="text-xs font-bold text-amber-500 uppercase block mb-4"><i class="fas fa-lock mr-1"></i> Waktu Pelaksanaan (Terkunci)</label>
                <input type="text" value="{{ $rka->waktu_pelaksanaan }}" readonly class="w-full bg-slate-950 border border-slate-800 rounded-xl p-4 text-sm text-slate-400 font-mono cursor-not-allowed">
            </div>
            <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                <label class="text-xs font-bold text-white uppercase block mb-4">F. Tahapan Pelaksanaan</label>
                <div class="space-y-3">
                    <template x-for="(item, idx) in tahapan" :key="idx">
                        <div class="grid grid-cols-12 gap-3 items-start">
                            <div class="col-span-1 pt-3 text-center text-slate-500 font-bold" x-text="idx + 1 + '.'"></div>
                            <div class="col-span-7">
                                <input type="text" :name="'tahapan_pelaksanaan['+idx+'][uraian]'" x-model="item.uraian" placeholder="Uraian" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-sm text-white">
                            </div>
                            <div class="col-span-3">
                                <input type="text" :name="'tahapan_pelaksanaan['+idx+'][output]'" x-model="item.output" placeholder="Output" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-sm text-white">
                            </div>
                            <div class="col-span-1">
                                <button type="button" @click="removeTahapan(idx)" class="w-full h-[46px] rounded-xl bg-rose-500/10 text-rose-500"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </template>
                </div>
                <button type="button" @click="addTahapan()" class="mt-4 text-xs font-bold text-indigo-400">+ Tambah Tahapan</button>
            </div>
            <div class="flex justify-between">
                <button type="button" @click="tab = 'narasi'" class="px-6 py-3 bg-slate-800 rounded-xl text-slate-400 font-bold text-xs">Kembali</button>
                <button type="button" @click="tab = 'preview'" class="px-6 py-3 bg-indigo-600 rounded-xl text-white font-bold text-xs">Finalisasi <i class="fas fa-arrow-right ml-2"></i></button>
            </div>
        </div>

        {{-- TAB 3: PREVIEW (FINALISASI) --}}
        <div x-show="tab === 'preview'" class="space-y-6" style="display: none;" x-transition>
            
            <div class="bg-white text-black p-10 md:p-16 rounded-xl shadow-2xl overflow-hidden font-serif">
                {{-- JUDUL --}}
                <div class="text-center mb-6">
                    <h3 class="font-bold text-base uppercase underline tracking-wide">KERANGKA ACUAN KERJA (KAK)</h3>
                    <p class="uppercase text-xs mt-1 font-bold">TAHUN ANGGARAN 2025</p>
                </div>

                {{-- I. IDENTITAS --}}
                <div class="mb-4 text-sm">
                    <h4 class="font-bold uppercase mb-2 text-xs">I. IDENTITAS KEGIATAN</h4>
                    <table class="w-full border-collapse text-xs">
                        <tr><td width="160" class="align-top py-0.5">Unit Organisasi</td><td width="10" class="align-top">:</td><td>Dinas Komunikasi dan Informatika</td></tr>
                        <tr><td class="align-top py-0.5">Program</td><td class="align-top">:</td><td>{{ $rka->subActivity->activity->program->nama_program ?? '-' }}</td></tr>
                        <tr><td class="align-top py-0.5">Kegiatan</td><td class="align-top">:</td><td>{{ $rka->subActivity->activity->nama_kegiatan ?? '-' }}</td></tr>
                        <tr><td class="align-top py-0.5">Sub Kegiatan</td><td class="align-top">:</td><td>{{ $rka->subActivity->nama_sub ?? '-' }}</td></tr>
                        <tr><td class="align-top py-0.5">Lokasi</td><td class="align-top">:</td><td x-text="tempat"></td></tr>
                    </table>
                </div>

                {{-- II. PENDAHULUAN --}}
                <div class="mb-4 text-sm">
                    <h4 class="font-bold uppercase mb-2 text-xs">II. PENDAHULUAN</h4>
                    <div class="ml-4 mb-3">
                        <span class="font-bold block text-xs">1. Latar Belakang</span>
                        <div class="text-justify whitespace-pre-line leading-relaxed" x-text="latarBelakang || '...'"></div>
                    </div>
                    <div class="ml-4">
                        <span class="font-bold block text-xs">2. Dasar Hukum</span>
                        <ol class="list-decimal ml-5 mt-1">
                            <template x-for="h in dasarHukum">
                                <li x-show="h" x-text="h" class="pl-1"></li>
                            </template>
                        </ol>
                    </div>
                </div>

                {{-- III. MAKSUD & TUJUAN --}}
                <div class="mb-4 text-sm">
                    <h4 class="font-bold uppercase mb-2 text-xs">III. MAKSUD DAN TUJUAN</h4>
                    <div class="ml-4">
                        {{-- MAKSUD: TEKS BIASA --}}
                        <span class="font-bold block text-xs">1. Maksud</span>
                        <div class="text-justify whitespace-pre-line mb-3" x-text="maksud || '...'"></div>
                        
                        {{-- TUJUAN: LIST ABJAD (a, b, c) --}}
                        <span class="font-bold block text-xs">2. Tujuan</span>
                        {{-- STYLE DI SINI: list-style-type: lower-alpha --}}
                        <ol style="list-style-type: lower-alpha;" class="ml-5 mt-1">
                            <template x-for="t in listTujuan">
                                <li x-show="t" x-text="t" class="pl-1 text-justify"></li>
                            </template>
                        </ol>
                    </div>
                </div>

                {{-- ... SISA PREVIEW (KELUARAN, METODE, BIAYA, DLL) TETAP SAMA ... --}}
                <div class="mb-4 text-sm">
                    <h4 class="font-bold uppercase mb-2 text-xs">V. KELUARAN (OUTPUT)</h4>
                    <div class="ml-4">
                        Indikator: {{ $rka->subActivity->indikator_sub }}<br>
                        Target: {{ $rka->subActivity->target_2025 }} {{ $rka->subActivity->satuan }}
                    </div>
                </div>
                <div class="mb-4 text-sm">
                    <h4 class="font-bold uppercase mb-2 text-xs">VI. HASIL YANG DIHARAPKAN (OUTCOME)</h4>
                    <div class="ml-4 text-justify whitespace-pre-line" x-text="penerimaManfaat || '...'"></div>
                </div>
                <div class="mb-4 text-sm">
                    <h4 class="font-bold uppercase mb-2 text-xs">VII. METODE PELAKSANAAN</h4>
                    <div class="ml-4">Dilaksanakan secara: <strong x-text="metode"></strong></div>
                </div>
                <div class="mb-4 text-sm">
                    <h4 class="font-bold uppercase mb-2 text-xs">VIII. RENCANA REALISASI FISIK</h4>
                    <div class="ml-4">Waktu Pelaksanaan: <strong>{{ $rka->waktu_pelaksanaan }}</strong></div>
                </div>
                <div class="mb-4 text-sm">
                    <h4 class="font-bold uppercase mb-2 text-xs">X. RINCIAN BIAYA</h4>
                    <table class="w-full text-xs border-collapse border border-black">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-black p-1 text-center">Uraian Belanja</th>
                                <th class="border border-black p-1 text-center">Spesifikasi</th>
                                <th class="border border-black p-1 text-center w-12">Vol</th>
                                <th class="border border-black p-1 text-center w-12">Sat</th>
                                <th class="border border-black p-1 text-center w-24">Harga</th>
                                <th class="border border-black p-1 text-center w-24">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rka->details as $detail)
                            <tr>
                                <td class="border border-black p-1">{{ $detail->uraian_belanja }}</td>
                                <td class="border border-black p-1">{{ $detail->spesifikasi }}</td>
                                <td class="border border-black p-1 text-center">{{ $detail->koefisien }}</td>
                                <td class="border border-black p-1 text-center">{{ $detail->satuan }}</td>
                                <td class="border border-black p-1 text-right">{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td class="border border-black p-1 text-right">{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr class="font-bold bg-gray-50">
                                <td colspan="5" class="border border-black p-1 text-right">TOTAL ANGGARAN</td>
                                <td class="border border-black p-1 text-right">Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-10 flex justify-end text-sm">
                    <div class="text-center w-64">
                        <p>Pontianak, {{ date('d F Y') }}</p>
                        <p>Pejabat Pembuat Komitmen</p>
                        <br><br><br>
                        <p class="font-bold underline uppercase">{{ Auth::user()->nama_lengkap ?? '.......................' }}</p>
                        <p>NIP. .......................</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center bg-slate-900 p-6 rounded-[2rem] border border-slate-800 mt-6">
                <button type="button" @click="tab = 'pelaksanaan'" class="text-slate-500 text-xs font-bold hover:text-white">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali Edit
                </button>
                <button type="submit" class="px-10 py-4 bg-emerald-600 text-white rounded-2xl font-bold shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Dokumen KAK
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function kakHandler() {
        return {
            tab: 'narasi',
            
            // --- DATA ---
            latarBelakang: `{{ $kak->latar_belakang ?? '' }}`,
            penerimaManfaat: `{{ $kak->penerima_manfaat ?? '' }}`,
            metode: `{{ $kak->metode_pelaksanaan ?? 'Swakelola' }}`,
            tempat: `{{ $kak->tempat_pelaksanaan ?? $rka->lokasi_kegiatan }}`,
            
            // MAKSUD (STRING BIASA SEKARANG)
            maksud: `{{ $kak->maksud ?? '' }}`, 

            // TUJUAN (ARRAY / LIST)
            listTujuan: @json($kak->tujuan ?? ['']), 

            // LAINNYA
            dasarHukum: @json($kak->dasar_hukum ?? ['']),
            tahapan: @json($kak->tahapan_pelaksanaan ?? [['uraian' => '', 'output' => '']]),

            // --- LOGIC ---
            addHukum() { this.dasarHukum.push(''); },
            removeHukum(i) { if(this.dasarHukum.length > 1) this.dasarHukum.splice(i, 1); },

            // Logic Tujuan (Tambah/Hapus Baris)
            addTujuan() { this.listTujuan.push(''); },
            removeTujuan(i) { if(this.listTujuan.length > 1) this.listTujuan.splice(i, 1); },

            addTahapan() { this.tahapan.push({uraian: '', output: ''}); },
            removeTahapan(i) { if(this.tahapan.length > 1) this.tahapan.splice(i, 1); }
        }
    }
</script>
@endsection