<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Usulan - Sekretariat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; overflow-x: hidden; }
        .sidebar-rka { min-height: 100vh; background: #1e293b; color: white; width: 260px; position: fixed; top: 0; left: 0; padding-top: 20px; z-index: 1000; }
        .sidebar-rka .nav-link { color: #94a3b8; padding: 12px 24px; display: flex; align-items: center; text-decoration: none; transition: 0.3s; border-left: 3px solid transparent; }
        .sidebar-rka .nav-link:hover { background: #334155; color: white; }
        .sidebar-rka .nav-link.active { background: #0f172a; color: #38bdf8; border-left-color: #38bdf8; font-weight: 600; }
        .sidebar-rka .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        .main-content { margin-left: 260px; padding: 30px; padding-bottom: 100px; }
        .badge-sbu { background-color: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .badge-ssh { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
        .floating-action-bar { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background: white; padding: 15px 30px; border-radius: 50px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: none; align-items: center; gap: 20px; z-index: 9999; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

    <div class="sidebar-rka">
        <div class="px-4 pb-4">
            <h5 class="fw-bold text-white mb-0">E-Budgeting</h5>
            <small class="text-muted">Modul Sekretariat</small>
        </div>
        <nav class="nav flex-column">
            <a href="{{ route('verifikasi.index') }}" class="nav-link active">
                <i class="bi bi-box-seam"></i> <span>Usulan Katalog</span>
                @php $cnt = \App\Models\Rka\KakDetail::where('is_manual', 1)->where('is_verified', 1)->count(); @endphp
                @if($cnt > 0) <span class="badge bg-danger rounded-pill ms-auto" style="font-size:0.7rem">{{ $cnt }}</span> @endif
            </a>
            <a href="{{ route('verifikasi.belanja') }}" class="nav-link">
                <i class="bi bi-file-earmark-spreadsheet"></i> <span>Rancangan Belanja</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="mt-4 w-100">
                @csrf
                <button type="submit" class="nav-link w-100 bg-transparent border-0 text-start"><i class="bi bi-box-arrow-left"></i> Logout</button>
            </form>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Verifikasi Usulan Katalog</h4>
                <div class="text-muted small">Monitoring input manual user.</div>
            </div>
            <div class="bg-white px-3 py-2 rounded shadow-sm border">
                <i class="bi bi-person-circle me-2 text-primary"></i> <span class="fw-bold small">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</span>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form id="bulkForm" method="POST">
            @csrf
            <input type="hidden" name="alasan_tolak" id="bulkReason">
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-check me-2"></i>Antrian Verifikasi</h6>
                    <small class="text-muted" id="selectedCountText">0 terpilih</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-4" style="width: 50px;">
                                        <input type="checkbox" class="form-check-input border-secondary" id="checkAll" style="cursor: pointer;">
                                    </th>
                                    <th>Item Pengajuan</th>
                                    <th>Kategori</th>
                                    <th>Harga & Volume</th>
                                    <th>Total (Rp)</th>
                                    <th>Asal KAK</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- INI YANG BENAR: MENGGUNAKAN $usulan --}}
                                @forelse($usulan as $item)
                                <tr>
                                    <td class="ps-4">
                                        <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="form-check-input item-checkbox border-secondary" style="cursor: pointer;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->nama_barang }}</div>
                                        <div class="small text-muted">{{ $item->keterangan ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->kategori == 'SSH' ? 'badge-ssh' : 'badge-sbu' }} rounded-pill px-3">{{ $item->kategori }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">Rp {{ number_format($item->harga_satuan) }}</div>
                                        <div class="small text-muted">x {{ $item->volume }} {{ $item->satuan }}</div>
                                    </td>
                                    <td class="fw-bold text-dark">Rp {{ number_format($item->total_harga) }}</td>
                                    <td>
                                        <div class="small fw-bold">ID: {{ $item->kak_id }}</div>
                                        <div class="small text-muted text-truncate" style="max-width: 150px;">{{ $item->kak->judul_kak ?? 'Kak Terhapus' }}</div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button type="button" class="btn btn-success btn-sm fw-bold px-3 rounded-pill" onclick="submitSingle('{{ route('verifikasi.approve', $item->id) }}')">ACC</button>
                                            <button type="button" class="btn btn-danger btn-sm fw-bold px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $item->id }}">Tolak</button>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalTolak{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0">
                                            <div class="modal-header bg-danger text-white border-0">
                                                <h6 class="modal-title fw-bold">Tolak Usulan</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <textarea id="reason{{ $item->id }}" class="form-control bg-light" rows="3" placeholder="Alasan penolakan..."></textarea>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-danger w-100 fw-bold rounded-pill" onclick="submitRejectSingle('{{ route('verifikasi.reject', $item->id) }}', '{{ $item->id }}')">Kirim Penolakan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">Tidak ada usulan baru.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="floating-action-bar" id="floatingBar">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark"><span id="countSelected">0</span> Item Dipilih</span>
            <div class="vr mx-2"></div>
            <button type="button" class="btn btn-success rounded-pill fw-bold px-4" onclick="submitBulkApprove()"><i class="bi bi-check-all me-1"></i> ACC Terpilih</button>
            <button type="button" class="btn btn-danger rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#modalBulkReject"><i class="bi bi-x-circle me-1"></i> Tolak Terpilih</button>
        </div>
    </div>

    <div class="modal fade" id="modalBulkReject" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold">Tolak Massal</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <textarea id="bulkReasonInput" class="form-control" rows="3" placeholder="Alasan untuk semua item terpilih..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger w-100 fw-bold rounded-pill" onclick="submitBulkReject()">Konfirmasi Tolak</button>
                </div>
            </div>
        </div>
    </div>

    <form id="singleActionForm" method="POST" style="display:none;">@csrf</form>
    <form id="singleRejectForm" method="POST" style="display:none;">@csrf <input type="hidden" name="alasan_tolak" id="singleReasonInput"></form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const checkAll = document.getElementById('checkAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const floatingBar = document.getElementById('floatingBar');
        const countSelected = document.getElementById('countSelected');
        const selectedCountText = document.getElementById('selectedCountText');
        const bulkForm = document.getElementById('bulkForm');

        function updateState() {
            let count = 0;
            itemCheckboxes.forEach(cb => { if(cb.checked) count++; });
            countSelected.innerText = count;
            selectedCountText.innerText = count + " terpilih";
            floatingBar.style.display = count > 0 ? 'flex' : 'none';
        }
        checkAll.addEventListener('change', function() { itemCheckboxes.forEach(cb => cb.checked = checkAll.checked); updateState(); });
        itemCheckboxes.forEach(cb => { cb.addEventListener('change', updateState); });

        function submitBulkApprove() { if(confirm('Yakin setujui semua?')) { bulkForm.action = "{{ route('verifikasi.bulk_approve') }}"; bulkForm.submit(); } }
        function submitBulkReject() { 
            const reason = document.getElementById('bulkReasonInput').value;
            if(!reason) { alert('Isi alasan!'); return; }
            document.getElementById('bulkReason').value = reason;
            bulkForm.action = "{{ route('verifikasi.bulk_reject') }}"; bulkForm.submit();
        }
        function submitSingle(url) { if(confirm('ACC item ini?')) { const f = document.getElementById('singleActionForm'); f.action = url; f.submit(); } }
        function submitRejectSingle(url, id) {
            const r = document.getElementById('reason'+id).value;
            if(!r) { alert('Isi alasan!'); return; }
            document.getElementById('singleReasonInput').value = r;
            const f = document.getElementById('singleRejectForm'); f.action = url; f.submit();
        }
    </script>
</body>
</html>