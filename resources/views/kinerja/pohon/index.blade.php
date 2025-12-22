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
        #wrapper { display: flex; width: 100%; min-height: 100vh; }
        #sidebar { width: 250px; background-color: #2c3e50; color: white; flex-shrink: 0; min-height: 100vh; }
        #sidebar .sidebar-brand { padding: 20px; font-size: 1.2rem; font-weight: bold; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        #sidebar ul { list-style: none; padding: 0; margin-top: 20px; }
        #sidebar ul li a { display: block; padding: 15px 20px; color: rgba(255,255,255,0.8); text-decoration: none; transition: 0.3s; cursor: pointer; }
        #sidebar ul li a:hover, #sidebar ul li a.active { background-color: #34495e; color: white; border-left: 4px solid #3498db; }
        #content-wrapper { flex-grow: 1; display: flex; flex-direction: column; }
        #topbar { height: 60px; background: white; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }
        #main-content { padding: 20px; }
        
        /* VISUAL D3 */
        .viewport { width: 100%; height: 700px; overflow: hidden; background: #fff; border: 1px solid #e3e6f0; border-radius: 8px; position: relative; cursor: grab; background-image: radial-gradient(#e1e1e1 1px, transparent 1px); background-size: 20px 20px; }
        .node rect { stroke: #999; stroke-width: 1.5px; cursor: pointer; fill: #fff; rx: 6; ry: 6; transition: all 0.2s; }
        .node-text-div { font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; text-align: center; height: 100%; padding: 5px; line-height: 1.2; color: #333; pointer-events: none; }
        .link { fill: none; stroke: #ccc; stroke-width: 1.5px; }

        /* STATUS COLORS */
        .status-pengajuan rect { stroke: #f6c23e !important; stroke-dasharray: 5,2; fill: #fffcf5; }
        .status-ditolak rect { stroke: #e74a3b !important; fill: #fff5f5; }
        .status-disetujui rect { stroke: #1cc88a !important; fill: #f0fff4; }

        /* GRID SYSTEM */
        .m-header { display: grid; grid-template-columns: 120px 1fr 250px 120px 150px; background: #4e73df; color: white; font-weight: bold; padding: 10px; border-radius: 8px 8px 0 0; font-size: 0.8rem; text-align: center; }
        .m-row { display: grid; grid-template-columns: 120px 1fr 250px 120px 150px; border-bottom: 1px solid #e3e6f0; align-items: center; background: white; transition: 0.2s; }
        .m-row:hover { background-color: #f8f9fc; }
        .m-col { padding: 10px; border-right: 1px solid #e3e6f0; font-size: 0.85rem; height: 100%; display: flex; align-items: center; }
        .m-col:last-child { border-right: none; }
        
        .btn-drop { cursor: pointer; color: #4e73df; margin-right: 10px; transition: 0.3s; display: inline-block; }
        .collapsed .btn-drop { transform: rotate(-90deg); }
        .lvl-visi { border-left: 5px solid #4e73df; font-weight: bold; background: #f8f9fc; }
    </style>
</head>
<body>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-chart-line me-2"></i> E-KINERJA</div>
        <ul>
            <li><a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li><a onclick="switchView('visual')" id="menuPohon" class="active"><i class="fas fa-sitemap me-2"></i> Pohon Kinerja</a></li>
            
            {{-- Menu Inbox untuk Validator --}}
            @if(in_array(Auth::user()->peran, ['admin_utama', 'sekretariat', 'validator_bappeda',]))
                <li>
                    <a onclick="switchView('inbox')" id="menuInbox">
                        <i class="fas fa-inbox me-2"></i> Inbox Pengajuan
                        @if(isset($inbox) && $inbox->count() > 0)
                            <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">{{ $inbox->count() }}</span>
                        @endif
                    </a>
                </li>
            @endif

            {{-- MENU BARU: Inputan Ditolak untuk OPD --}}
            @if(!$isValidator)
                <li>
                    <a onclick="switchView('rejected')" id="menuRejected">
                        <i class="fas fa-times-circle me-2 text-warning"></i> Inputan Ditolak
                        @if(isset($rejected) && $rejected->count() > 0)
                            <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">{{ $rejected->count() }}</span>
                        @endif
                    </a>
                </li>
            @endif
        </ul>
        <div class="p-3 mt-auto text-center">
            <small class="text-white-50">Login: {{ Auth::user()->nama_lengkap }}</small>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">@csrf<button class="btn btn-sm btn-danger w-100">Logout</button></form>
        </div>
    </nav>

    <div id="content-wrapper">
        <nav id="topbar">
            <h5 class="m-0 text-dark fw-bold" id="pageTitle">Pohon Kinerja Visual</h5>
            <div class="text-secondary small">{{ now()->format('l, d F Y') }}</div>
        </nav>

        <div id="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div id="actionHeader" class="card shadow-sm mb-3">
                <div class="card-body py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted small"><i class="fas fa-info-circle"></i> Kelola struktur kinerja perangkat daerah.</span>
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group btn-group-sm shadow-sm">
                            <button type="button" class="btn btn-primary" id="btnViewVisual" onclick="switchView('visual')"><i class="fas fa-project-diagram me-1"></i> Pohon</button>
                            <button type="button" class="btn btn-outline-primary" id="btnViewMatriks" onclick="switchView('matriks')"><i class="fas fa-table me-1"></i> Matriks</button>
                        </div>
                        <div class="vr mx-2"></div>
                        <button class="btn btn-success btn-sm" onclick="bukaModalTambah()"><i class="fas fa-plus-circle"></i> Ajukan Baru</button>
                    </div>
                </div>
            </div>

            <div id="viewVisual">
                <div class="viewport shadow-sm" id="visualContainer"></div>
            </div>

            <div id="viewInbox" style="display:none">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-check me-2"></i> Daftar Pengajuan Masuk</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="m-header">
                            <div>LEVEL</div><div>URAIAN KINERJA</div><div>INDIKATOR</div><div class="text-center">TARGET</div><div class="text-center">AKSI</div>
                        </div>
                        @if(isset($inbox))
                            @forelse($inbox as $item)
                                <div class="m-row">
                                    <div class="m-col fw-bold text-center small text-uppercase" style="background: #f8f9fc;">{{ str_replace('_',' ',$item->jenis_kinerja) }}</div>
                                    <div class="m-col ps-3"><strong>{{ $item->nama_kinerja }}</strong></div>
                                    <div class="m-col flex-column align-items-start small">@foreach($item->indikators as $ind) <div>• {{ $ind->indikator }}</div> @endforeach</div>
                                    <div class="m-col text-center small">@foreach($item->indikators as $ind) <div>{{ $ind->target }} {{ $ind->satuan }}</div> @endforeach</div>
                                    <div class="m-col justify-content-center">
                                        <button class="btn btn-sm btn-primary shadow-sm" onclick="bukaModalValidasiInbox({{ $item->id }}, '{{ $item->nama_kinerja }}')">Periksa</button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 text-center text-muted">Tidak ada pengajuan baru.</div>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>

            <div id="viewRejected" style="display:none">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white py-3">
                        <h6 class="m-0 fw-bold"><i class="fas fa-exclamation-circle me-2"></i> Daftar Inputan Perlu Perbaikan</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="m-header bg-secondary">
                            <div>LEVEL</div><div>URAIAN KINERJA</div><div style="grid-column: span 2;">ALASAN PENOLAKAN</div><div class="text-center">AKSI</div>
                        </div>
                        @if(isset($rejected))
                            @forelse($rejected as $item)
                                <div class="m-row text-danger">
                                    <div class="m-col text-center small fw-bold">{{ strtoupper($item->jenis_kinerja) }}</div>
                                    <div class="m-col ps-3"><strong>{{ $item->nama_kinerja }}</strong></div>
                                    <div class="m-col small italic px-3" style="grid-column: span 2;">
                                        <i class="fas fa-comment-dots me-1"></i> {{ $item->catatan_penolakan ?? 'Harap revisi kembali data Anda.' }}
                                    </div>
                                    <div class="m-col justify-content-center">
                                        <button class="btn btn-sm btn-warning fw-bold shadow-sm" onclick="bukaModalEdit({{ $item->id }})">REVISI</button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 text-center text-muted">Bagus! Tidak ada data yang ditolak.</div>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>

            <div id="viewMatriks" style="display:none">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="m-header">
                            <div>LEVEL</div><div>URAIAN KINERJA</div><div>INDIKATOR</div><div>TARGET</div><div>ANGGARAN</div>
                        </div>
                        @if(isset($pohons))
                            @foreach($pohons as $node)
                                @include('kinerja.pohon.partial-cascading-row', ['node' => $node, 'level' => 0])
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div> 
    </div> 
</div> 

<div class="modal fade" id="modalValidasi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title fw-bold">Validasi Input Kinerja</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body py-3">
                <p class="small text-muted mb-3">Tentukan keputusan untuk inputan: <br><strong id="vNamaKinerja"></strong></p>
                <form id="formValidasi">
                    <input type="hidden" name="id" id="vNodeId">
                    <div class="mb-3"><label class="form-label small fw-bold">Keputusan</label><select class="form-select" name="action" id="vAction"><option value="setuju">Setujui</option><option value="tolak">Tolak</option></select></div>
                    <div class="mb-3"><label class="form-label small fw-bold">Catatan (Opsional)</label><textarea class="form-control" name="catatan" rows="3" placeholder="Alasan jika ditolak..."></textarea></div>
                    <div class="form-check bg-light p-2 rounded border"><input class="form-check-input ms-0 me-2" type="checkbox" name="bulk" value="1" id="vBulk"><label class="form-check-label small fw-bold" for="vBulk">Setujui Seluruh Ranting (Massal)</label></div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary btn-sm" onclick="eksekusiValidasi()">Simpan</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalForm" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title fw-bold">Form Pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="formKinerja">
                    <input type="hidden" id="nodeId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Induk Kinerja</label>
                            <select class="form-select form-select-sm" name="parent_id" id="parentSelect">
                                <option value="">-- Pilih Induk Kinerja --</option>
                                @if(isset($parents))
                                    @foreach($parents as $jenis => $daftarKinerja)
                                        <optgroup label="LEVEL: {{ strtoupper(str_replace('_', ' ', $jenis)) }}">
                                            @foreach($daftarKinerja as $item) <option value="{{ $item->id }}">{{ $item->nama_kinerja }}</option> @endforeach
                                        </optgroup>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Jenis Level</label>
                            <select class="form-select form-select-sm" name="jenis_kinerja" id="inputJenis">
                                <option value="program">Program</option>
                                <option value="kegiatan">Kegiatan</option>
                                <option value="sub_kegiatan">Sub Kegiatan</option>
                                <option value="rencana_aksi">Rencana Aksi</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label small fw-bold">Nama Kinerja</label><textarea class="form-control" name="nama_kinerja" id="inputNama" rows="2" required></textarea></div>
                    <div class="bg-light p-3 rounded border mb-3">
    <label class="small fw-bold mb-2 d-flex justify-content-between align-items-center">
        Indikator Kinerja
        <button type="button" class="btn btn-xs btn-primary py-0" onclick="tambahBarisIndikator()">+ Tambah</button>
    </label>
    <div id="indikatorList">
        {{-- Baris ini akan diisi otomatis oleh fungsi tambahBarisIndikator() --}}
    </div>
</div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary btn-sm" onclick="simpanData()">Simpan Data</button></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js"></script>

<script>
    const rawData = {!! json_encode($pohons ?? []) !!};
    const userPeran = "{{ Auth::user()->peran }}";

    function switchView(viewName) {
        // Reset Display Semua View
        const views = ['viewVisual', 'viewMatriks', 'viewManagement', 'viewInbox'];
        views.forEach(v => {
            const el = document.getElementById(v);
            if(el) el.style.display = 'none';
        });

        // Reset Active Class Sidebar
        const menuIds = ['menuPohon', 'menuManagement', 'menuInbox'];
        menuIds.forEach(m => {
            const el = document.getElementById(m);
            if(el) el.classList.remove('active');
        });

        // Tampilkan View Terpilih
        if (viewName === 'visual') {
            document.getElementById('viewVisual').style.display = 'block';
            document.getElementById('menuPohon').classList.add('active');
            document.getElementById('pageTitle').innerText = 'Pohon Kinerja Visual';
        } else if (viewName === 'matriks') {
            document.getElementById('viewMatriks').style.display = 'block';
            document.getElementById('pageTitle').innerText = 'Matriks Cascading Kinerja';
        } else if (viewName === 'management') {
            document.getElementById('viewManagement').style.display = 'block';
            document.getElementById('menuManagement').classList.add('active');
            document.getElementById('pageTitle').innerText = 'Manajemen Akses OPD';
        } else if (viewName === 'inbox') {
            document.getElementById('viewInbox').style.display = 'block';
            document.getElementById('menuInbox').classList.add('active');
            document.getElementById('pageTitle').innerText = 'Inbox Pengajuan Masuk';
        } else if (viewName === 'rejected') {
            document.getElementById('viewRejected').style.display = 'block';
            document.getElementById('menuRejected').classList.add('active');
            document.getElementById('pageTitle').innerText = 'Daftar Perbaikan Kinerja';
        }
    }

    function toggleRow(id) {
        const children = document.getElementById('child-' + id);
        const btn = document.getElementById('row-' + id);
        if (children) {
            children.style.display = (children.style.display === "none") ? "block" : "none";
            btn.classList.toggle('collapsed');
        }
    }

    function bukaModalTambah() {
        const modal = new bootstrap.Modal(document.getElementById('modalForm'));
        $('#formKinerja')[0].reset();
        $('#nodeId').val('');
        modal.show();
    }


    function bukaModalValidasiInbox(id, nama) {
        $('#vNodeId').val(id);
        $('#vNamaKinerja').text(nama);
        const modal = new bootstrap.Modal(document.getElementById('modalValidasi'));
        modal.show();
    }

    // FUNGSI UNTUK MENAMPILKAN DATA LAMA KE DALAM FORM
// Fungsi untuk menambah baris input indikator secara dinamis
// FUNGSI SIMPAN: Mengarahkan ke rute Update Anda
function simpanData() {
    const id = $('#nodeId').val();
    // Jika ada ID, gunakan rute update. Jika kosong, gunakan store.
    const url = id ? "{{ url('kinerja/update') }}/" + id : "{{ route('kinerja.store') }}";

    $.ajax({
        url: url,
        method: 'POST',
        data: $('#formKinerja').serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(res) { 
            alert(res.message); // Menampilkan "Perbaikan berhasil disimpan!"
            location.reload(); 
        },
        error: function() { alert("Gagal memproses data."); }
    });
}

// Fungsi untuk menambah baris dengan tombol hapus (logonya fa-trash)
function tambahBarisIndikator(val = '', target = '', satuan = '') {
    $('#indikatorList').append(`
        <div class="row g-2 mb-2 baris-indikator">
            <div class="col-6">
                <input type="text" name="indikator[]" class="form-control form-control-sm" value="${val}" placeholder="Indikator">
            </div>
            <div class="col-2">
                <input type="text" name="target[]" class="form-control form-control-sm" value="${target}" placeholder="Target">
            </div>
            <div class="col-2">
                <input type="text" name="satuan[]" class="form-control form-control-sm" value="${satuan}" placeholder="Satuan">
            </div>
            <div class="col-2 d-grid">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusBarisIndikator(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `);
}

// Fungsi untuk menghapus baris tertentu saat tombol ditekan
function hapusBarisIndikator(btn) {
    // Menghapus elemen row tempat tombol tersebut berada
    $(btn).closest('.baris-indikator').remove();
    
    // Pastikan minimal ada satu baris tersisa agar form tidak kosong total
    if ($('#indikatorList .baris-indikator').length === 0) {
        tambahBarisIndikator();
    }
}

// Update fungsi Tambah Baru agar mereset indikator
function bukaModalTambah() {
    $('#formKinerja')[0].reset();
    $('#nodeId').val('');
    $('#modalTitle').text('Form Pengajuan Baru');
    $('#indikatorList').html(''); // Kosongkan list
    tambahBarisIndikator(); // Berikan 1 baris kosong
    (new bootstrap.Modal(document.getElementById('modalForm'))).show();
}

// Update fungsi Revisi agar menarik data indikator
function bukaModalEdit(id) {
    $.get("{{ url('kinerja/edit-detail') }}/" + id, function(data) {
        $('#nodeId').val(data.id);
        $('#inputNama').val(data.nama_kinerja);
        $('#parentSelect').val(data.parent_id);
        $('#inputJenis').val(data.jenis_kinerja);
        $('#modalTitle').text('Revisi Pengajuan Kinerja');

        // Isi ulang indikator dari database
        $('#indikatorList').empty();
        if(data.indikators && data.indikators.length > 0) {
            data.indikators.forEach(ind => {
                tambahBarisIndikator(ind.indikator, ind.target, ind.satuan);
            });
        } else {
            tambahBarisIndikator();
        }

        (new bootstrap.Modal(document.getElementById('modalForm'))).show();
    });
}

        function eksekusiValidasi() {
    const id = $('#vNodeId').val();
    
    // Gunakan URL yang sesuai dengan rute di web.php
    const urlApprove = "{{ url('kinerja/approve') }}/" + id;

    $.ajax({
        url: urlApprove,
        method: 'POST',
        data: $('#formValidasi').serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(res) {
            alert(res.message);
            location.reload();
        },
        error: function(xhr) { 
            // Menampilkan pesan error jika tetap 404 atau lainnya
            console.log(xhr.responseText);
            alert("Gagal memproses validasi (Error " + xhr.status + ")"); 
        }
    });
}

    function lihatDetailOPD(opdId) {
        window.location.href = "{{ route('kinerja.pohon') }}?opd_id=" + opdId;
    }

    document.addEventListener("DOMContentLoaded", () => { 
        if (rawData && rawData.length > 0) {
            rawData.forEach(tree => renderD3Tree(tree)); 
        } else {
            document.getElementById('visualContainer').innerHTML = `<div class="p-5 text-center text-muted">Data pohon kinerja tidak ditemukan.</div>`;
        }
    });

    function renderD3Tree(data) {
        const container = d3.select("#visualContainer");
        const width = document.getElementById('visualContainer').clientWidth;
        const height = 700;
        
        const svg = container.append("svg")
            .attr("width", width).attr("height", height)
            .call(d3.zoom().on("zoom", (e) => g.attr("transform", e.transform)))
            .append("g");
        const g = svg;

        const root = d3.hierarchy(data);
        const treeLayout = d3.tree().nodeSize([250, 150]);
        treeLayout(root);

        g.selectAll(".link").data(root.links()).enter().append("path")
            .attr("class", "link")
            .attr("d", d3.linkVertical().x(d => d.x + width/2).y(d => d.y + 50));

        const node = g.selectAll(".node").data(root.descendants()).enter().append("g")
            .attr("class", "node")
            .attr("transform", d => `translate(${d.x + width/2},${d.y + 50})`)
            .on("click", (e, d) => {
                if(userPeran === 'sekretariat' || userPeran === 'admin_utama') {
                    $('#vNodeId').val(d.data.id);
                    $('#vNamaKinerja').text(d.data.nama_kinerja);
                    const modalValidasi = new bootstrap.Modal(document.getElementById('modalValidasi'));
                    modalValidasi.show();
                }
            });

        node.append("rect").attr("width", 200).attr("height", 60).attr("x", -100).attr("y", -30)
            .attr("class", d => "status-" + (d.data.status || 'pengajuan'));

        node.append("foreignObject").attr("width", 190).attr("height", 50).attr("x", -95).attr("y", -25)
            .append("xhtml:div").attr("class", "node-text-div")
            .html(d => `<div>${d.data.nama_kinerja}</div>`);
    }
</script>
</body>
</html>