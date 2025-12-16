<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $viewTitle ?? 'Pohon Kinerja' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fc; overflow-x: hidden; }
        
        /* LAYOUT */
        #wrapper { display: flex; width: 100%; min-height: 100vh; }
        #sidebar { width: 250px; background-color: #2c3e50; color: white; flex-shrink: 0; min-height: 100vh; }
        #sidebar .sidebar-brand { padding: 20px; font-size: 1.2rem; font-weight: bold; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        #sidebar ul { list-style: none; padding: 0; margin-top: 20px; }
        #sidebar ul li a { display: block; padding: 15px 20px; color: rgba(255,255,255,0.8); text-decoration: none; transition: 0.3s; cursor: pointer; }
        #sidebar ul li a:hover, #sidebar ul li a.active { background-color: #34495e; color: white; border-left: 4px solid #3498db; }
        
        #content-wrapper { flex-grow: 1; display: flex; flex-direction: column; }
        #topbar { height: 60px; background: white; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }
        #main-content { padding: 20px; }

        /* VISUALISASI D3 */
        .viewport { width: 100%; height: 700px; overflow: hidden; background: #fff; border: 1px solid #e3e6f0; border-radius: 8px; position: relative; cursor: grab; background-image: radial-gradient(#e1e1e1 1px, transparent 1px); background-size: 20px 20px; }
        .node rect { stroke: #999; stroke-width: 1.5px; cursor: pointer; fill: #fff; rx: 6; ry: 6; transition: all 0.2s; }
        .node:hover rect { stroke-width: 2.5px; filter: drop-shadow(0 0 5px rgba(0,0,0,0.1)); }
        .node-text-div { font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; text-align: center; height: 100%; padding: 5px; line-height: 1.2; color: #333; pointer-events: none; }
        .link { fill: none; stroke: #ccc; stroke-width: 1.5px; }
        
        /* WARNA STATUS */
        .status-pengajuan rect { stroke: #f6c23e !important; stroke-dasharray: 5,2; fill: #fffcf5; }
        .status-ditolak rect { stroke: #e74a3b !important; fill: #fff5f5; }
        .status-disetujui rect { stroke: #1cc88a !important; }

        /* STYLE LIST VIEW */
        .tree-item { cursor: pointer; transition: 0.2s; border-left: 3px solid transparent; }
        .tree-item:hover { background-color: #f1f3f9; border-left: 3px solid #4e73df; }
        .level-0 { border-left: 4px solid #4e73df !important; background-color: #f8f9fc; }
        .level-1 { margin-left: 20px; border-left: 3px solid #1cc88a; }
        .level-2 { margin-left: 40px; border-left: 3px solid #36b9cc; }
        .level-3 { margin-left: 60px; border-left: 3px solid #f6c23e; }
        .level-4 { margin-left: 80px; border-left: 3px solid #e74a3b; }

        /* MODAL & TABLE INDIKATOR */
        .modal { z-index: 1060 !important; }
        .modal-backdrop { z-index: 1050 !important; }
        .table-indikator thead th { background-color: #f8f9fc; font-size: 0.85rem; }
    </style>
</head>
<body>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-chart-line me-2"></i> E-KINERJA</div>
        <ul>
            <li><a href="#"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            
            <li>
                <a onclick="switchView('visual')" id="menuPohon" class="active">
                    <i class="fas fa-sitemap me-2"></i> Pohon Kinerja
                </a>
            </li>

            @if(Auth::user()->peran == 'sekretariat' || Auth::user()->peran == 'admin_utama')
                <li>
                    <a onclick="switchView('inbox')" id="menuInbox">
                        <i class="fas fa-clipboard-check me-2"></i> Verifikasi Pengajuan
                        <span id="badgePengajuan" class="badge bg-warning text-dark float-end" style="display:none">0</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kinerja.akses.index') }}">
                        <i class="fas fa-user-lock me-2"></i> Akses Tambah Kinerja
                    </a>
                </li>
            @else
                <li>
                    <a onclick="switchView('inbox')" id="menuInbox">
                        <i class="fas fa-tools me-2"></i> Perbaikan Data
                        <span id="badgePerbaikan" class="badge bg-danger float-end" style="display:none">0</span>
                    </a>
                </li>
            @endif
        </ul>
        <div class="p-3 mt-auto text-center">
            <small class="text-white-50">Login: {{ Auth::user()->nama_lengkap ?? 'User' }}</small>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">@csrf<button class="btn btn-sm btn-danger w-100">Logout</button></form>
        </div>
    </nav>

    <div id="content-wrapper">
        <nav id="topbar">
            <h5 class="m-0 text-dark fw-bold" id="pageTitle">{{ $viewTitle ?? 'Pohon Kinerja' }}</h5>
            <div class="text-secondary small">{{ now()->format('l, d F Y') }}</div>
        </nav>

        <div id="main-content">
            
            <div id="actionHeader" class="card shadow-sm mb-3">
                <div class="card-body py-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="text-muted small"><i class="fas fa-info-circle"></i> Klik node untuk detail/edit.</span>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm me-2" onclick="bukaModalTambah()">
                            <i class="fas fa-plus-circle"></i> Ajukan Baru
                        </button>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-primary active" id="btnVisual" onclick="switchMode('visual')">Visual</button>
                            <button class="btn btn-outline-primary" id="btnList" onclick="switchMode('list')">List</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="viewVisual">
                <div class="viewport shadow-sm" id="visualContainer"></div>
            </div>
            
            <div id="viewList" style="display:none">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div id="listContainer" class="list-group list-group-flush"></div>
                    </div>
                </div>
            </div>

            <div id="viewInbox" style="display:none">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 font-weight-bold text-primary" id="inboxHeaderTitle">Daftar Tugas</h6>
                    </div>
                    <div class="card-body p-0">
                        <div id="inboxContainer" class="list-group list-group-flush">
                            </div>
                    </div>
                </div>
            </div>

        </div> 
    </div> 
</div> 

<div class="modal fade" id="modalForm" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary" id="modalTitle">Form Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <div id="alertTolak" class="alert alert-danger d-none">
                    <strong><i class="fas fa-info-circle"></i> Status: Ditolak</strong>
                    <p class="mb-0 mt-1 small" id="pesanTolak"></p>
                </div>

                <form id="formKinerja">
                    <input type="hidden" id="nodeId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Induk Kinerja <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="parent_id" id="parentSelect" required>
                                <option value="">-- Pilih Induk --</option>
                                @if(isset($parents))
                                    @foreach($parents as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_kinerja }} ({{ ucfirst($p->jenis_kinerja) }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Jenis Kinerja <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="jenis_kinerja" id="inputJenis" onchange="toggleFields()">
                                <option value="program">Program</option>
                                <option value="kegiatan">Kegiatan</option>
                                <option value="sub_kegiatan">Sub Kegiatan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Kinerja (Nomenklatur) <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="nama_kinerja" id="inputNama" rows="2" placeholder="Contoh: Penyelenggaraan Pelayanan Publik..." required></textarea>
                    </div>

                    <div class="row d-none bg-light p-2 rounded mb-3 border" id="rowSubKegiatan">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Anggaran (Rp)</label>
                            <input type="number" name="anggaran" id="inputAnggaran" class="form-control form-control-sm" placeholder="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Penanggung Jawab</label>
                            <input type="text" name="penanggung_jawab" id="inputPJ" class="form-control form-control-sm" placeholder="Bidang...">
                        </div>
                    </div>

                    <div class="border rounded p-2 mb-3">
                        <label class="form-label fw-bold small text-primary"><i class="fas fa-bullseye"></i> Indikator Kinerja</label>
                        <table class="table table-bordered table-sm table-indikator mb-0">
                            <thead class="text-center">
                                <tr>
                                    <th width="50%">Indikator</th>
                                    <th width="20%">Target</th>
                                    <th width="20%">Satuan</th>
                                    <th width="10%">
                                        <button type="button" class="btn btn-xs btn-success" onclick="addIndikatorRow()">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="bodyIndikator">
                                </tbody>
                        </table>
                        <div class="form-text small fst-italic">* Klik (+) untuk menambah indikator baru</div>
                    </div>

                    <div id="areaApproval" class="d-none border-top pt-3 bg-warning-subtle p-3 rounded">
                        <h6 class="fw-bold text-dark mb-2">Keputusan Verifikasi</h6>
                        <textarea class="form-control mb-2" id="catatanKabid" placeholder="Tulis alasan penolakan jika ditolak..."></textarea>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="submitApproval('setuju')" class="btn btn-success flex-fill">Setujui</button>
                            <button type="button" onclick="submitApproval('tolak')" class="btn btn-danger flex-fill">Tolak</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light" id="footerSubmit">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary px-4" onclick="simpanData()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js"></script>

<script>
    const rawData = {!! json_encode($pohons ?? []) !!};
    const userId = {{ Auth::id() }};
    const userRole = "{{ Auth::user()->peran ?? 'staf' }}"; 

    document.addEventListener("DOMContentLoaded", () => { 
        if (!rawData || rawData.length === 0) {
            document.getElementById('visualContainer').innerHTML = '<div class="alert alert-warning m-5 text-center">Data Pohon Kinerja Kosong.<br>Silakan tambahkan data baru.</div>';
        } else {
            renderD3Tree(rawData[0]); 
            renderListView(); 
        }
        hitungBadgeInbox();
    });

    // --- FUNGSI DINAMIS INDIKATOR ---
    function addIndikatorRow(data = null) {
        let valInd = data ? data.indikator : '';
        let valTrg = data ? data.target : '';
        let valSat = data ? data.satuan : '';
        
        let html = `
            <tr>
                <td><textarea name="indikator[]" class="form-control form-control-sm" rows="1" placeholder="Uraian..." required>${valInd}</textarea></td>
                <td><input type="text" name="target[]" class="form-control form-control-sm" value="${valTrg}" placeholder="Angka"></td>
                <td><input type="text" name="satuan[]" class="form-control form-control-sm" value="${valSat}" placeholder="Satuan"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
        $('#bodyIndikator').append(html);
    }

    function removeRow(btn) {
        $(btn).closest('tr').remove();
    }

    function toggleFields() {
        let jenis = $('#inputJenis').val();
        if(jenis === 'sub_kegiatan') {
            $('#rowSubKegiatan').removeClass('d-none');
        } else {
            $('#rowSubKegiatan').addClass('d-none');
            $('#inputAnggaran').val('');
            $('#inputPJ').val('');
        }
    }

    // --- FORM LOGIC ---
    function klikNode(data) {
        $('#formKinerja')[0].reset();
        $('#nodeId').val(data.id);
        
        // Isi Data Utama
        $('#inputNama').val(data.nama_kinerja);
        $('#parentSelect').val(data.parent_id);
        $('#inputJenis').val(data.jenis_kinerja);
        $('#inputAnggaran').val(data.anggaran || '');
        $('#inputPJ').val(data.penanggung_jawab || '');

        toggleFields(); // Cek apakah perlu nampilin field anggaran

        // Isi Indikator (Looping)
        $('#bodyIndikator').empty();
        if (data.indikators && data.indikators.length > 0) {
            data.indikators.forEach(item => addIndikatorRow(item));
        } else {
            addIndikatorRow(); // Baris kosong default
        }

        // Logic Readonly/Approval (Sama seperti sebelumnya)
        const isCreator = (data.created_by == userId);
        const isPending = (data.status === 'pengajuan');
        const isRejected = (data.status === 'ditolak');

        // Default: Readonly semua, kecuali kondisi tertentu
        let readOnly = true;

        $('#alertTolak').addClass('d-none');
        $('#areaApproval').addClass('d-none');
        $('#footerSubmit').addClass('d-none');

        if (isRejected && isCreator) {
            // REVISI (Boleh Edit)
            readOnly = false;
            $('#modalTitle').text('Perbaikan Data');
            $('#alertTolak').removeClass('d-none');
            $('#pesanTolak').text(data.catatan_penolakan || '-');
            $('#footerSubmit').removeClass('d-none');
        } else if (isPending && (userRole === 'kepala_bidang' || userRole === 'admin_utama' || userRole === 'sekretariat')) {
            // APPROVAL (Cuma boleh approve/reject)
            $('#modalTitle').text('Verifikasi Pengajuan');
            $('#areaApproval').removeClass('d-none');
        } else if (!data.id) {
            // BARU (Boleh Edit)
            readOnly = false;
        }

        // Terapkan Readonly ke Form
        $('#inputNama').prop('readonly', readOnly);
        $('#parentSelect').prop('disabled', readOnly);
        $('#inputJenis').prop('disabled', readOnly);
        $('#inputAnggaran').prop('readonly', readOnly);
        $('#inputPJ').prop('readonly', readOnly);
        
        // Terapkan ke Tabel Indikator
        $('#bodyIndikator textarea, #bodyIndikator input').prop('readonly', readOnly);
        $('#bodyIndikator button').prop('disabled', readOnly);

        $('#modalForm').modal('show');
    }

    function bukaModalTambah() {
        $('#formKinerja')[0].reset();
        $('#nodeId').val('');
        $('#modalTitle').text('Form Pengajuan Baru');
        
        $('#bodyIndikator').empty();
        addIndikatorRow(); // 1 Baris kosong
        
        toggleFields();

        // Unlock Form
        $('#alertTolak').addClass('d-none');
        $('#areaApproval').addClass('d-none');
        $('#footerSubmit').removeClass('d-none');
        $('#parentSelect').prop('disabled', false);
        $('#inputNama').prop('readonly', false);
        $('#inputJenis').prop('disabled', false);
        $('#inputAnggaran').prop('readonly', false);
        $('#inputPJ').prop('readonly', false);
        $('#bodyIndikator button').prop('disabled', false);
        $('#bodyIndikator input').prop('readonly', false);
        $('#bodyIndikator textarea').prop('readonly', false);

        $('#modalForm').modal('show');
    }

    function simpanData() {
        if(!$('#parentSelect').val()) { alert("Induk Kinerja wajib dipilih!"); return; }
        if(!$('#inputNama').val()) { alert("Nama Kinerja wajib diisi!"); return; }
        
        let id = $('#nodeId').val();
        let url = id ? `/kinerja/update/${id}` : `/kinerja/store`;
        
        $.ajax({
            url: url, method: 'POST',
            data: $('#formKinerja').serialize(), // Ini otomatis ambil array indikator[]
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) { alert(res.message || "Berhasil disimpan!"); location.reload(); },
            error: function(xhr) { alert("Gagal: " + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText)); }
        });
    }

    function submitApproval(action) {
        let id = $('#nodeId').val();
        let catatan = $('#catatanKabid').val();
        if (action === 'tolak' && !catatan) { alert("Wajib mengisi alasan penolakan!"); return; }
        $.ajax({
            url: `/kinerja/approval/${id}`, method: 'POST',
            data: { action: action, catatan: catatan, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(res) { alert(res.message); location.reload(); },
            error: function(xhr) { alert("Error: " + xhr.statusText); }
        });
    }

    // --- NAVIGASI & RENDER TREE (SAMA SEPERTI SEBELUMNYA) ---
    function switchView(viewName) {
        const visualDiv = document.getElementById('viewVisual');
        const listDiv = document.getElementById('viewList');
        const inboxDiv = document.getElementById('viewInbox');
        const actionHeader = document.getElementById('actionHeader');

        document.getElementById('menuPohon').classList.remove('active');
        if(document.getElementById('menuInbox')) document.getElementById('menuInbox').classList.remove('active');

        if (viewName === 'inbox') {
            visualDiv.style.display = 'none';
            listDiv.style.display = 'none';
            inboxDiv.style.display = 'block';
            actionHeader.style.display = 'none';
            if(document.getElementById('menuInbox')) document.getElementById('menuInbox').classList.add('active');
            renderInbox();
        } else {
            inboxDiv.style.display = 'none';
            actionHeader.style.display = 'block';
            document.getElementById('menuPohon').classList.add('active');
            switchMode('visual'); 
        }
    }

    function switchMode(mode) {
        document.getElementById('viewList').style.display = mode === 'list' ? 'block' : 'none';
        document.getElementById('viewVisual').style.display = mode === 'visual' ? 'block' : 'none';
        document.getElementById('btnList').className = mode === 'list' ? 'btn btn-primary active' : 'btn btn-outline-primary';
        document.getElementById('btnVisual').className = mode === 'visual' ? 'btn btn-primary active' : 'btn btn-outline-primary';
    }

    function getNodesByStatus(nodes, status, checkOwner = false) {
        let results = [];
        nodes.forEach(node => {
            let match = (node.status === status);
            if (checkOwner) match = match && (node.created_by == userId);
            if (match) results.push(node);
            if (node.children) results = results.concat(getNodesByStatus(node.children, status, checkOwner));
        });
        return results;
    }

    function renderInbox() {
        const container = document.getElementById('inboxContainer');
        container.innerHTML = '';
        let items = [];
        let emptyMsg = '';

        if (userRole === 'sekretariat' || userRole === 'admin_utama') {
            items = getNodesByStatus(rawData, 'pengajuan', false);
            emptyMsg = 'Tidak ada pengajuan yang perlu diverifikasi.';
            document.getElementById('inboxHeaderTitle').innerText = `Daftar Pengajuan Masuk (${items.length})`;
        } else {
            items = getNodesByStatus(rawData, 'ditolak', true);
            emptyMsg = 'Tidak ada data yang perlu diperbaiki.';
            document.getElementById('inboxHeaderTitle').innerText = `Daftar Perbaikan (${items.length})`;
        }

        if (items.length === 0) {
            container.innerHTML = `<div class="p-5 text-center text-muted"><i class="fas fa-check-circle fa-2x mb-3 text-gray-300"></i><br>${emptyMsg}</div>`;
            return;
        }

        items.forEach(node => {
            const dataStr = encodeURIComponent(JSON.stringify(node));
            let badgeInfo = (userRole === 'sekretariat' || userRole === 'admin_utama') 
                ? '<span class="badge bg-warning text-dark">Menunggu</span>' 
                : '<span class="badge bg-danger">Ditolak</span>';
            
            const itemHtml = `
                <a href="#" onclick="pilihInboxItem('${dataStr}')" class="list-group-item list-group-item-action p-3 border-bottom">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 fw-bold text-dark">${node.nama_kinerja}</h6>
                        ${badgeInfo}
                    </div>
                    <small class="text-muted text-uppercase fw-bold">${(node.jenis_kinerja || '').replace('_', ' ')}</small>
                </a>
            `;
            container.innerHTML += itemHtml;
        });
    }

    function pilihInboxItem(encodedData) {
        const data = JSON.parse(decodeURIComponent(encodedData));
        klikNode(data);
    }

    function hitungBadgeInbox() {
        if(userRole === 'sekretariat' || userRole === 'admin_utama') {
            const count = getNodesByStatus(rawData, 'pengajuan', false).length;
            if(count > 0) $('#badgePengajuan').text(count).show();
        } else {
            const count = getNodesByStatus(rawData, 'ditolak', true).length;
            if(count > 0) $('#badgePerbaikan').text(count).show();
        }
    }

    function renderListView() {
        const container = document.getElementById('listContainer');
        let html = '';
        function buildListHTML(nodes, level) {
            let content = '';
            nodes.forEach(node => {
                let badgeClass = node.status === 'disetujui' ? 'bg-success' : (node.status === 'ditolak' ? 'bg-danger' : 'bg-warning text-dark');
                const nodeDataStr = encodeURIComponent(JSON.stringify(node));
                content += `
                    <div class="list-group-item tree-item level-${level}" onclick="pilihInboxItem('${nodeDataStr}')">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div><small class="text-muted fw-bold">${(node.jenis_kinerja || '').toUpperCase()}</small><h6 class="mb-0">${node.nama_kinerja}</h6></div>
                            <span class="badge ${badgeClass} rounded-pill">${node.status}</span>
                        </div>
                    </div>
                `;
                if (node.children && node.children.length > 0) content += buildListHTML(node.children, level + 1);
            });
            return content;
        }
        if (rawData.length > 0) html = buildListHTML(rawData, 0); 
        else html = '<div class="p-3 text-center text-muted">Belum ada data visual.</div>';
        container.innerHTML = html;
    }

    function renderD3Tree(rootData) {
        const container = document.getElementById('visualContainer');
        container.innerHTML = ''; 
        const width = container.clientWidth || 1000;
        const nodeWidth = 220; const nodeHeight = 80;
        const root = d3.hierarchy(rootData, d => d.children);
        const treeLayout = d3.tree().nodeSize([nodeWidth + 50, nodeHeight + 80]);
        treeLayout(root);
        const svg = d3.select(container).append("svg").attr("width", "100%").attr("height", "100%")
            .call(d3.zoom().scaleExtent([0.1, 2]).on("zoom", (e) => g.attr("transform", e.transform)))
            .append("g").attr("transform", `translate(${width/2}, 80) scale(0.9)`);
        const g = svg;
        g.selectAll(".link").data(root.links()).enter().append("path").attr("class", "link").attr("d", d3.linkVertical().x(d => d.x).y(d => d.y));
        const node = g.selectAll(".node").data(root.descendants()).enter().append("g")
            .attr("class", "node").attr("transform", d => `translate(${d.x},${d.y})`).on("click", (e, d) => klikNode(d.data)); 
        node.append("rect").attr("width", nodeWidth).attr("height", nodeHeight).attr("x", -nodeWidth/2).attr("y", -nodeHeight/2).attr("class", d => "status-" + (d.data.status || 'disetujui'));
        node.append("foreignObject").attr("width", nodeWidth).attr("height", nodeHeight).attr("x", -nodeWidth/2).attr("y", -nodeHeight/2)
            .append("xhtml:div").attr("class", "node-text-div").html(d => `<div>${d.data.nama_kinerja}</div>`);
    }
</script>

</body>
</html>