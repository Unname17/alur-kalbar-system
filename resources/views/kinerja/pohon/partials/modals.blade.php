{{-- 
    FILE: modals.blade.php
    STYLE: Shadcn UI Minimalist 
--}}

<div class="modal fade shadcn-ui" id="modalValidasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-white flex justify-between items-center">
                <h5 class="text-base font-bold text-slate-900 m-0 tracking-tight">Validasi Pengajuan</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="p-6">
                <div class="mb-5 p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block mb-1">Keputusan Untuk:</label>
                    <strong id="vNamaKinerja" class="text-sm text-slate-800 leading-snug">Nama Kinerja</strong>
                </div>

                <form id="formValidasi">
                    <input type="hidden" name="id" id="vNodeId">
                    
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Jenis Kinerja</label>
                        <select class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-white outline-none focus:ring-2 focus:ring-slate-200 transition-all" name="jenis_kinerja" id="inputJenis">
                            <option value="" disabled selected>-- Pilih Level --</option>
                            
                            {{-- Kelompok Strategis (Biasanya diinput oleh Admin/Bappeda) --}}
                            <optgroup label="STRATEGIS (PROVINSI)" class="text-slate-900 font-bold bg-slate-50">
                                <option value="visi">Visi</option>
                                <option value="misi">Misi</option>
                                <option value="sasaran_daerah">Sasaran Daerah</option>
                            </optgroup>

                            {{-- Kelompok OPD (Tingkat Instansi) --}}
                            <optgroup label="PERANGKAT DAERAH (OPD)" class="text-slate-900 font-bold bg-slate-50">
                                <option value="sasaran_opd">Sasaran OPD (Kepala Dinas)</option>
                                <option value="program">Program (Kabid)</option>
                                <option value="kegiatan">Kegiatan (Katim/Kasi)</option>
                                <option value="sub_kegiatan">Sub Kegiatan (Eselon IV/JF)</option>
                            </optgroup>

                            {{-- Kelompok Operasional (Individu/Staf) --}}
                            <optgroup label="OPERASIONAL & STAF" class="text-slate-900 font-bold bg-slate-50">
                                <option value="skp">SKP (Sasaran Kinerja Pegawai)</option>
                                <option value="rencana_aksi">Rencana Aksi</option>
                            </optgroup>
                        </select>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" class="px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-widest hover:text-slate-900 transition-colors" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="px-6 py-2 bg-slate-900 text-white text-xs font-bold uppercase tracking-widest rounded-lg hover:bg-slate-800 transition-all shadow-md" 
                        onclick="eksekusiValidasi()">
                    Simpan Keputusan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 
    FILE: modals.blade.php
    Update: Memperbaiki modalForm agar Sasaran OPD muncul
--}}

{{-- 1. MODAL VALIDASI (Untuk Sekretariat/Admin) --}}
<div class="modal fade shadcn-ui" id="modalValidasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-xl shadow-2xl overflow-hidden">
            {{-- Header & Info --}}
            <div class="px-6 py-4 border-b border-slate-100 bg-white flex justify-between items-center">
                <h5 class="text-base font-bold text-slate-900 m-0 tracking-tight">Validasi Pengajuan</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-6">
                <div class="mb-5 p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block mb-1">Keputusan Untuk:</label>
                    <strong id="vNamaKinerja" class="text-sm text-slate-800 leading-snug">Nama Kinerja</strong>
                </div>

                <form id="formValidasi">
                    <input type="hidden" name="id" id="vNodeId">
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Pilih Keputusan</label>
                            <select class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none" name="action">
                                <option value="setuju">Setujui Pengajuan</option>
                                <option value="tolak">Tolak Pengajuan</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Catatan / Alasan</label>
                            <textarea class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg" name="catatan" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" class="px-6 py-2 bg-slate-900 text-white text-xs font-bold uppercase rounded-lg shadow-md" onclick="eksekusiValidasi()">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- 2. MODAL FORM (UNTUK AJUKAN BARU) - BAGIAN INI YANG HARUS DIPERBAIKI --}}
<div class="modal fade shadcn-ui" id="modalForm" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-white flex justify-between items-center">
                <h5 class="text-base font-bold text-slate-900 m-0 tracking-tight">Formulir Pengajuan Data</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-6 bg-slate-50/20">
                <form id="formKinerja">
                    <input type="hidden" id="nodeId" name="id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Induk Kinerja (Parent)</label>
                            <select class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-white outline-none" name="parent_id" id="parentSelect">
                                <option value="">-- Pilih Induk --</option>
                                @if(isset($parents)) 
                                    @foreach($parents as $j => $dk) 
                                        <optgroup label="{{ strtoupper($j) }}" class="font-bold text-slate-400">
                                            @foreach($dk as $i) 
                                                <option value="{{ $i->id }}">{{ $i->nama_kinerja }}</option> 
                                            @endforeach
                                        </optgroup> 
                                    @endforeach 
                                @endif
                            </select>
                        </div>

                        {{-- BAGIAN YANG SUDAH DIPERBAIKI --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Jenis Kinerja</label>
                            <select class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-white outline-none" name="jenis_kinerja" id="inputJenis">
                                <option value="" disabled selected>-- Pilih Level --</option>
                                
                                {{-- Munculkan Sasaran OPD jika role-nya Kepala Dinas --}}
                                @if(Auth::user()->peran == 'kepala_dinas' || in_array(Auth::user()->peran, ['admin_utama', 'sekretariat']))
                                    <option value="sasaran_opd" class="font-bold text-blue-600">Sasaran OPD (Kepala Dinas)</option>
                                @endif

                                <option value="program">Program</option>
                                <option value="kegiatan">Kegiatan</option>
                                <option value="sub_kegiatan">Sub Kegiatan</option>

                                {{-- Level Staf --}}
                                @if(Auth::user()->peran == 'staf' || in_array(Auth::user()->peran, ['admin_utama', 'sekretariat']))
                                    <option value="rencana_aksi">Rencana Aksi</option>
                                    <option value="skp">SKP</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="mb-5 space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Nama / Deskripsi Kinerja</label>
                        <textarea class="w-full px-4 py-3 text-sm border border-slate-200 rounded-lg bg-white" 
                                  name="nama_kinerja" rows="3" required placeholder="Deskripsi..."></textarea>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-0">Indikator Kinerja</label>
                            <button type="button" class="px-3 py-1 bg-white border border-slate-200 text-slate-900 text-[10px] font-bold uppercase rounded-md shadow-sm" onclick="tambahBarisIndikator()">+ Tambah</button>
                        </div>
                        <div id="indikatorList" class="p-4 space-y-3"></div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-2">
                <button type="button" class="px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-widest hover:text-slate-900" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="px-8 py-2 bg-blue-600 text-white text-xs font-bold uppercase tracking-widest rounded-lg hover:bg-blue-700 transition-all shadow-lg" onclick="simpanData()">Simpan Data</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling khusus agar modal terlihat rapi */
    .shadcn-ui optgroup { padding: 8px; background: #f8fafc; }
    .shadcn-ui option { padding: 4px 8px; }
</style>