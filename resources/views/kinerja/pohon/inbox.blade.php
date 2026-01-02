@extends('kinerja.pohon.index')

@section('title', 'Inbox Validasi')
@section('page_title', 'Inbox Pengajuan Masuk')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: { preflight: false } // Mencegah bentrok dengan sidebar
    }
</script>
<style>
    .shadcn-ui { font-family: 'Inter', sans-serif; }
    .content-area-compact { margin-top: -15px; }
    .status-group .btn-check:checked + .btn { background-color: #0f172a !important; color: white !important; }
    .status-group .btn { border: 1px solid #e2e8f0; background: white; color: #64748b; font-size: 0.75rem; font-weight: 600; }
    .animate-in { animation: fadeIn 0.2s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    
    /* Grid Consistency */
    .m-header, .m-row {
        display: grid !important;
        grid-template-columns: 120px 1fr 250px 120px 150px !important;
        min-width: 900px;
    }
</style>
@endpush

@section('content')
<div class="shadcn-ui antialiased content-area-compact px-2">
    {{-- Filter Bar --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 mb-6">
        <form id="searchForm" class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
            <div class="md:col-span-5 space-y-1.5">
                <label class="text-[10px] font-bold uppercase text-slate-400">Cari Kinerja</label>
                <div class="relative group">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="q" id="ajaxSearch" class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none" placeholder="Cari program/kegiatan...">
                </div>
            </div>
            @if($isValidator)
            <div class="md:col-span-4 space-y-1.5">
                <label class="text-[10px] font-bold uppercase text-slate-400">Perangkat Daerah</label>
                <select name="opd_id" id="ajaxOpd" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none">
                    <option value="">Semua OPD</option>
                    @foreach($opds as $o) <option value="{{ $o->id }}">{{ $o->nama_perangkat_daerah }}</option> @endforeach
                </select>
            </div>
            @endif
            <div class="md:col-span-3 space-y-1.5">
                <label class="text-[10px] font-bold uppercase text-slate-400">Status</label>
                <div class="status-group flex p-1 bg-slate-100 rounded-lg">
                    <div class="flex-grow"><input type="radio" class="btn-check ajaxStatus" name="status" id="st1" value="all"><label class="btn border-0 w-full py-1.5 mb-0" for="st1">Semua</label></div>
                    <div class="flex-grow"><input type="radio" class="btn-check ajaxStatus" name="status" id="st2" value="pengajuan" checked><label class="btn border-0 w-full py-1.5 mb-0" for="st2">Pending</label></div>
                </div>
            </div>
        </form>
    </div>

    <div id="inboxTableContainer">
        @include('kinerja.pohon.partial-inbox-table')
    </div>
</div>
@endsection

@push('js')
<script>
    /**
     * FUNGSI GLOBAL: eksekusiValidasi
     * Harus berada di luar $(document).ready agar bisa dipanggil oleh onclick di modal
     */
    function eksekusiValidasi() {
        const id = $('#vNodeId').val();
        const urlApprove = "{{ url('kinerja/approve') }}/" + id;
        const formData = $('#formValidasi').serialize();

        // Berikan efek loading pada tombol agar tidak diklik dua kali
        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = "MEMPROSES...";
        btn.disabled = true;

        $.ajax({
            url: urlApprove,
            method: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                // Tutup modal menggunakan instance Bootstrap 5
                const modalEl = document.getElementById('modalValidasi');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                // Tampilkan Alert Gaya Shadcn (Toast)
                showShadcnToast(res.message || "Validasi berhasil diproses!");

                // Segarkan tabel tanpa reload halaman penuh
                setTimeout(() => { 
                    location.reload(); 
                }, 1000);
            },
            error: function(xhr) {
                btn.innerText = originalText;
                btn.disabled = false;
                showShadcnToast("Gagal: " + (xhr.responseJSON?.message || "Terjadi kesalahan sistem"));
            }
        });
    }

    /**
     * FUNGSI GLOBAL: showShadcnToast
     */
    function showShadcnToast(message) {
        // Hapus toast lama jika ada
        $('#shadcn-toast').remove();

        const toastHtml = `
            <div id="shadcn-toast" class="fixed bottom-6 right-6 z-[9999] flex items-center gap-3 bg-slate-900 text-white shadow-2xl rounded-lg px-5 py-3 animate-in fade-in slide-in-from-right duration-300">
                <i class="fas fa-check-circle text-emerald-400 text-xs"></i>
                <div class="text-sm font-medium tracking-tight">${message}</div>
            </div>`;
        
        $('body').append(toastHtml);
        
        // Hilangkan otomatis setelah 4 detik
        setTimeout(() => {
            $('#shadcn-toast').fadeOut(300, function() { $(this).remove(); });
        }, 4000);
    }

    /**
     * FUNGSI GLOBAL: toggleDropdown
     */
    function toggleDropdown(id) {
        const target = $(`.child-of-${id}`);
        if (target.hasClass('hidden')) {
            target.hide().removeClass('hidden').slideDown(200);
        } else {
            target.slideUp(200, function() { $(this).addClass('hidden'); });
        }
        $(`#icon-${id}`).toggleClass('rotate-90');
    }

    /**
     * EVENT HANDLER: Dimasukkan ke dalam ready
     */
    $(document).ready(function() {
        let delayTimer;
        // AJAX Real-time Search
        $('#ajaxSearch, #ajaxOpd, .ajaxStatus').on('keyup change', function() {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(function() {
                const queryParams = $('#searchForm').serialize();
                $('#inboxTableContainer').css('opacity', '0.5');

                $.ajax({
                    url: "{{ route('kinerja.inbox') }}",
                    data: queryParams,
                    success: function(response) {
                        $('#inboxTableContainer').html(response).css('opacity', '1');
                    }
                });
            }, 400);
        });
    });
</script>
@endpush