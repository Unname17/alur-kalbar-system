{{-- 
    FILE: log_modals.blade.php
    FITUR LENGKAP: 
    1. Modal Filter: Menyaring tabel berdasarkan Tanggal, OPD, dan Aktivitas
    2. Modal Manajemen Opsi 1: Export Excel dengan ATUR RENTANG TANGGAL
    3. Modal Manajemen Opsi 2: Arsip & Hapus Data Lama
--}}

<div class="modal fade shadcn-ui antialiased" id="modalFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-2xl bg-white">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest m-0">Filter Riwayat Log</h3>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="p-6">
                {{-- Form ID 'filter-form' terhubung ke fungsi loadData() di log.blade.php --}}
                <form id="filter-form">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Dari Tanggal</label>
                                <input type="date" name="start_date" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg outline-none focus:border-slate-400 transition-all bg-slate-50/50">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg outline-none focus:border-slate-400 transition-all bg-slate-50/50">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Perangkat Daerah (OPD)</label>
                            <select name="opd_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg outline-none bg-white">
                                <option value="">Semua OPD</option>
                                {{-- Proteksi agar tidak error Undefined Variable $opds --}}
                                @if(isset($opds))
                                    @foreach($opds as $o)
                                        <option value="{{ $o->id }}">{{ $o->nama_perangkat_daerah }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Jenis Aktivitas</label>
                            <select name="aktivitas" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg outline-none bg-white font-bold text-slate-700 uppercase">
                                <option value="">Semua Aktivitas</option>
                                <option value="AKSES_MODUL">Akses Modul</option>
                                <option value="TAMBAH_DATA">Tambah Data</option>
                                <option value="UBAH_DATA">Ubah Data</option>
                                <option value="HAPUS_DATA">Hapus Data</option>
                                <option value="SETUJU_DATA">Setuju Data</option>
                                <option value="TOLAK_DATA">Tolak Data</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex gap-2">
                        <button type="button" onclick="$('#filter-form')[0].reset(); loadData();" class="flex-grow py-2 text-xs font-bold text-slate-400 uppercase tracking-widest border-0 bg-transparent cursor-pointer">Reset</button>
                        <button type="submit" class="flex-grow py-2.5 bg-slate-900 text-white text-xs font-bold uppercase tracking-widest rounded-lg border-0 shadow-lg cursor-pointer">Terapkan Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade shadcn-ui antialiased" id="modalManagementLog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-2xl shadow-2xl overflow-hidden bg-white">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest m-0">Ekspor & Manajemen Data</h3>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="p-6 space-y-5 bg-slate-50/30">
                
                {{-- OPSI 1: EKSPOR EXCEL DENGAN ATUR RENTANG TANGGAL --}}
                <div class="p-5 border border-slate-200 rounded-xl bg-white hover:border-slate-400 transition-all shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                            <i class="fas fa-file-excel fa-lg"></i>
                        </div>
                        <div class="flex-grow">
                            <h4 class="text-xs font-bold text-slate-900 mb-1">Opsi 1: Export Excel (Rentang Tanggal)</h4>
                            <p class="text-[11px] text-slate-500 mb-4">Unduh laporan ke format .xlsx berdasarkan tanggal pilihan Anda.</p>
                            
                            <form action="{{ route('kinerja.log.export') }}" method="GET" class="space-y-3">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-bold text-slate-400 uppercase">Mulai</label>
                                        <input type="date" name="start_date" class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-md outline-none bg-slate-50">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-bold text-slate-400 uppercase">Selesai</label>
                                        <input type="date" name="end_date" class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-md outline-none bg-slate-50">
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-2 bg-slate-900 text-white text-[10px] font-bold uppercase rounded-lg border-0 shadow-md cursor-pointer">
                                    <i class="fas fa-download mr-1"></i> Unduh File Excel
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- OPSI 2: ARSIP & PEMBERSIHAN DATA --}}
                <div class="p-5 border border-amber-100 bg-amber-50/40 rounded-xl shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-amber-100 text-amber-600 rounded-lg">
                            <i class="fas fa-archive fa-lg"></i>
                        </div>
                        <div class="flex-grow">
                            <h4 class="text-xs font-bold text-amber-900 mb-1">Opsi 2: Arsip & Hapus Data</h4>
                            <p class="text-[11px] text-amber-700/70 mb-4">Pindahkan data lama ke arsip server, lalu hapus dari database aktif.</p>
                            
                            <form action="{{ route('kinerja.log.archive') }}" method="POST" class="space-y-3">
                                @csrf
                                <select name="auto_delete_months" class="w-full px-3 py-2 text-xs border border-amber-200 rounded-lg outline-none bg-white font-bold">
                                    <option value="3">Data > 3 Bulan terakhir</option>
                                    <option value="6">Data > 6 Bulan terakhir</option>
                                    <option value="12">Data > 1 Tahun terakhir</option>
                                </select>
                                <button type="submit" class="w-full py-2.5 bg-amber-600 text-white text-[10px] font-bold uppercase rounded-lg border-0 cursor-pointer" onclick="return confirm('Pindahkan data ke arsip server dan bersihkan database?')">
                                    Jalankan Pembersihan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kebijakan Auto-Delete (1 Tahun) --}}
            <div class="px-6 py-4 bg-white border-t border-slate-100 text-center">
                <p class="text-[9px] text-slate-400 italic m-0">
                    *Arsip di server akan dihapus otomatis setelah berumur 1 tahun sesuai kebijakan sistem **Alur-Kalbar**.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling agar input date & select terlihat lebih bersih */
    .shadcn-ui input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.5; }
    .shadcn-ui select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 1.5em 1.5em;
    }
</style>