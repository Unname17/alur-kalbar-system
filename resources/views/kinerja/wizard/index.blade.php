@extends('kinerja.pohon.index')

@section('title', 'Wizard Cascading')
@section('page_title', 'Penyusunan & Pembaruan Cascading')

@push('css')
<style>
    /* 1. Desain Panah Progress (Chevron) */
    .arrow-steps .step {
        font-size: 11px; font-weight: 800; color: #94a3b8; cursor: pointer;
        padding: 15px 10px 15px 35px; float: left; position: relative;
        background-color: #f1f5f9; user-select: none; transition: all 0.2s ease;
        flex: 1; text-align: center; text-transform: uppercase; letter-spacing: 1px;
    }
    .arrow-steps .step:after, .arrow-steps .step:before {
        content: " "; position: absolute; top: 0; right: -17px; width: 0; height: 0;
        border-top: 25px solid transparent; border-bottom: 25px solid transparent;
        border-left: 17px solid #f1f5f9; z-index: 2; transition: border-color 0.2s ease;
    }
    .arrow-steps .step:before { right: auto; left: 0; border-left: 17px solid #fff; z-index: 0; }
    .arrow-steps .step:first-child:before { border: none; }
    .arrow-steps .step:first-child { border-top-left-radius: 1rem; border-bottom-left-radius: 1rem; padding-left: 20px; }
    .arrow-steps .step:last-child { border-top-right-radius: 1rem; border-bottom-right-radius: 1rem; }
    .arrow-steps .step:last-child:after { border: none; }

    /* 2. States & Hover */
    .arrow-steps .step.active { color: #fff; background-color: #1e293b; }
    .arrow-steps .step.active:after { border-left-color: #1e293b; }
    .arrow-steps .step.completed { color: #fff; background-color: #10b981; }
    .arrow-steps .step.completed:after { border-left-color: #10b981; }
    .arrow-steps .step:hover { background-color: #e2e8f0; }

    .nav-tip {
        display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px;
        background: #fdf2f2; border: 1px solid #fee2e2; border-radius: 99px;
        color: #b91c1c; font-size: 10px; font-weight: 800; margin-top: 15px;
        animation: pulse-soft 2s infinite;
    }
    @keyframes pulse-soft { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.02); } }
    .animate-fade { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .status-badge {
        font-size: 9px; font-weight: 900; text-transform: uppercase;
        padding: 4px 12px; border-radius: 8px; letter-spacing: 1px;
    }

    .btn-floating-revisi {
        position: fixed; bottom: 40px; right: 40px; z-index: 50;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-floating-revisi:hover { transform: scale(1.1); }

    /* CSS UNTUK LOCKING */
    .form-locked { pointer-events: none; opacity: 0.6; user-select: none; }
</style>
@endpush

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6 relative">
    
    {{-- ALERT AKSES DIKUNCI (Muncul jika isLocked true) --}}
    @if($isLocked)
    <div class="col-span-12 animate-fade">
        <div class="bg-rose-50 border-2 border-rose-100 p-8 rounded-[2.5rem] flex items-start gap-6 shadow-sm">
            <div class="w-16 h-16 bg-rose-500 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-rose-200">
                <i class="fas fa-lock text-white text-2xl"></i>
            </div>
            <div>
                <h4 class="text-xl font-black text-rose-900 tracking-tight uppercase">Akses Input Ditutup</h4>
                <p class="text-rose-700 font-bold mt-1 leading-relaxed">
                    {{ $access->pesan_blokir ?? 'Maaf, batas waktu penginputan cascading telah berakhir. Silakan hubungi Admin Bappeda untuk informasi lebih lanjut.' }}
                </p>
                <div class="mt-4 flex gap-3">
                    <span class="px-3 py-1 bg-white border border-rose-200 rounded-lg text-[10px] font-black text-rose-500 uppercase tracking-widest">
                        Status: Locked by Bappeda
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- NAVIGATION TOP --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
        <div class="arrow-steps flex overflow-hidden">
            @foreach(['Tujuan PD', 'Sasaran Strategis', 'Program', 'Kegiatan', 'Sub-Kegiatan'] as $i => $label)
                <div class="step {{ $i == 0 ? 'active' : '' }}" id="step-nav-{{ $i+1 }}" onclick="jumpToStep({{ $i+1 }})">
                    <span>{{ $i+1 }}. {{ $label }}</span>
                </div>
            @endforeach
        </div>
        <div class="nav-tip">
            <i class="fas fa-lightbulb"></i>
            <span>Tip: Klik panah untuk pindah level. Gunakan tombol merah di pojok kanan bawah untuk melihat daftar revisi.</span>
        </div>
    </div>

    {{-- FORM BODY --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden animate-fade">
        <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div>
                <h2 id="step-title" class="text-2xl font-black text-slate-800 tracking-tight">Tentukan Tujuan PD</h2>
                <div id="status-display" class="mt-2 flex items-center gap-2">
                    <span id="badge-status" class="status-badge bg-slate-100 text-slate-500">Draft / Baru</span>
                </div>
            </div>
            <div id="badge-step" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest">
                Langkah 1 / 5
            </div>
        </div>

        {{-- Tambahkan class form-locked jika isLocked true --}}
        <form id="formWizard" class="p-10 space-y-8 transition-all duration-500 {{ $isLocked ? 'form-locked' : '' }}">
            {{-- ALERT REVISI --}}
            <div id="alert-revisi" class="hidden col-span-12 bg-rose-50 p-6 rounded-2xl border border-rose-100 flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center shrink-0">
                    <i class="fas fa-undo-alt text-white"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest block mb-1">Catatan Revisi Atasan:</span>
                    <p id="catatan-revisi" class="text-sm font-bold text-rose-900 leading-relaxed"></p>
                </div>
            </div>

            <input type="hidden" id="existing_id" value="">
            
            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-12 bg-indigo-50 p-5 rounded-2xl border border-indigo-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-calendar-check text-indigo-600"></i>
                        <span class="text-xs font-black text-indigo-900 uppercase">Tahun Perencanaan:</span>
                    </div>
                    <select id="tahun_input" class="bg-white border-none rounded-xl text-sm font-black px-6 py-2 shadow-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(range(2025, 2030) as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-6">
                    <label id="label-parent" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Pilih Induk Data</label>
                    <select id="parent_id" onchange="loadExisting(this.value)" class="w-full bg-slate-100/50 border-slate-200 rounded-2xl p-4 text-sm font-bold focus:ring-4 focus:ring-indigo-100 outline-none">
                        <option value="">-- Pilih Induk --</option>
                    </select>
                </div>
                <div class="col-span-6">
                    <label class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block mb-3">
                        <i class="fas fa-edit me-1"></i> Update Data Existing?
                    </label>
                    <select id="existing_select" onchange="fillForm(this.value)" class="w-full bg-indigo-50 border-indigo-100 rounded-2xl p-4 text-sm font-bold focus:ring-4 focus:ring-indigo-100 outline-none">
                        <option value="">-- Input Baru --</option>
                    </select>
                </div>

                <div class="col-span-12">
                    <label id="label-nama" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Nama Uraian</label>
                    <textarea id="nama" rows="3" class="w-full bg-white border-2 border-slate-100 rounded-2xl p-5 text-sm font-bold focus:border-indigo-500 outline-none"></textarea>
                </div>

                <div class="col-span-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Indikator Kinerja</label>
                    <input type="text" id="indikator" class="w-full bg-white border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-indigo-500 outline-none">
                </div>
                <div class="col-span-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Satuan</label>
                    <input type="text" id="satuan" class="w-full bg-white border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-indigo-500 outline-none">
                </div>
                <div class="col-span-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Nilai Target</label>
                    <input type="text" id="target_value" class="w-full bg-white border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-indigo-500 outline-none">
                </div>

                <div id="box-extra" class="hidden col-span-12 grid grid-cols-2 gap-8 pt-8 border-t border-slate-100">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Tipe Perhitungan</label>
                        <select id="tipe_perhitungan" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 text-sm font-bold">
                            <option value="Akumulasi">Akumulasi</option>
                            <option value="Non-Akumulasi">Non-Akumulasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Klasifikasi Indikator</label>
                        <select id="klasifikasi" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 text-sm font-bold">
                            <option value="IKK">IKK (Kunci)</option>
                            <option value="IKU">IKU (Utama)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-10 flex justify-between items-center border-t border-slate-50">
                <button type="button" onclick="location.reload()" class="px-8 py-4 text-slate-400 font-bold text-xs uppercase hover:text-rose-500">Reset</button>
                
                {{-- Sembunyikan tombol simpan jika isLocked true --}}
                @if(!$isLocked)
                <button type="button" id="btn-save" onclick="processSave()" class="bg-indigo-600 text-white px-12 py-5 rounded-2xl font-black text-[11px] uppercase tracking-[2px] shadow-xl hover:bg-emerald-600 transition-all flex items-center gap-3">
                    <span>Simpan & Ajukan</span> <i class="fas fa-paper-plane"></i>
                </button>
                @else
                <button type="button" disabled class="bg-slate-300 text-white px-12 py-5 rounded-2xl font-black text-[11px] uppercase tracking-[2px] cursor-not-allowed flex items-center gap-3">
                    <span>Akses Terkunci</span> <i class="fas fa-lock"></i>
                </button>
                @endif
            </div>
        </form>
    </div>

    {{-- FLOATING REVISION CENTER --}}
    <button type="button" onclick="openRevisionCenter()" class="btn-floating-revisi bg-rose-600 text-white px-6 py-5 rounded-full shadow-2xl flex items-center gap-4 border-4 border-white group">
        <div class="relative">
            <i class="fas fa-exclamation-triangle text-xl"></i>
            <span id="revisi-count-badge" class="absolute -top-4 -right-4 bg-white text-rose-600 text-[10px] font-black w-6 h-6 flex items-center justify-center rounded-full shadow-md border-2 border-rose-500">0</span>
        </div>
        <div class="text-left leading-none">
            <span class="block text-[10px] font-black uppercase tracking-widest opacity-70">Butuh</span>
            <span class="block text-xs font-black uppercase tracking-widest">Revisi Data</span>
        </div>
    </button>

    {{-- MODAL PUSAT REVISI --}}
    <div id="modal-revisi" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-[3rem] w-full max-w-2xl overflow-hidden shadow-2xl animate-fade">
            <div class="p-10 border-b border-slate-100 flex justify-between items-center bg-rose-50/50">
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Pusat Revisi Data</h3>
                    <p class="text-rose-500 text-[10px] font-black mt-1 uppercase tracking-[2px]">Klik data di bawah untuk memperbaiki</p>
                </div>
                <button onclick="closeRevisionCenter()" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 hover:text-rose-500 shadow-sm transition-all"><i class="fas fa-times"></i></button>
            </div>
            <div id="list-revisi" class="p-6 max-h-[450px] overflow-y-auto custom-scrollbar space-y-4 bg-slate-50/30"></div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // ... (Semua script JavaScript Anda yang sebelumnya tetap sama) ...
    // ... Pastikan fungsi jumpToStep, fillForm, dan processSave tetap ada ...
    
    let currentStep = 1;
    const config = {
        1: { title: "Tujuan PD", parent: "Misi Gubernur" },
        2: { title: "Sasaran Strategis", parent: "Tujuan PD" },
        3: { title: "Program", parent: "Sasaran Strategis" },
        4: { title: "Kegiatan", parent: "Program" },
        5: { title: "Sub-Kegiatan", parent: "Kegiatan" }
    };

    function jumpToStep(step) {
        currentStep = step;
        $('.step').removeClass('active completed');
        for(let i=1; i<step; i++) $(`#step-nav-${i}`).addClass('completed');
        $(`#step-nav-${step}`).addClass('active');

        $('#step-title').text(config[step].title);
        $('#badge-step').text(`Langkah ${step} / 5`);
        $('#label-parent').text(`Pilih ${config[step].parent} (Induk)`);

        if(step === 5) $('#box-extra').removeClass('hidden'); else $('#box-extra').addClass('hidden');
        resetFormUI();

        $.get("{{ url('kinerja/wizard/fetch-parents') }}/" + step, function(data) {
            let html = '<option value="">-- Pilih Induk --</option>';
            data.forEach(item => { html += `<option value="${item.id}">${item.text}</option>`; });
            $('#parent_id').html(html);
        });
    }

    function loadExisting(parentId) {
        if(!parentId) return;
        $.get("{{ url('kinerja/wizard/fetch-existing') }}/" + currentStep + "/" + parentId, function(data) {
            let html = '<option value="">-- Input Baru --</option>';
            data.forEach(item => { html += `<option value="${item.id}">${item.text}</option>`; });
            $('#existing_select').html(html);
        });
    }

    function fillForm(id) {
        if(!id) { resetFormUI(); return; }
        $.get("{{ url('kinerja/wizard/fetch-detail') }}/" + currentStep + "/" + id, function(data) {
            $('#existing_id').val(data.id);
            let namaVal = data.nama_tujuan || data.nama_sasaran || data.nama_program || data.nama_kegiatan || data.nama_sub;
            let indVal = data.indikator || data.indikator_sasaran || data.indikator_program || data.indikator_kegiatan || data.indikator_sub;
            $('#nama').val(namaVal);
            $('#indikator').val(indVal);
            $('#satuan').val(data.satuan);
            $('#target_value').val(data['target_' + $('#tahun_input').val()] || '');
            updateStatusUI(data.status, data.catatan_revisi);
        });
    }

    function updateStatusUI(status, catatan) {
        const badge = $('#badge-status');
        const alertBox = $('#alert-revisi');
        badge.removeClass().addClass('status-badge');
        alertBox.addClass('hidden');
        let s = status ? status.toLowerCase() : 'draft';

        if(s === 'rejected') {
            badge.addClass('bg-rose-100 text-rose-600').text('Perlu Revisi');
            alertBox.removeClass('hidden');
            $('#catatan-revisi').text(catatan);
        } else if(s === 'pending') {
            badge.addClass('bg-amber-100 text-amber-600').text('Menunggu Verifikasi');
        } else if(s === 'approved') {
            badge.addClass('bg-emerald-100 text-emerald-600').text('Disetujui');
        }
    }

    function resetFormUI() {
        $('#existing_id, #nama, #indikator, #satuan, #target_value').val('');
        $('#badge-status').removeClass().addClass('status-badge bg-slate-100 text-slate-500').text('Draft / Baru');
        $('#alert-revisi').addClass('hidden');
    }

    
function processSave() {
        // 1. Ambil SEMUA data form (Gabungan dari kode lama)
        const data = {
            step: currentStep,
            existing_id: $('#existing_id').val(),
            tahun_input: $('#tahun_input').val(),
            parent_id: $('#parent_id').val(),
            nama: $('#nama').val(),
            indikator: $('#indikator').val(),
            satuan: $('#satuan').val(),
            target_value: $('#target_value').val(),
            tipe_perhitungan: $('#tipe_perhitungan').val(),
            klasifikasi: $('#klasifikasi').val(),
            _token: "{{ csrf_token() }}"
        };

        // 2. Kirim dengan penanganan Error 403 (Kode Baru)
        $.post("{{ route('kinerja.wizard.store') }}", data)
            .done(function(res) {
                if(res.success) {
                    alert('Data berhasil diajukan!');
                    location.reload();
                }
            })
            .fail(function(xhr) {
                // INI BAGIAN PENTINGNYA
                // Menangkap pesan error dari Middleware CheckInputAkses
                if (xhr.status === 403) {
                    // Tampilkan pesan spesifik (misal: "Akses Sub-Kegiatan ditutup")
                    alert('GAGAL MENYIMPAN: \n' + xhr.responseJSON.message);
                } else {
                    alert('Terjadi kesalahan sistem. Silakan coba lagi.');
                    console.error(xhr.responseText);
                }
            });
    }

    function openRevisionCenter() {
        $.get("{{ route('kinerja.wizard.rejected') }}", function(data) {
            let html = '';
            data.forEach(item => {
                html += `<div onclick="teleportToRevision(${item.step}, ${item.parent_id}, ${item.id})" class="p-6 bg-white border-2 border-slate-100 rounded-[2rem] hover:border-rose-300 cursor-pointer transition-all">
                    <span class="px-3 py-1 bg-rose-100 text-rose-600 rounded-lg text-[9px] font-black uppercase">${item.level_name}</span>
                    <h4 class="text-sm font-black mt-2">${item.nama}</h4>
                    <p class="text-[11px] text-rose-800 font-bold mt-2">"${item.catatan}"</p>
                </div>`;
            });
            $('#list-revisi').html(html || '<p class="text-center py-10">Tidak ada revisi</p>');
            $('#modal-revisi').removeClass('hidden').addClass('flex');
        });
    }

    function teleportToRevision(step, parentId, existingId) {
        $('#modal-revisi').addClass('hidden').removeClass('flex');
        jumpToStep(step);
        setTimeout(() => {
            $('#parent_id').val(parentId).trigger('change');
            setTimeout(() => { $('#existing_select').val(existingId).trigger('change'); }, 1000);
        }, 800);
    }

    function closeRevisionCenter() { $('#modal-revisi').addClass('hidden').removeClass('flex'); }

    $(document).ready(() => { 
        jumpToStep(1); 
        $.get("{{ route('kinerja.wizard.rejected') }}", d => $('#revisi-count-badge').text(d.length));
    });
</script>
@endpush