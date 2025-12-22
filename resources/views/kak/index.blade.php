<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen KAK - Alur Kalbar</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .navbar { background: #1a237e !important; }
        .card { border: none; border-radius: 12px; }
        .table thead { background-color: #f1f3f9; }
        .btn-primary { background-color: #1a237e; border: none; }
        .btn-primary:hover { background-color: #0d47a1; }
        
        /* Status Color Badges */
        .bg-waiting { background-color: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb; }
        .bg-approved { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .bg-rejected { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        
        .breadcrumb-item + .breadcrumb-item::before { content: "›"; color: #9e9e9e; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm mb-0">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/dashboard">
                <i class="bi bi-grid-1x2-fill me-2"></i>
                <span>PORTAL ALUR | <small class="fw-light">Modul KAK</small></span>
            </a>
            
            <div class="ms-auto d-flex align-items-center">
                <div class="text-white small me-3 d-none d-md-block">
                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->nama ?? 'User Satker' }}
                </div>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="bg-white border-bottom mb-4">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-dark mb-0">Daftar Sub Kegiatan</h4>
                    <p class="text-muted mb-0 small">Pilih Sub Kegiatan untuk menyusun atau memverifikasi KAK</p>
                </div>
                <a href="/" class="btn btn-light btn-sm border rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Modul Kinerja
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm p-3 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white p-3 rounded-3 me-3">
                            <i class="bi bi-list-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted small">Total Sub Kegiatan</h6>
                            <h4 class="mb-0 fw-bold">{{ $listSubKegiatan->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white p-3 rounded-3 me-3">
                            <i class="bi bi-file-earmark-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted small">KAK Tersusun</h6>
                            <h4 class="mb-0 fw-bold">{{ $listSubKegiatan->where('kak', '!=', null)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm overflow-hidden mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="small fw-bold text-muted">
                                <th class="ps-4 py-3" style="width: 60px;">NO</th>
                                <th class="py-3">INFORMASI SUB KEGIATAN</th>
                                <th class="py-3 text-center">STATUS VERIFIKASI</th>
                                <th class="py-3 text-end pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
    @forelse($listSubKegiatan as $index => $sub)
    <tr class="table-light">
        <td class="ps-4 fw-bold text-primary">{{ $index + 1 }}</td>
        <td colspan="3">
            <div class="fw-bold"><i class="bi bi-folder2-open me-2"></i>SUB KEGIATAN: {{ $sub->nama_kinerja }}</div>
        </td>
    </tr>

    @foreach($sub->children as $rak)
    <tr>
        <td></td> <td class="ps-5">
            <div class="fw-semibold text-dark mb-0">{{ $rak->nama_kinerja }}</div>
            <div class="text-muted extra-small" style="font-size: 0.75rem;">
                <span class="badge bg-light text-dark border me-1">Rencana Aksi ID: {{ $rak->id }}</span>
                <i class="bi bi-person-badge"></i> PJ: {{ $rak->penanggung_jawab ?? 'Staf Pelaksana' }}
            </div>
        </td>
        <td class="text-center">
            @if(!$rak->kak)
                <span class="badge bg-light text-muted border rounded-pill px-3 py-2 small">Belum Disusun</span>
            @elseif($rak->kak->status == 1)
                <span class="badge bg-waiting rounded-pill px-3 py-2 small">Menunggu Verifikasi</span>
            @elseif($rak->kak->status == 2)
                <span class="badge bg-approved rounded-pill px-3 py-2 small">Disetujui</span>
            @elseif($rak->kak->status == 3)
                <span class="badge bg-rejected rounded-pill px-3 py-2 small">Ditolak</span>
            @endif
        </td>
        <td class="text-end pe-4">
    <div class="btn-group">
        @if($rak->kak)
            <a href="{{ route('kak.show', $rak->kak->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-eye"></i> Detail
            </a>

            @if($rak->kak->status == 1 && (Auth::user()->peran == 'sekretariat' || Auth::user()->peran == 'admin_utama'))
                <button type="button" class="btn btn-success btn-sm rounded-pill px-3 ms-1" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalVerifikasi"
                        data-id="{{ $rak->kak->id }}"
                        data-nama="{{ $rak->nama_kinerja }}">
                    <i class="bi bi-shield-check"></i> Verifikasi
                </button>
            @endif

            @if($rak->kak->status == 3)
                <a href="{{ route('kak.edit', $rak->kak->id) }}" class="btn btn-warning btn-sm rounded-pill px-3 ms-1">
                    <i class="bi bi-pencil-square"></i> Perbaiki
                </a>
            @endif
        @else
            <a href="{{ route('kak.create', $rak->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-plus-lg"></i> Susun KAK
            </a>
        @endif
    </div>
</td>
    </tr>
    @endforeach

    @empty
    <tr>
        <td colspan="4" class="text-center py-5 text-muted small">Data perencanaan belum tersedia.</td>
    </tr>
    @endforelse
    </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-3">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Data disinkronkan dari <strong>Modul Kinerja</strong> secara real-time.</small>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form id="formVerifikasi" method="POST" action="">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">Verifikasi KAK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4 text-center">
                        <i class="bi bi-shield-check text-primary fs-1 mb-3"></i>
                        <h6 class="fw-bold mb-1" id="verifNama"></h6>
                        <p class="small text-muted mb-4">Pastikan dokumen sudah sesuai dengan target kinerja</p>
                        
                        <div class="text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">KEPUTUSAN</label>
                                <select name="status" class="form-select border-primary" required>
                                    <option value="2">Setujui Dokumen</option>
                                    <option value="3">Tolak (Kembalikan)</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold small">CATATAN SEKRETARIAT</label>
                                <textarea name="catatan_sekretariat" class="form-control" rows="3" placeholder="Contoh: Mohon lengkapi data tim pelaksana..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Keputusan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalVerif = document.getElementById('modalVerifikasi');
        if (modalVerif) {
            modalVerif.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                
                document.getElementById('formVerifikasi').action = `/kak/verifikasi/${id}`;
                document.getElementById('verifNama').textContent = nama;
            });
        }
    </script>
</body>
</html>