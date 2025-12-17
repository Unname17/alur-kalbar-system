<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perancangan Anggaran (RKA) | Alur Kalbar</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3436;
        }

        .navbar-custom {
            background: white;
            border-bottom: 1px solid #edf2f7;
            padding: 1rem 0;
        }

        .card-kak {
            border: none;
            border-radius: 20px;
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .card-kak:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-disetujui {
            background-color: #d1fae5;
            color: #065f46;
        }

        .kak-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .bg-soft-primary { background-color: #e0e7ff; color: #4338ca; }

        .btn-rancang {
            background: #4338ca;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-rancang:hover {
            background: #3730a3;
            color: white;
            box-shadow: 0 4px 12px rgba(67, 56, 202, 0.3);
        }

        .nomor-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 0.75rem;
        }

        .empty-state {
            padding: 5rem 0;
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-custom sticky-top mb-5">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <div class="bg-primary rounded-3 p-2 me-2">
                <i class="bi bi-layers-half text-white"></i>
            </div>
            <span class="fw-bold text-dark">ALUR-KALBAR</span>
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
            <i class="bi bi-grid-fill me-2"></i>Dashboard Portal
        </a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row mb-5">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-2">Perancangan Anggaran (RKA)</h2>
            <p class="text-muted fs-5">Langkah kedua: Menyusun detail belanja berdasarkan KAK yang sudah diverifikasi.</p>
        </div>
        <div class="col-lg-4 text-lg-end d-flex align-items-center justify-content-lg-end">
            <div class="text-end me-3">
                <div class="small text-muted">Total KAK Siap Rancang</div>
                <div class="h4 fw-bold mb-0 text-primary">{{ $listKak->count() }}</div>
            </div>
            <div class="vr mx-3 h-50"></div>
            <i class="bi bi-calculator fs-1 text-primary"></i>
        </div>
    </div>

    <div class="row g-4">
        @forelse($listKak as $kak)
        <div class="col-md-6 col-lg-4">
            <div class="card card-kak shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kak-icon-wrapper bg-soft-primary">
                            <i class="bi bi-file-earmark-check fs-3"></i>
                        </div>
                        <span class="status-badge badge-disetujui">Disetujui</span>
                    </div>

                    <h5 class="fw-bold text-dark mb-2 lh-base" style="height: 3.5rem; overflow: hidden;">
                        {{ $kak->judul_kak }}
                    </h5>
                    
                    <p class="text-muted small mb-4">
                        <i class="bi bi-diagram-2 me-1"></i> {{ $kak->pohonKinerja->nama_kinerja ?? 'Program Strategis' }}
                    </p>

                    <div class="nomor-box mb-4">
                        <label class="text-muted small d-block mb-1">Nomor KAK Resmi:</label>
                        <span class="font-monospace fw-bold text-primary">{{ $kak->nomor_kak }}</span>
                    </div>

                    <div class="d-grid">
                        <a href="{{ route('rka.index', $kak->id) }}" class="btn btn-rancang">
                            <i class="bi bi-pencil-square me-2"></i>Mulai Rancang RKA
                        </a>
                    </div>
                </div>
                
                <div class="position-absolute bottom-0 start-0 w-100 bg-primary" style="height: 4px; opacity: 0.1;"></div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state bg-white rounded-5 shadow-sm">
                <i class="bi bi-folder-x fs-1 text-muted mb-3 d-block"></i>
                <h4 class="fw-bold text-muted">Belum Ada KAK Terverifikasi</h4>
                <p class="text-muted px-5">Hanya Sub Kegiatan dengan status "Disetujui" di Modul KAK yang akan muncul di sini untuk tahap perancangan anggaran.</p>
                <a href="{{ route('kak.index') }}" class="btn btn-primary rounded-pill px-4 mt-3">Ke Modul KAK</a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<footer class="text-center text-muted py-4 small mt-5">
    &copy; 2025 Pemerintah Provinsi Kalimantan Barat - Sistem Alur Kalbar
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>