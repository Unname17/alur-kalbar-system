@extends('kinerja.pohon.index')

@section('title', 'Wizard Cascading')
@section('page_title', 'Penyusunan & Pembaruan Cascading')

@push('css')
<style>
    .arrow-steps .step { font-size: 11px; font-weight: 800; color: #94a3b8; cursor: pointer; padding: 15px 10px 15px 35px; float: left; position: relative; background-color: #f1f5f9; user-select: none; transition: all 0.2s ease; flex: 1; text-align: center; text-transform: uppercase; letter-spacing: 1px; }
    .arrow-steps .step:after, .arrow-steps .step:before { content: " "; position: absolute; top: 0; right: -17px; width: 0; height: 0; border-top: 25px solid transparent; border-bottom: 25px solid transparent; border-left: 17px solid #f1f5f9; z-index: 2; transition: border-color 0.2s ease; }
    .arrow-steps .step:before { right: auto; left: 0; border-left: 17px solid #fff; z-index: 0; }
    .arrow-steps .step:first-child:before { border: none; }
    .arrow-steps .step:first-child { border-top-left-radius: 1rem; border-bottom-left-radius: 1rem; padding-left: 20px; }
    .arrow-steps .step:last-child { border-top-right-radius: 1rem; border-bottom-right-radius: 1rem; }
    .arrow-steps .step:last-child:after { border: none; }
    .arrow-steps .step.active { color: #fff; background-color: #1e293b; }
    .arrow-steps .step.active:after { border-left-color: #1e293b; }
    .arrow-steps .step.completed { color: #fff; background-color: #10b981; }
    .arrow-steps .step.completed:after { border-left-color: #10b981; }
    .arrow-steps .step:hover { background-color: #e2e8f0; }
    .nav-tip { display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px; background: #fdf2f2; border: 1px solid #fee2e2; border-radius: 99px; color: #b91c1c; font-size: 10px; font-weight: 800; margin-top: 15px; animation: pulse-soft 2s infinite; }
    @keyframes pulse-soft { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.02); } }
    .animate-fade { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .status-badge { font-size: 9px; font-weight: 900; text-transform: uppercase; padding: 4px 12px; border-radius: 8px; letter-spacing: 1px; }
    .btn-floating-revisi { position: fixed; bottom: 40px; right: 40px; z-index: 50; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .btn-floating-revisi:hover { transform: scale(1.1); }
    .form-locked { pointer-events: none; opacity: 0.6; user-select: none; }
</style>
@endpush

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6 relative">
    
    {{-- Notifikasi Akses --}}
    @if($isLocked)
    <div class="col-span-12 animate-fade">
        <div class="bg-rose-50 border-2 border-rose-100 p-8 rounded-[2.5rem] flex items-start gap-6 shadow-sm">
            <div class="w-16 h-16 bg-rose-500 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-rose-200">
                <i class="fas fa-lock text-white text-2xl"></i>
            </div>
            <div>
                <h4 class="text-xl font-black text-rose-900 tracking-tight uppercase">Akses Input Ditutup</h4>
                <p class="text-rose-700 font-bold mt-1 leading-relaxed">{{ $access->pesan_blokir ?? 'Maaf, batas waktu penginputan cascading telah berakhir.' }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Stepper Navigation --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
        <div class="arrow-steps flex overflow-hidden">
            @foreach(['Tujuan PD', 'Sasaran Strategis', 'Program', 'Kegiatan', 'Sub-Kegiatan'] as $i => $label)
                <div class="step {{ $i == 0 ? 'active' : '' }}" id="step-nav-{{ $i+1 }}" onclick="jumpToStep({{ $i+1 }})">
                    <span>{{ $i+1 }}. {{ $label }}</span>
                </div>
            @endforeach
        </div>
        <div class="nav-tip"><i class="fas fa-lightbulb"></i><span>Tip: Klik panah untuk pindah level. Data Sub-Kegiatan adalah kunci perhitungan SPK.</span></div>
    </div>

    {{-- Main Wizard Area --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden animate-fade">
        <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div>
                <h2 id="step-title" class="text-2xl font-black text-slate-800 tracking-tight">Tentukan Tujuan PD</h2>
                <div id="status-display" class="mt-2 flex items-center gap-2">
                    <span id="badge-status" class="status-badge bg-slate-100 text-slate-500">Draft / Baru</span>
                </div>
            </div>
            <div id="badge-step" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest">Langkah 1 / 5</div>
        </div>

        {{-- FORM INPUT --}}
        <form id="wizardForm" onsubmit="submitWizard(event)" class="p-10 space-y-8 transition-all duration-500 {{ $isLocked ? 'form-locked' : '' }}">
            @csrf
            {{-- FIX: Pastikan atribut name="..." ada --}}
            <input type="hidden" name="step" id="input_step" value="2">
            <input type="hidden" name="id" id="input_id"> 

            <div id="alert-revisi" class="hidden col-span-12 bg-rose-50 p-6 rounded-2xl border border-rose-100 flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center shrink-0"><i class="fas fa-undo-alt text-white"></i></div>
                <div><span class="text-[10px] font-black text-rose-500 uppercase tracking-widest block mb-1">Catatan Revisi:</span><p id="catatan-revisi" class="text-sm font-bold text-rose-900 leading-relaxed"></p></div>
            </div>
            
            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-12 bg-indigo-50 p-5 rounded-2xl border border-indigo-100 flex items-center justify-between">
                    <div class="flex items-center gap-3"><i class="fas fa-calendar-check text-indigo-600"></i><span class="text-xs font-black text-indigo-900 uppercase">Tahun Perencanaan:</span></div>
                    {{-- Input Tahun (Opsional, tidak dikirim ke DB tapi untuk logika UI) --}}
                    <select id="tahun_input" class="bg-white border-none rounded-xl text-sm font-black px-6 py-2 shadow-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(range(2025, 2030) as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Parent Selection --}}
                <div class="col-span-6">
                    <label id="label-parent" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Pilih Induk Data</label>
                    {{-- FIX: Tambahkan name="parent_id" --}}
                    <select name="parent_id" id="parent_id" onchange="loadExisting(this.value)" class="w-full bg-slate-100/50 border-slate-200 rounded-2xl p-4 text-sm font-bold focus:ring-4 focus:ring-indigo-100 outline-none"><option value="">-- Pilih Induk --</option></select>
                </div>
                
                {{-- Existing Data Selection --}}
                <div class="col-span-6">
                    <label class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block mb-3"><i class="fas fa-edit me-1"></i> Update Data Existing?</label>
                    <select id="existing_select" onchange="fillForm(this.value)" class="w-full bg-indigo-50 border-indigo-100 rounded-2xl p-4 text-sm font-bold focus:ring-4 focus:ring-indigo-100 outline-none"><option value="">-- Input Baru --</option></select>
                </div>

                {{-- Nama Uraian --}}
                <div class="col-span-12">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Nama Uraian</label>
                    {{-- FIX: Tambahkan name="nama" --}}
                    <textarea name="nama" id="nama" rows="3" class="w-full bg-white border-2 border-slate-100 rounded-2xl p-5 text-sm font-bold focus:border-indigo-500 outline-none"></textarea>
                </div>

                {{-- Indikator --}}
                <div class="col-span-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Indikator Kinerja</label>
                    {{-- FIX: Tambahkan name="indikator" --}}
                    <input type="text" name="indikator" id="indikator" class="w-full bg-white border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-indigo-500 outline-none">
                </div>
                
                {{-- Satuan --}}
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Satuan</label>
                    {{-- FIX: Tambahkan name="satuan" --}}
                    <input type="text" name="satuan" id="satuan" class="w-full bg-white border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-indigo-500 outline-none">
                </div>
                
                {{-- GRID TARGET & BASELINE --}}
                <div class="col-span-12 bg-slate-50 p-5 rounded-2xl border border-slate-200 mt-4">
                    <h5 class="text-xs font-black text-slate-700 uppercase mb-4 flex items-center gap-2">
                        <i class="fas fa-bullseye text-emerald-500"></i> Target Kinerja ({{ $startYear }} - {{ $startYear + 4 }})
                    </h5>

                    <div class="grid grid-cols-12 gap-3 items-end">
                        {{-- Baseline --}}
                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1 text-center">Baseline ({{ $startYear - 1 }})</label>
                            {{-- FIX: Tambahkan name="baseline" --}}
                            <input type="number" step="any" name="baseline" id="baseline" class="w-full text-center py-2 text-sm font-bold text-slate-700 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-white" placeholder="0">
                        </div>

                        <div class="hidden md:flex col-span-1 justify-center pb-2 text-slate-300">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </div>

                        {{-- Target 5 Tahun --}}
                        <div class="col-span-12 md:col-span-8 grid grid-cols-5 gap-2">
                            @foreach($years as $index => $year)
                                <div class="relative group">
                                    <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1 text-center group-hover:text-indigo-500 transition-colors">{{ $year }}</label>
                                    {{-- FIX: Name attribute sudah benar, tapi ID perlu unik untuk JS fillForm --}}
                                    <input type="number" step="any" name="target_values[{{ $year }}]" id="input_target_{{ $year }}" class="w-full text-center py-2 text-sm font-bold text-indigo-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all {{ $index == 4 ? 'border-emerald-300 bg-emerald-50/20' : '' }}" placeholder="-">
                                    @if($index == 4) <div class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full" title="Target Akhir"></div> @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- EXTRA INPUT (SUB KEGIATAN) --}}
                <div id="box-extra" class="hidden col-span-12 grid grid-cols-2 gap-8 pt-8 border-t border-slate-100">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Tipe Perhitungan</label>
                        {{-- FIX: Tambahkan name="tipe_perhitungan" --}}
                        <select name="tipe_perhitungan" id="tipe_perhitungan" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 text-sm font-bold">
                            <option value="Akumulasi">Akumulasi (Dijumlahkan)</option>
                            <option value="Non-Akumulasi">Non-Akumulasi (Target Akhir)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-indigo-600 uppercase tracking-widest block mb-3">Klasifikasi</label>
                        {{-- FIX: Tambahkan name="klasifikasi" --}}
                        <select name="klasifikasi" id="klasifikasi" class="w-full bg-indigo-50 border-indigo-100 text-indigo-900 rounded-2xl p-4 text-sm font-bold">
                            <option value="IKK">IKK (Indeks Kinerja Kunci)</option>
                            <option value="IKU">IKU (Indeks Kinerja Utama)</option>
                            <option value="IKD">IKD (Indeks Kinerja Daerah)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-10 flex justify-between items-center border-t border-slate-50">
                <button type="button" onclick="location.reload()" class="px-8 py-4 text-slate-400 font-bold text-xs uppercase hover:text-rose-500">Reset</button>
                @if(!$isLocked)
                {{-- Tombol Submit Tipe Submit --}}
                <button type="submit" id="btn-save" class="bg-indigo-600 text-white px-12 py-5 rounded-2xl font-black text-[11px] uppercase tracking-[2px] shadow-xl hover:bg-emerald-600 transition-all flex items-center gap-3">
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

    {{-- Tombol Floating Revisi --}}
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
    
    {{-- MODAL REVISI --}}
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
<script src="https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js"></script>
<script>
    let currentStep = 2; // Default start step
    const startYear = {{ $startYear }};
    const years = {!! json_encode($years) !!}; 

    const config = {
        1: { title: "Tujuan PD", parent: "Misi Gubernur" }, // Fallback logic
        2: { title: "Tujuan PD", parent: "Misi Gubernur" },
        3: { title: "Sasaran Strategis", parent: "Tujuan PD" },
        4: { title: "Program", parent: "Sasaran Strategis" },
        5: { title: "Kegiatan", parent: "Program" },
        6: { title: "Sub-Kegiatan", parent: "Kegiatan" }
    };

    // FUNGSI NAVIGASI STEP (DIPERBARUI: Menerima Auto-Fill Data)
    function jumpToStep(step, autoParentId = null, autoExistingId = null) {
        currentStep = step;
        
        // 1. Update UI Step
        $('.step').removeClass('active completed');
        for(let i=2; i<step; i++) { $(`#step-nav-${i}`).addClass('completed'); }
        $(`#step-nav-${step}`).addClass('active');

        // 2. Update Judul & Label
        $('#step-title').text(config[step].title);
        $('#badge-step').text(`Langkah ${step-1} / 5`); // asumsi start step 2
        $('#label-parent').text(`Pilih ${config[step].parent} (Induk)`);
        $('#input_step').val(step);

        // 3. Tampilkan/Sembunyikan Input Tambahan
        if(step === 6) $('#box-extra').removeClass('hidden'); else $('#box-extra').addClass('hidden');
        
        // 4. Reset Form dulu
        resetFormUI();

        // 5. Tampilkan Parent Dropdown (SELALU MUNCUL untuk Step 2-6)
        $('#parent-container').removeClass('hidden');
        
        // 6. Load Data Parent (Chain Reaction dimulai disini)
        loadParents(step, autoParentId, autoExistingId);
        
        refreshTree();
    }

    // LOAD PARENTS (DENGAN CALLBACK AUTO-SELECT)
    function loadParents(step, autoParentId = null, autoExistingId = null) {
        $('#parent_id').html('<option>Loading...</option>');
        
        $.get("{{ url('kinerja/wizard/fetch-parents') }}/" + step, function(data) {
            let html = '<option value="">-- Pilih Induk --</option>';
            data.forEach(item => { html += `<option value="${item.id}">${item.text}</option>`; });
            $('#parent_id').html(html);

            // LOGIKA AUTO-FILL REVISI: Jika ada ID Parent, langsung pilih & load anak
            if (autoParentId) {
                $('#parent_id').val(autoParentId);
                loadExisting(autoParentId, autoExistingId); // Lanjut ke rantai berikutnya
            }
        });
    }

    // LOAD EXISTING DATA (DENGAN CALLBACK AUTO-SELECT)
    function loadExisting(parentId, autoExistingId = null) {
        if(!parentId) { 
            $('#existing_select').html('<option value="">-- Input Baru --</option>');
            return; 
        }

        $('#existing_select').html('<option>Loading...</option>');

        $.get("{{ url('kinerja/wizard/fetch-existing') }}/" + currentStep + "/" + parentId, function(data) {
            let html = '<option value="">-- Input Baru --</option>';
            data.forEach(item => { html += `<option value="${item.id}">${item.text}</option>`; });
            $('#existing_select').html(html);

            // LOGIKA AUTO-FILL REVISI: Jika ada ID Existing, langsung pilih & isi form
            if (autoExistingId) {
                $('#existing_select').val(autoExistingId);
                fillForm(autoExistingId); // Eksekusi pengisian form
            }
        });
    }

    function fillForm(id) {
        if(!id) { resetFormUI(); return; }
        
        // Tampilkan loading indicator visual (opsional)
        $('#nama').attr('placeholder', 'Mengambil data...');

        $.get("{{ url('kinerja/wizard/fetch-detail') }}/" + currentStep + "/" + id, function(data) {
            $('#input_id').val(data.id);
            $('#nama').val(data.nama);
            $('#indikator').val(data.indikator);
            $('#satuan').val(data.satuan);
            
            // Populate Baseline & Target 5 Tahun
            $('#baseline').val(data.baseline || 0);
            
            // Pastikan ID element input target sesuai dengan tahun yang di-generate blade
            $('#input_target_' + startYear).val(data.tahun_1 || '');
            $('#input_target_' + (startYear+1)).val(data.tahun_2 || '');
            $('#input_target_' + (startYear+2)).val(data.tahun_3 || '');
            $('#input_target_' + (startYear+3)).val(data.tahun_4 || '');
            $('#input_target_' + (startYear+4)).val(data.tahun_5 || '');

            if(currentStep === 6) { // Sub Kegiatan
                $('#tipe_perhitungan').val(data.tipe_perhitungan || 'Non-Akumulasi');
                $('#klasifikasi').val(data.klasifikasi || 'IKK');
            }
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
        } else {
            badge.addClass('bg-slate-100 text-slate-500').text('Draft / Baru');
        }
    }

    function resetFormUI() {
        $('#input_id, #nama, #indikator, #satuan, #baseline').val('');
        $('[id^=input_target_]').val('');
        $('#badge-status').removeClass().addClass('status-badge bg-slate-100 text-slate-500').text('Draft / Baru');
        $('#alert-revisi').addClass('hidden');
        $('#nama').attr('placeholder', 'Contoh: Meningkatnya kualitas layanan...');
    }

    function submitWizard(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        $('#btn-save').addClass('opacity-50 pointer-events-none').find('span').text('Menyimpan...');

        $.ajax({
            url: "{{ route('kinerja.wizard.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                alert('Data berhasil disimpan!');
                resetFormUI(); // Reset form setelah simpan
                
                // Refresh data existing di dropdown agar data baru muncul/terupdate
                loadExisting($('#parent_id').val());
                
                refreshTree();
                checkRevisions(); // Cek lagi jumlah revisi
            },
            error: function(xhr) {
                alert('Gagal menyimpan: ' + (xhr.responseJSON?.message || 'Error validasi'));
            },
            complete: function() {
                $('#btn-save').removeClass('opacity-50 pointer-events-none').find('span').text('Simpan & Ajukan');
            }
        });
    }

    // --- REVISI SYSTEM ---
    function openRevisionCenter() {
        checkRevisions(true); // true = force show modal
    }

    function checkRevisions(showModal = false) {
        $.get("{{ route('kinerja.wizard.rejected') }}", function(data) {
            $('#revisi-count-badge').text(data.length);
            
            if (showModal) {
                let html = '';
                if(data.length > 0) {
                    data.forEach(item => {
                        // Pass ID parent & ID item ke teleport
                        html += `<div onclick="teleportToRevision(${item.step}, ${item.parent_id}, ${item.id})" class="p-6 bg-white border-2 border-slate-100 rounded-[2rem] hover:border-rose-300 cursor-pointer transition-all group mb-3 shadow-sm hover:shadow-md">
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-3 py-1 bg-rose-100 text-rose-600 rounded-lg text-[9px] font-black uppercase tracking-wider">${item.level_name}</span>
                                <i class="fas fa-arrow-right text-slate-300 group-hover:text-rose-500 transition-colors"></i>
                            </div>
                            <h4 class="text-sm font-black text-slate-800 leading-snug">${item.nama}</h4>
                            <div class="mt-3 bg-rose-50 p-3 rounded-xl border border-rose-100">
                                <p class="text-[10px] font-bold text-rose-800 italic">Catatan: "${item.catatan}"</p>
                            </div>
                        </div>`;
                    });
                } else {
                    html = '<div class="text-center py-10"><div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-check text-emerald-500 text-xl"></i></div><p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Tidak ada revisi</p></div>';
                }
                $('#list-revisi').html(html);
                $('#modal-revisi').removeClass('hidden').addClass('flex');
            }
        });
    }

    function teleportToRevision(step, parentId, existingId) {
        $('#modal-revisi').addClass('hidden').removeClass('flex');
        // PANGGIL JUMP DENGAN PARAMETER AUTO-FILL
        jumpToStep(step, parentId, existingId);
    }

    function closeRevisionCenter() { $('#modal-revisi').addClass('hidden').removeClass('flex'); }

    // Dummy refresh tree
    function refreshTree() { console.log('Tree Refreshed'); }

    $(document).ready(() => { 
        jumpToStep(1); // Start at Step 2
        checkRevisions(false); // Cek badge count saja
    });
</script>
@endpush