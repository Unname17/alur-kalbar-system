<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Verifikasi - Sekretariat</title>
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
    </style>
</head>
<body>

    <div class="sidebar-rka">
        <div class="px-4 pb-4">
            <h5 class="fw-bold text-white mb-0">E-Budgeting</h5>
            <small class="text-muted">Modul Sekretariat</small>
        </div>
        <nav class="nav flex-column">
            <a href="{{ route('verifikasi.index') }}" class="nav-link">
                <i class="bi bi-box-seam"></i> <span>Usulan Katalog</span>
            </a>
            <a href="{{ route('verifikasi.belanja') }}" class="nav-link active">
                <i class="bi bi-file-earmark-spreadsheet"></i> <span>Rancangan Belanja</span>
            </a>
            <div class="mt-4 px-4"><a href="{{ route('verifikasi.belanja') }}" class="btn btn-outline-secondary w-100 btn-sm text-light border-secondary"> <i class="bi bi-arrow-left"></i> Kembali</a></div>
        </nav>
    </div>

    <div class="main-content">
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2">Dokumen KAK</span>
                        <h4 class="fw-bold text-dark mb-1">{{ $kak->judul_kak }}</h4>
                        <div class="text-muted small mb-3">Kode Proyek: {{ $kak->kode_proyek ?? '-' }}</div>
                        
                        <div class="d-flex gap-4 small text-secondary">
                            <div><i class="bi bi-person me-1"></i> {{ $kak->user->nama_lengkap ?? 'User' }}</div>
                            <div><i class="bi bi-calendar me-1"></i> Diajukan: {{ $kak->updated_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">Total Anggaran</div>
                        <h3 class="fw-bold text-success">Rp {{ number_format($kak->rincianBelanja->sum('total_harga'), 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold"><i class="bi bi-list-task me-2"></i>Rincian Item Belanja</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Item / Spesifikasi</th>
                            <th>Volume</th>
                            <th>Harga Satuan</th>
                            <th class="text-end pe-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kak->rincianBelanja as $index => $item)
                        <tr>
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $item->nama_barang }}</div>
                                <div class="small text-muted">{{ $item->keterangan ?? '-' }}</div>
                            </td>
                            <td>{{ $item->volume }} {{ $item->satuan }}</td>
                            <td>Rp {{ number_format($item->harga_satuan) }}</td>
                            <td class="text-end pe-4 fw-bold">Rp {{ number_format($item->total_harga) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold py-3">Total Keseluruhan</td>
                            <td class="text-end pe-4 fw-bold py-3 text-success">
                                Rp {{ number_format($kak->rincianBelanja->sum('total_harga'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Keputusan Verifikasi</h6>
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-muted small mb-0">
                            Jika disetujui, dokumen ini akan dianggap <b>SAH</b> dan masuk ke rekapitulasi anggaran.<br>
                            Jika ditolak, dokumen akan dikembalikan ke User untuk diperbaiki beserta catatannya.
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTolak">
                                <i class="bi bi-x-circle me-1"></i> Tolak / Revisi
                            </button>
                            <form action="{{ route('verifikasi.kak.acc', $kak->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold" onclick="return confirm('Yakin ingin MENGESAHKAN dokumen ini?')">
                                    <i class="bi bi-check-circle me-1"></i> SAHKAN Dokumen
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTolak" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-danger text-white border-0">
                    <h6 class="modal-title fw-bold">Kembalikan Dokumen (Revisi)</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('verifikasi.kak.tolak', $kak->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="fw-bold small mb-2">Catatan Perbaikan / Alasan Penolakan</label>
                            <textarea name="catatan" class="form-control" rows="4" placeholder="Contoh: Harga kertas terlalu mahal, mohon sesuaikan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Kirim Revisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>