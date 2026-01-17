@extends('kak.layout')

@section('title', 'Penyusunan KAK')
@section('header_title', 'Penyusunan Kerangka Acuan Kerja')

@section('content')
<div class="max-w-6xl mx-auto" x-data="kakHandler()">
    
    {{-- 1. INFO CARD (DATA OTOMATIS DARI RKA & KINERJA - 5W1H) --}}
    <div class="glass-card p-6 rounded-[2rem] bg-slate-900/50 border border-indigo-500/30 mb-8 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
        
        <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white"><i class="fas fa-info"></i></div>
            <h3 class="text-indigo-300 font-bold text-sm uppercase tracking-widest">Konteks Kegiatan (Data Otomatis)</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-sm relative z-10">
            {{-- WHAT --}}
            <div>
                <div class="text-slate-500 text-[10px] font-bold uppercase mb-1">Sub Kegiatan (What)</div>
                <div class="text-white font-bold mb-3 leading-snug">{{ $rka->subActivity->nama_sub }}</div>
                
                <div class="text-slate-500 text-[10px] font-bold uppercase mb-1">Target Output</div>
                <div class="text-emerald-400 font-mono font-bold bg-emerald-900/20 px-3 py-1 rounded-lg inline-block">
                    {{ $rka->subActivity->tahun_1 }} {{ $rka->subActivity->satuan }}
                </div>
            </div>

            {{-- HOW MUCH --}}
            <div>
                <div class="text-slate-500 text-[10px] font-bold uppercase mb-1">Pagu Anggaran (How Much)</div>
                <div class="text-amber-400 font-bold font-mono text-2xl mb-1">
                    Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-slate-400">Sumber: RKA Murni</div>
            </div>

            {{-- WHEN --}}
            <div>
                <div class="text-slate-500 text-[10px] font-bold uppercase mb-1">Waktu Pelaksanaan (When)</div>
                <div class="text-white font-bold flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-indigo-400"></i>
                    {{ $rka->waktu_pelaksanaan ?? 'Januari - Desember' }}
                </div>
                <div class="text-[10px] text-slate-500 mt-2 italic">
                    *Waktu dikunci dari RKA. Silakan atur matriks tahapan di bawah.
                </div>
            </div>
        </div>
    </div>

    {{-- 2. FORM INPUT NARASI --}}
    <form action="{{ route('kak.store', $rka->id) }}" method="POST">
        @csrf
        
        {{-- TAB NAVIGATION --}}
        <div class="flex gap-4 mb-6 border-b border-slate-800 pb-1 overflow-x-auto">
            <button type="button" @click="tab = 'narasi'" :class="tab === 'narasi' ? 'text-indigo-400 border-b-2 border-indigo-500' : 'text-slate-500 hover:text-slate-300'" class="pb-3 px-4 text-sm font-bold transition-all whitespace-nowrap">1. Narasi & Dasar Hukum</button>
            <button type="button" @click="tab = 'pelaksanaan'" :class="tab === 'pelaksanaan' ? 'text-indigo-400 border-b-2 border-indigo-500' : 'text-slate-500 hover:text-slate-300'" class="pb-3 px-4 text-sm font-bold transition-all whitespace-nowrap">2. Metode & Timeline</button>
            <button type="button" @click="tab = 'preview'" :class="tab === 'preview' ? 'text-indigo-400 border-b-2 border-indigo-500' : 'text-slate-500 hover:text-slate-300'" class="pb-3 px-4 text-sm font-bold transition-all whitespace-nowrap">3. Finalisasi</button>
        </div>

        {{-- TAB 1: NARASI --}}
        <div x-show="tab === 'narasi'" class="space-y-6" x-transition>
            
            {{-- A. LATAR BELAKANG --}}
            <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                <label class="text-xs font-bold text-white uppercase mb-4 block">A. Latar Belakang (Why)</label>
                <textarea x-model="latarBelakang" name="latar_belakang" rows="8" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-4 text-sm text-slate-200 focus:border-indigo-500 outline-none leading-relaxed" placeholder="Jelaskan mengapa kegiatan ini harus dilaksanakan..."></textarea>
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

            {{-- MAKSUD & TUJUAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                    <label class="text-xs font-bold text-white uppercase block mb-4">C.1. Maksud</label>
                    <textarea x-model="maksud" name="maksud" rows="6" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-4 text-sm text-slate-200 focus:border-indigo-500 outline-none" placeholder="Maksud umum kegiatan..."></textarea>
                </div>
                <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                    <label class="text-xs font-bold text-white uppercase block mb-4">C.2. Tujuan</label>
                    <div class="space-y-2">
                        <template x-for="(item, index) in listTujuan" :key="index">
                            <div class="flex gap-2">
                                <div class="w-6 pt-3 text-center text-slate-500 text-xs font-bold" x-text="String.fromCharCode(97 + index) + '.'"></div>
                                <textarea :name="'tujuan[]'" x-model="listTujuan[index]" rows="2" class="flex-1 bg-slate-800 border border-slate-700 rounded-xl p-3 text-sm text-white focus:border-indigo-500 outline-none" placeholder="Tujuan spesifik..."></textarea>
                                <button type="button" @click="removeTujuan(index)" class="w-10 h-10 mt-1 rounded-xl bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all"><i class="fas fa-trash"></i></button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addTujuan()" class="mt-4 text-xs font-bold text-indigo-400 hover:text-white transition-colors">+ Tambah Tujuan</button>
                </div>
            </div>

            {{-- PENERIMA MANFAAT --}}
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

        {{-- TAB 2: PELAKSANAAN & TIMELINE --}}
        <div x-show="tab === 'pelaksanaan'" class="space-y-6" style="display: none;" x-transition>
            
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

            {{-- TAHAPAN PELAKSANAAN + TIMELINE MATRIX --}}
            <div class="glass-card p-8 rounded-[2rem] bg-slate-900 border border-slate-800">
                <div class="flex justify-between items-center mb-4">
                    <label class="text-xs font-bold text-white uppercase">F. Tahapan & Jadwal Matriks</label>
                    <div class="text-[10px] text-slate-400 italic">Centang bulan pelaksanaan untuk setiap tahapan</div>
                </div>
                
                <div class="space-y-6">
                    <template x-for="(item, idx) in tahapan" :key="idx">
                        <div class="bg-slate-950/50 p-4 rounded-2xl border border-slate-800">
                            
                            {{-- Baris 1: Uraian & Output --}}
                            <div class="grid grid-cols-12 gap-3 items-start mb-4">
                                <div class="col-span-1 pt-3 text-center text-slate-500 font-bold" x-text="idx + 1 + '.'"></div>
                                <div class="col-span-7">
                                    <label class="text-[9px] uppercase text-slate-500 font-bold mb-1 block">Uraian Kegiatan</label>
                                    <input type="text" :name="'tahapan_pelaksanaan['+idx+'][uraian]'" x-model="item.uraian" placeholder="Contoh: Rapat Koordinasi..." class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-sm text-white">
                                </div>
                                <div class="col-span-3">
                                    <label class="text-[9px] uppercase text-slate-500 font-bold mb-1 block">Output</label>
                                    <input type="text" :name="'tahapan_pelaksanaan['+idx+'][output]'" x-model="item.output" placeholder="Contoh: Notulen..." class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-sm text-white">
                                </div>
                                <div class="col-span-1 pt-6">
                                    <button type="button" @click="removeTahapan(idx)" class="w-full h-[46px] rounded-xl bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>

                            {{-- Baris 2: Timeline Checkbox --}}
                            <div class="pl-10">
                                <label class="text-[9px] uppercase text-slate-500 font-bold mb-2 block">Jadwal Pelaksanaan (Bulan)</label>
                                <div class="grid grid-cols-6 md:grid-cols-12 gap-2">
                                    @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'] as $m)
                                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                                            <input type="checkbox" :name="'tahapan_pelaksanaan['+idx+'][months][]'" value="{{ $m }}" x-model="item.months" class="hidden peer">
                                            <div class="w-full py-2 bg-slate-800 border border-slate-700 rounded-lg text-center text-[10px] font-bold text-slate-400 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-400 transition-all">
                                                {{ $m }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
                <button type="button" @click="addTahapan()" class="mt-4 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-xs font-bold text-indigo-400 hover:text-white transition-colors border border-dashed border-slate-600 w-full">+ Tambah Tahapan Baru</button>
            </div>

            <div class="flex justify-between">
                <button type="button" @click="tab = 'narasi'" class="px-6 py-3 bg-slate-800 rounded-xl text-slate-400 font-bold text-xs">Kembali</button>
                <button type="button" @click="tab = 'preview'" class="px-6 py-3 bg-indigo-600 rounded-xl text-white font-bold text-xs">Finalisasi <i class="fas fa-arrow-right ml-2"></i></button>
            </div>
        </div>

        {{-- TAB 3: PREVIEW (FINALISASI) --}}
        <div x-show="tab === 'preview'" class="space-y-6" style="display: none;" x-transition>
            
            <div class="bg-white text-black p-10 md:p-16 rounded-xl shadow-2xl overflow-hidden font-serif">
                {{-- Preview Document (Sama seperti print blade namun visual) --}}
                <div class="text-center mb-6">
                    <h3 class="font-bold text-base uppercase underline tracking-wide">KERANGKA ACUAN KERJA (KAK)</h3>
                    <p class="uppercase text-xs mt-1 font-bold">TAHUN ANGGARAN {{ date('Y') }}</p>
                </div>
                
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800 mb-6">
                    <i class="fas fa-eye mr-1"></i> Ini adalah pratinjau dokumen. Pastikan semua narasi dan matriks jadwal sudah sesuai sebelum disimpan.
                </div>

                {{-- Preview Identitas & Narasi... (Code disingkat, gunakan kode print blade untuk isi detail) --}}
                <div class="text-sm space-y-4">
                    <p><strong>1. Latar Belakang:</strong> <span x-text="latarBelakang.substring(0, 100) + '...'"></span></p>
                    <p><strong>2. Maksud:</strong> <span x-text="maksud"></span></p>
                    <p><strong>3. Tahapan & Jadwal:</strong></p>
                    <ul class="list-disc ml-5 text-xs">
                        <template x-for="item in tahapan">
                            <li>
                                <span x-text="item.uraian"></span> 
                                (<span class="text-emerald-600 font-bold" x-text="item.months ? item.months.join(', ') : '-'"></span>)
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <div class="flex justify-between items-center bg-slate-900 p-6 rounded-[2rem] border border-slate-800 mt-6">
                <button type="button" @click="tab = 'pelaksanaan'" class="text-slate-500 text-xs font-bold hover:text-white">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali Edit
                </button>
                <button type="submit" class="px-10 py-4 bg-emerald-600 text-white rounded-2xl font-bold shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan & Selesai
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function kakHandler() {
        return {
            tab: 'narasi',
            
            latarBelakang: @json($kak->latar_belakang ?? ''),
            penerimaManfaat: @json($kak->penerima_manfaat ?? ''),
            metode: @json($kak->metode_pelaksanaan ?? 'Swakelola'),
            tempat: @json($kak->tempat_pelaksanaan ?? $rka->lokasi_kegiatan),
            maksud: @json($kak->maksud ?? ''), 
            
            // Array Lists
            listTujuan: @json($kak->tujuan ?? ['']), 
            dasarHukum: @json($kak->dasar_hukum ?? ['']),
            
            // Tahapan Complex (Ada uraian, output, dan months)
            // Pastikan format defaultnya ada 'months: []'
            tahapan: @json($kak->tahapan_pelaksanaan ?? [['uraian' => '', 'output' => '', 'months' => []]]),

            // Logic Dasar Hukum
            addHukum() { this.dasarHukum.push(''); },
            removeHukum(i) { if(this.dasarHukum.length > 1) this.dasarHukum.splice(i, 1); },

            // Logic Tujuan
            addTujuan() { this.listTujuan.push(''); },
            removeTujuan(i) { if(this.listTujuan.length > 1) this.listTujuan.splice(i, 1); },

            // Logic Tahapan
            addTahapan() { 
                this.tahapan.push({uraian: '', output: '', months: []}); 
            },
            removeTahapan(i) { 
                if(this.tahapan.length > 1) this.tahapan.splice(i, 1); 
            }
        }
    }
</script>
@endsection