@extends('kinerja.pohon.index')

@section('page_title', 'Konfigurasi Hak Akses')

@push('css')
{{-- Library Select2 untuk fitur Multi-Select pada Izin Level --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Styling Custom Select2 agar serasi dengan Tailwind */
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #f1f5f9 !important;
        border-radius: 1rem !important;
        padding: 6px 8px !important;
        background-color: #f8fafc !important;
        min-height: 52px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #4f46e5 !important;
        background-color: #ffffff !important;
    }
    .select2-selection__choice {
        background-color: #4f46e5 !important;
        color: white !important;
        border: none !important;
        border-radius: 0.5rem !important;
        padding: 4px 12px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        margin-top: 5px !important;
        text-transform: uppercase;
    }
    .select2-selection__choice__remove {
        color: white !important;
        margin-right: 8px !important;
        border-right: 1px solid rgba(255,255,255,0.3) !important;
        padding-right: 8px !important;
    }
    .select2-selection__choice__remove:hover {
        background-color: rgba(0,0,0,0.1) !important;
    }
    
    /* Scrollbar Custom untuk Modal */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endpush

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6">
    {{-- Header & Info Waktu Server --}}
    <div class="flex justify-between items-end px-4">
        <div>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Kontrol Akses Input</h3>
            <p class="text-slate-400 text-[10px] font-bold mt-1 uppercase tracking-widest">
                Waktu Server: <span class="text-indigo-600">{{ now()->format('d/m/Y H:i') }}</span>
            </p>
        </div>
        <button onclick="openModal()" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:bg-indigo-700 transition-all">
            <i class="fas fa-plus-circle me-2"></i> Tambah Aturan Baru
        </button>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
    <div class="px-4 animate-fade">
        <div class="bg-emerald-50 text-emerald-600 px-6 py-4 rounded-2xl text-sm font-bold border border-emerald-100 flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
        </div>
    </div>
    @endif

    {{-- Tabel Data --}}
    <div class="bg-white rounded-[3rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-10">
            <table class="w-full">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">
                        <th class="pb-4 pl-4">Entitas (OPD/Pegawai)</th>
                        <th class="pb-4">Level & Akar</th>
                        <th class="pb-4">Masa Berlaku</th>
                        <th class="pb-4 text-center">Status</th>
                        <th class="pb-4 text-right pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($accessRules as $rule)
                    <tr class="hover:bg-slate-50 transition-colors">
                        {{-- Kolom 1: OPD & Pegawai --}}
                        <td class="py-6 pl-4 align-top">
                            <span class="text-sm font-black text-slate-800 block mb-1">
                                {{ $rule->perangkatDaerah->nama_pd ?? 'Semua OPD' }}
                            </span>
                            @if($rule->user_nip)
                                <div class="inline-flex items-center gap-2 bg-indigo-50 px-3 py-1 rounded-lg">
                                    <i class="fas fa-user-circle text-indigo-500 text-xs"></i>
                                    <span class="text-[10px] font-bold text-indigo-600 uppercase">{{ $rule->user->nama_lengkap ?? $rule->user_nip }}</span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 bg-slate-100 px-3 py-1 rounded-lg">
                                    <i class="fas fa-users text-slate-400 text-xs"></i>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">Seluruh Pegawai</span>
                                </div>
                            @endif
                        </td>

                        {{-- Kolom 2: Level & Goal --}}
                        <td class="py-6 align-top">
                            {{-- Badge Level dengan Warna Spesifik --}}
                            @php
                                $levelColors = [
                                    'Goal' => 'bg-pink-100 text-pink-700',
                                    'SasaranStrategis' => 'bg-orange-100 text-orange-700',
                                    'Program' => 'bg-blue-100 text-blue-700',
                                    'Activity' => 'bg-violet-100 text-violet-700',
                                    'SubActivity' => 'bg-teal-100 text-teal-700',
                                ];
                                $colorClass = $levelColors[$rule->level_izin] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="inline-block px-3 py-1 {{ $colorClass }} rounded-lg text-[10px] font-black uppercase tracking-wide mb-2">
                                {{ $rule->level_izin }}
                            </span>
                            
                            {{-- Info Root/Parent --}}
                            <p class="text-xs font-bold text-slate-500 leading-relaxed max-w-xs">
                                @if($rule->goal)
                                    <i class="fas fa-sitemap mr-1 text-slate-300"></i> {{ $rule->goal->nama_tujuan }}
                                @else
                                    <span class="italic text-slate-400">Semua Akar Pohon</span>
                                @endif
                            </p>
                        </td>

                        {{-- Kolom 3: Waktu --}}
                        <td class="py-6 align-top text-xs font-bold text-slate-500">
                            @if($rule->waktu_buka && $rule->waktu_tutup)
                                <div class="flex flex-col gap-1">
                                    <div><span class="text-slate-300 w-12 inline-block">Mulai</span> : {{ $rule->waktu_buka->format('d/m/y H:i') }}</div>
                                    <div><span class="text-slate-300 w-12 inline-block">Selesai</span> : {{ $rule->waktu_tutup->format('d/m/y H:i') }}</div>
                                </div>
                            @else
                                <span class="text-slate-300 italic">∞ Selamanya (Tanpa Batas)</span>
                            @endif
                        </td>

                        {{-- Kolom 4: Status --}}
                        <td class="py-6 align-top text-center">
                            @php
                                $now = now();
                                $manualLock = $rule->is_locked;
                                $isOutside = ($rule->waktu_buka && $rule->waktu_tutup) && ($now->lt($rule->waktu_buka) || $now->gt($rule->waktu_tutup));
                            @endphp

                            @if($manualLock)
                                <span class="px-3 py-1.5 bg-rose-100 text-rose-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-rose-200">Terkunci Manual</span>
                            @elseif($isOutside)
                                <span class="px-3 py-1.5 bg-amber-100 text-amber-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-amber-200">Jadwal Tutup</span>
                            @else
                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-200">Akses Terbuka</span>
                            @endif
                        </td>

                        {{-- Kolom 5: Aksi Hapus --}}
                        <td class="py-6 align-top text-right pr-4">
                            <form action="{{ route('kinerja.admin.access.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aturan akses ini?');">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-full bg-slate-50 text-slate-300 hover:bg-rose-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-slate-300 text-2xl"></i>
                                </div>
                                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Belum ada aturan akses yang dibuat</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL FORM --}}
