@extends('kinerja.pohon.index')

@section('title', 'Kunci Akses')
@section('page_title', 'Konfigurasi Hak Akses Input')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: { preflight: false }
    }
</script>
<style>
    .shadcn-ui { font-family: 'Inter', sans-serif; }
    .content-area-compact { margin-top: -15px; }
    
    /* Perbaikan Select2 agar serasi dengan Shadcn */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        padding: 2px !important;
        background-color: #ffffff !important;
    }

    /* Animasi Halus */
    .animate-in { animation: fadeIn 0.2s ease-out; }
    .zoom-in { animation: zoomIn 0.2s ease-out; }
    .slide-in { animation: slideIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes zoomIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

    /* Mencegah konflik visibility Tailwind */
    .form-wrapper-visible { visibility: visible !important; }
</style>
@endpush

@section('content')
<div class="shadcn-ui antialiased content-area-compact px-2">
    {{-- BAGIAN 1: FORM PENGATURAN (MENGGUNAKAN JQUERY SLIDE UNTUK STABILITAS) --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition-colors" 
             id="btn-toggle-form">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-slate-100 rounded-lg text-slate-600"><i class="fas fa-key text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 m-0 tracking-tight" id="form-title">Pengaturan Buka Kunci Akses Baru</h3>
                    <p class="text-[11px] text-slate-500 m-0">Kelola izin penginputan data untuk instansi atau pegawai.</p>
                </div>
            </div>
            <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-300" id="chevron-icon"></i>
        </div>
        
        {{-- MENGHAPUS CLASS 'collapse' UNTUK MENGHINDARI KONFLIK TAILWIND --}}
        <div id="formAksesWrapper" class="hidden border-t border-slate-100 bg-slate-50/30 form-wrapper-visible">
            <div class="p-6">
                <form action="{{ route('kinerja.akses.store') }}" method="POST" id="form-akses">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">1. OPD</label>
                            <select name="opd_id" id="select-opd" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none transition-all" required>
                                <option value="">-- Pilih Instansi --</option>
                                @foreach($listOpd as $opd) 
                                    <option value="{{ $opd->id }}">{{ $opd->nama_perangkat_daerah }}</option> 
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">2. Pegawai</label>
                            <select name="user_id" id="select-user" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none transition-all">
                                <option value="">-- Seluruh Pegawai di OPD --</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">3. Akar Pohon</label>
                            <select name="parent_id_allowed" id="select-parent" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none transition-all" required>
                                <option value="">-- Pilih Ranting --</option>
                                @foreach($allNodes as $node) 
                                    <option value="{{ $node->id }}">[{{ strtoupper($node->jenis_kinerja) }}] {{ $node->nama_kinerja }}</option> 
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">4. Level Izin</label>
                            <select name="jenis_kinerja_allowed[]" id="select-level" class="w-full select2" multiple required>
                                <option value="visi">Visi</option>
                                <option value="misi">Misi</option>
                                <option value="sasaran_opd">Sasaran OPD (Kepala Dinas)</option>
                                <option value="program">Program</option>
                                <option value="kegiatan">Kegiatan</option>
                                <option value="sub_kegiatan">Sub Kegiatan</option>
                                <option value="skp">SKP</option>
                                <option value="rencana_aksi">Rencana Aksi</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Mulai Dibuka</label>
                            <input type="datetime-local" name="start_date" id="input-start" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none transition-all" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Batas Akhir</label>
                            <input type="datetime-local" name="end_date" id="input-end" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none transition-all" required>
                        </div>
                        <div class="lg:col-span-2 flex items-end gap-2">
                            <button type="submit" class="flex-grow py-2.5 bg-slate-900 text-white text-xs font-bold uppercase rounded-lg border-0 shadow-md cursor-pointer transition-all hover:bg-slate-800">
                                <span id="btn-text">Aktifkan Kunci Akses</span>
                            </button>
                            <button type="button" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 text-xs font-bold uppercase rounded-lg hidden transition-all hover:bg-slate-50" id="btn-cancel" onclick="resetForm()">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: TABEL --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 w-full" style="border-collapse: collapse;">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-bottom border-slate-100">Target</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-bottom border-slate-100 text-center">Izin Ranting</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-bottom border-slate-100 text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-bottom border-slate-100 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rules as $rule)
                        @php $isExpired = now() > $rule->end_date; @endphp
                        <tr class="{{ $isExpired ? 'bg-red-50/20' : '' }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-900">{{ $rule->opd->nama_perangkat_daerah ?? 'SISTEM' }}</div>
                                <div class="text-[11px] font-semibold text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <i class="fas fa-user-circle"></i> {{ $rule->user->nama_lengkap ?? 'Seluruh Pegawai' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-xs font-bold text-slate-700 truncate max-w-[180px] mx-auto">{{ $rule->parentNode->nama_kinerja }}</div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">LEVEL: {{ strtoupper($rule->jenis_kinerja_allowed) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 {{ $isExpired ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }} text-[10px] font-bold rounded-full">
                                    {{ $isExpired ? 'TERKUNCI' : 'TERBUKA' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-1">
                                    <button class="btn btn-link p-2 text-slate-400 hover:text-slate-900" onclick='editAkses(@json($rule))'>
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button class="btn btn-link p-2 text-slate-400 hover:text-red-600" onclick="confirmDeleteAkses({{ $rule->id }})">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-12 text-center text-slate-400 italic text-sm">Belum ada konfigurasi akses yang terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('#select-level').select2({ placeholder: " Pilih Level...", width: '100%' });

    // PENGGANTIAN COLLAPSE BOOTSTRAP DENGAN JQUERY SLIDE
    $('#btn-toggle-form').on('click', function() {
        $('#formAksesWrapper').slideToggle(300);
        $('#chevron-icon').toggleClass('rotate-180');
    });

    // Cascading OPD
    $('#select-opd').on('change', function() {
        let opdId = $(this).val();
        let $userSelect = $('#select-user');
        if (opdId) {
            $.get("{{ url('kinerja/api/users-by-opd') }}/" + opdId, function(data) {
                $userSelect.html('<option value="">-- Seluruh Pegawai di OPD --</option>');
                $.each(data, function(k, v) { $userSelect.append(`<option value="${v.id}">${v.nama_lengkap}</option>`); });
            });
        }
    });

    @if(session('success')) showShadcnToast("{{ session('success') }}"); @endif
});

/**
 * FUNGSI EDIT MENGGUNAKAN SLIDEDOWN (STABIL)
 */
function editAkses(data) {
    $('#form-title').text('Edit / Perpanjang Akses Input');
    $('#btn-text').text('Perbarui Hak Akses');
    $('#btn-cancel').removeClass('hidden');
    
    $('#form-akses').attr('action', `/kinerja/akses/${data.id}`);
    $('#form-method').val('PUT');
    
    $('#select-opd').val(data.opd_id).trigger('change');
    setTimeout(() => { $('#select-user').val(data.user_id); }, 700);
    $('#select-parent').val(data.parent_id_allowed);
    $('#select-level').val([data.jenis_kinerja_allowed]).trigger('change');
    $('#input-start').val(data.start_date.substring(0, 16).replace(' ', 'T'));
    $('#input-end').val(data.end_date.substring(0, 16).replace(' ', 'T'));
    
    // Pastikan form terbuka dengan SlideDown
    $('#formAksesWrapper').slideDown(300);
    $('#chevron-icon').addClass('rotate-180');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    $('#form-title').text('Pengaturan Buka Kunci Akses Baru');
    $('#btn-text').text('Aktifkan Kunci Akses');
    $('#btn-cancel').addClass('hidden');
    $('#form-akses').attr('action', "{{ route('kinerja.akses.store') }}");
    $('#form-method').val('POST');
    $('#form-akses')[0].reset();
    $('#select-level').val(null).trigger('change');
}

function confirmDeleteAkses(id) {
    const dialogHtml = `
    <div id="shadcn-dialog" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in">
        <div class="bg-white w-full max-w-[400px] rounded-xl border border-slate-200 shadow-2xl p-6 zoom-in shadcn-ui">
            <h3 class="text-lg font-semibold text-slate-900 m-0">Cabut Akses Input?</h3>
            <p class="text-sm text-slate-500 mt-2 mb-6 leading-relaxed">Izin penginputan data untuk instansi/pegawai ini akan dicabut secara permanen.</p>
            <div class="flex justify-end gap-2">
                <button onclick="$('#shadcn-dialog').remove()" class="btn btn-light border text-sm px-4 shadow-none cursor-pointer">Batal</button>
                <form action="{{ url('kinerja/akses') }}/${id}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger text-sm px-4 shadow-none cursor-pointer">Ya, Cabut</button>
                </form>
            </div>
        </div>
    </div>`;
    $('body').append(dialogHtml);
}

function showShadcnToast(message) {
    const toastHtml = `<div id=\"shadcn-toast\" class=\"fixed bottom-6 right-6 z-[9999] flex items-center gap-3 bg-slate-900 text-white shadow-2xl rounded-lg px-5 py-3 slide-in\"><i class=\"fas fa-check-circle text-emerald-400 text-xs\"></i><div class=\"text-sm font-medium tracking-tight\">${message}</div></div>`;
    $('body').append(toastHtml);
    setTimeout(() => { $('#shadcn-toast').fadeOut(300, function() { $(this).remove(); }); }, 4000);
}
</script>
@endpush