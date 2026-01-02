@extends('kinerja.pohon.index')

{{-- 1. MASUKKAN JUDUL KE NAVBAR --}}
@section('title', 'Audit Trail Aktivitas')
@section('page_title', 'Audit Trail Aktivitas')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false, // Penting agar layout index.blade.php tidak hancur
        }
    }
</script>
<style>
    .shadcn-ui { font-family: 'Inter', sans-serif; }
    .loading-overlay { opacity: 0.4; pointer-events: none; transition: opacity 0.2s; }
    
    /* Animasi Dialog Shadcn */
    .animate-in { animation: fadeIn 0.2s ease-out; }
    .zoom-in { animation: zoomIn 0.2s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes zoomIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    /* CSS Tambahan agar konten mepet ke atas navbar */
    .content-area-compact {
        margin-top: -10px;
    }
</style>
@endpush

@section('content')
<div class="shadcn-ui antialiased content-area-compact">
    
    {{-- 2. HEADER AREA (HANYA UNTUK SEARCH & SETTING) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            {{-- Teks deskripsi kecil di bawah navbar --}}
            <p class="text-sm text-slate-500 m-0">Riwayat aktivitas sistem modul kinerja secara real-time.</p>
        </div>
        
        <div class="flex items-center gap-2">
            {{-- Search Bar --}}
            <div class="relative group">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs transition-colors group-focus-within:text-slate-900"></i>
                <input type="text" id="live-search" 
                       class="w-56 pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-400 transition-all bg-white shadow-sm" 
                       placeholder="Cari aktivitas...">
            </div>
            
            <button onclick="$('#modalFilter').modal('show')" class="btn btn-light border p-2 rounded-lg bg-white shadow-sm" title="Filter Laporan">
                <i class="fas fa-filter text-muted text-sm"></i>
            </button>
            
<button type="button" 
        class="p-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all border-0 cursor-pointer shadow-sm"
        data-bs-toggle="modal" 
        data-bs-target="#modalManagementLog" 
        title="Manajemen Log & Export">
    <i class="fas fa-cog text-sm"></i>
</button>
        </div>
    </div>

    {{-- 3. TABEL UTAMA --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 w-full" style="border-collapse: collapse;">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-bottom border-slate-100">Waktu</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-bottom border-slate-100">Pelaku & Instansi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-bottom border-slate-100">Aktivitas & Deskripsi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-bottom border-slate-100 text-center">Modul</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-bottom border-slate-100 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="log-data-container" class="bg-white divide-y divide-slate-100">
                    @include('kinerja.admin.log_table')
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('kinerja.admin.log_modals') 
@endsection

@push('js')
<script>
// Fungsi loadData tetap global agar bisa diakses deleteLogManual
function loadData(page = 1) {
    const search = $('#live-search').val();
    const formData = $('#filter-form').serialize();
    const url = `{{ route('kinerja.log.index') }}?page=${page}&search=${search}&${formData}`;

    $('#log-data-container').addClass('loading-overlay');
    $.ajax({
        url: url,
        type: "GET",
        success: function(html) {
            $('#log-data-container').html(html).removeClass('loading-overlay');
        }
    });
}

$(document).ready(function() {
    loadData();
    let typingTimer;
    $('#live-search').on('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(loadData, 500);
    });

    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        loadData();
        $('#modalFilter').modal('hide');
    });
});

function deleteLogManual(id) {
    const dialogHtml = `
    <div id="shadcn-dialog" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in">
        <div class="bg-white w-full max-w-[400px] rounded-xl border border-slate-200 shadow-2xl p-6 zoom-in shadcn-ui">
            <h3 class="text-lg font-semibold text-slate-900 m-0 tracking-tight">Hapus Permanen?</h3>
            <p class="text-sm text-slate-500 mt-2 mb-6 leading-relaxed">Tindakan ini akan menghapus catatan aktivitas ini selamanya dari database.</p>
            <div class="flex justify-end gap-2">
                <button onclick="$('#shadcn-dialog').remove()" class="btn btn-light border text-sm px-4">Batal</button>
                <button id="confirm-delete-btn" class="btn btn-danger text-sm px-4 shadow-sm">Ya, Hapus</button>
            </div>
        </div>
    </div>`;
    $('body').append(dialogHtml);

    $('#confirm-delete-btn').on('click', function() {
        $.ajax({
            url: `{{ url('kinerja/log') }}/${id}`,
            type: "POST",
            data: { _token: "{{ csrf_token() }}", _method: "DELETE" },
            success: function() {
                $('#shadcn-dialog').remove();
                loadData();
            }
        });
    });
}
</script>
@endpush