<div id="modal-access" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] w-full max-w-4xl overflow-hidden shadow-2xl animate-fade">
        <form action="{{ route('kinerja.admin.access.store') }}" method="POST">
            @csrf
            
            {{-- Header Modal --}}
            <div class="bg-indigo-600 p-8 text-white flex items-center gap-5">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl shadow-inner backdrop-blur-sm">
                    <i class="fas fa-user-lock"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-tight leading-none">Konfigurasi Hak Akses Baru</h3>
                    <p class="text-[10px] font-bold text-indigo-200 uppercase mt-1">Atur izin input untuk OPD atau Pegawai tertentu</p>
                </div>
                <button type="button" onclick="closeModal()" class="ml-auto text-white/50 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-10 grid grid-cols-2 gap-8 max-h-[65vh] overflow-y-auto custom-scrollbar">
                
                {{-- 1. OPD --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Perangkat Daerah <span class="text-rose-500">*</span></label>
                    <select name="pd_id" id="pd_id" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold outline-none focus:border-indigo-500 focus:bg-white transition-all" required>
                        <option value="">-- Pilih OPD --</option>
                        @foreach($pdList as $pd)
                            <option value="{{ $pd->id }}">{{ $pd->nama_pd }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. Pegawai (AJAX) --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pegawai Spesifik (Opsional)</label>
                    <select name="user_nip" id="user_nip" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold outline-none focus:border-indigo-500 focus:bg-white transition-all">
                        <option value="">-- Seluruh Pegawai di OPD --</option>
                    </select>
                </div>

                {{-- 3. Parent ID (AJAX) --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Akar Pohon / Parent (Opsional)</label>
                    <select name="parent_id" id="parent_id" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold outline-none focus:border-indigo-500 focus:bg-white transition-all">
                        <option value="">-- Semua Root Pohon --</option>
                    </select>
                </div>
                
                {{-- 4. LEVEL IZIN (MULTI-SELECT) --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Izin Level (Bisa Pilih Banyak) <span class="text-rose-500">*</span></label>
                    {{-- Value di sini HARUS SINKRON dengan Middleware CheckInputAkses.php --}}
                    <select name="level_izin[]" id="level_izin" class="select2-multi w-full" multiple="multiple" required>
                        <option value="Goal">Tujuan PD (Langkah 1)</option>
                        <option value="SasaranStrategis">Sasaran Strategis (Langkah 2)</option>
                        <option value="Program">Program (Langkah 3)</option>
                        <option value="Activity">Kegiatan (Langkah 4)</option>
                        <option value="SubActivity">Sub-Kegiatan (Langkah 5)</option>
                    </select>
                </div>

                {{-- 5. Waktu --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu Buka</label>
                    <input type="datetime-local" name="waktu_buka" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold outline-none focus:border-indigo-500 transition-colors">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu Tutup</label>
                    <input type="datetime-local" name="waktu_tutup" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold outline-none focus:border-indigo-500 transition-colors">
                </div>

                {{-- 6. Pesan Blokir --}}
                <div class="col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Alasan Blokir / Pesan Info</label>
                    <textarea name="pesan_blokir" rows="2" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold outline-none focus:border-indigo-500 transition-colors" placeholder="Contoh: Batas waktu input RKA telah berakhir..."></textarea>
                </div>
            </div>

            {{-- Footer Modal --}}
            <div class="p-10 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                <button type="button" onclick="closeModal()" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Batal</button>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-3 cursor-pointer select-none group">
                        <input type="checkbox" name="is_locked" value="1" class="w-5 h-5 rounded-lg border-2 border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span class="text-[10px] font-black text-slate-400 group-hover:text-rose-500 uppercase tracking-widest transition-colors">Paksa Kunci Manual</span>
                    </label>
                    <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[2px] shadow-xl hover:bg-indigo-700 hover:shadow-2xl hover:-translate-y-1 transition-all">
                        Simpan Aturan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk Izin Level
        $('#level_izin').select2({
            placeholder: "Pilih Level (Bisa lebih dari satu)",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modal-access')
        });

        // Logika Dropdown Berantai (Cascading Dropdown)
        $('#pd_id').on('change', function() {
            const pd_id = $(this).val();
            
            // Reset dropdown anak
            $('#user_nip').html('<option value="">-- Seluruh Pegawai di OPD --</option>');
            $('#parent_id').html('<option value="">-- Semua Root Pohon --</option>');

            if (!pd_id) return;

            // PERBAIKAN DI SINI: Gunakan URL String manual (Backticks)
            // 1. Ambil Data Pegawai
            $.get(`/kinerja/admin/fetch-pegawai/${pd_id}`, function(data) {
                let html = '<option value="">-- Seluruh Pegawai di OPD --</option>';
                data.forEach(u => html += `<option value="${u.nip}">${u.nama_lengkap}</option>`);
                $('#user_nip').html(html);
            });

            // 2. Ambil Data Goals/Pohon
            $.get(`/kinerja/admin/fetch-goals/${pd_id}`, function(data) {
                let html = '<option value="">-- Semua Root Pohon --</option>';
                data.forEach(g => html += `<option value="${g.id}">${g.nama_tujuan}</option>`);
                $('#parent_id').html(html);
            });
        });
    });

    function openModal() { $('#modal-access').removeClass('hidden').addClass('flex'); }
    function closeModal() { $('#modal-access').addClass('hidden').removeClass('flex'); }
</script>
@endpush