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
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        .navbar-custom { background: white; border-bottom: 1px solid #e2e8f0; padding: 1rem 0; }
        
        /* Card Styling */
        .card-kak { border: 1px solid #e2e8f0; border-radius: 24px; background: white; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
        .card-kak:hover { transform: translateY(-8px); border-color: #4338ca; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); }
        
        /* Badge Styling */
        .status-badge { font-size: 0.65rem; font-weight: 800; padding: 0.5rem 1rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-sync { background-color: #e0e7ff; color: #4338ca; }
        .badge-pengajuan { background-color: #fef3c7; color: #92400e; }
        .badge-revisi { background-color: #fee2e2; color: #dc2626; }
        .badge-ready { background-color: #f1f5f9; color: #64748b; }
        .badge-final { background-color: #dcfce7; color: #166534; }

        /* Icon Wrapper */
        .kak-icon-wrapper { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 0; }
        .bg-soft-success { background-color: #f0fdf4; color: #16a34a; }
        .bg-soft-primary { background-color: #eef2ff; color: #4338ca; }
        .bg-soft-warning { background-color: #fffbeb; color: #d97706; }
        .bg-soft-danger { background-color: #fff1f2; color: #e11d48; }
        
        /* Typography & Buttons */
        .judul-kak { font-size: 1rem; font-weight: 700; line-height: 1.5; color: #0f172a; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 3rem; }
        .btn-rancang { background: #4338ca; color: white; border: none; border-radius: 14px; padding: 0.8rem; font-weight: 700; transition: 0.2s; }
        .btn-rancang:hover { background: #3730a3; color: white; box-shadow: 0 10px 15px -3px rgba(67, 56, 202, 0.3); }
        .stat-box { border: 1px solid #e2e8f0; background: white; border-radius: 20px; }
        .pagu-box { background-color: #f8fafc; border-radius: 14px; padding: 0.8rem; border: 1px solid #f1f5f9; }
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
            <i class="bi bi-grid-fill me-2"></i>Portal Utama
        </a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
            <h2 class="fw-bold mb-2">Perancangan Anggaran (RKA)</h2>
            <p class="text-muted mb-0">Susun rincian belanja berdasarkan dokumen perencanaan yang telah disahkan.</p>
            
            <form action="{{ route('rka.sync_all') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm py-2 fw-bold" onclick="return confirm('Tarik semua data KAK terbaru?')">
                    <i class="bi bi-arrow-repeat me-2"></i> Sinkronkan Semua Data KAK
                </button>
            </form>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
            <div class="d-inline-flex align-items-center stat-box p-3 shadow-sm">
                <div class="text-end me-3">
                    <div class="small text-muted">Total KAK Tersedia</div>
                    <div class="h4 fw-bold mb-0 text-primary">{{ $listKak->count() }}</div>
                </div>
                <div class="vr mx-3 h-50"></div>
                <i class="bi bi-calculator fs-1 text-primary"></i>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
    @forelse($listKak as $item)
        @php
            $local = $localKaks->where('kak_id', $item->id)->first();
        @endphp

        <div class="col-md-6 col-lg-4">
            <div class="card card-kak shadow-sm h-100">
                <div class="card-body p-4 d-flex flex-column">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="kak-icon-wrapper {{ $local ? ($local->status_internal == 'final' ? 'bg-soft-success' : ($local->status_internal == 'revisi' ? 'bg-soft-danger' : 'bg-soft-warning')) : 'bg-light' }}">
                            <i class="bi {{ $local ? ($local->status_internal == 'final' ? 'bi-patch-check-fill' : 'bi-file-earmark-text') : 'bi-cloud-download' }} fs-4"></i>
                        </div>
                        
                        @if($local)
                            <span class="status-badge {{ 'badge-'.$local->status_internal }}">
                                {{ $local->status_internal == 'final' ? 'RKA Disetujui' : ($local->status_internal == 'revisi' ? 'Perlu Revisi' : ($local->status_internal == 'pengajuan' ? 'Diverifikasi' : 'Tersinkron')) }}
                            </span>
                        @else
                            <span class="status-badge badge-ready">Siap Sinkron</span>
                        @endif
                    </div>

                    <h5 class="judul-kak mb-3">{{ $item->judul_kak }}</h5>

                    <div class="pagu-box mb-4">
                        <div class="text-muted small mb-1">Pagu Anggaran:</div>
                        <div class="fw-bold text-dark">
                            {{-- Menjumlahkan kolom 'total_harga' dari semua rincian belanja terkait --}}
        Rp {{ number_format($item->rincianBelanja->sum('total_harga') ?? 0, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="mt-auto d-grid">
                        @if($local)
                            @if($local->status_internal == 'final')
                                <button class="btn btn-success rounded-3 py-2 fw-bold mb-2 shadow-sm" disabled>
                                    <i class="bi bi-check-all me-2"></i>RKA Telah Disahkan
                                </button>
                                <a href="{{ route('rka.cetak', $item->id) }}" class="btn btn-outline-success border-2 rounded-3 small py-2">
                                    <i class="bi bi-printer me-1"></i> Cetak Dokumen RKA
                                </a>
                            @elseif($local->status_internal == 'pengajuan')
                                <button class="btn btn-secondary py-3 fw-bold rounded-4" disabled>
                                    <i class="bi bi-lock-fill me-2"></i>Sedang Diverifikasi
                                </button>
                            @else
                                <a href="{{ route('rka.index', $item->id) }}" class="btn btn-rancang py-3">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    {{ $local->status_internal == 'revisi' ? 'Perbaiki RKA' : 'Rancang RKA' }}
                                </a>
                            @endif
                        @else
                            <form action="{{ route('rka.sync_all') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary w-100 rounded-4 py-3 fw-bold border-2">
                                    <i class="bi bi-arrow-repeat me-2"></i>Sinkronkan Data
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted opacity-25"></i>
            <p class="text-muted mt-3">Tidak ada data perencanaan (KAK) yang ditemukan.</p>
        </div>
    @endforelse
    </div>
</div>

<footer class="text-center text-muted py-5 small">
    &copy; 2025 Pemerintah Provinsi Kalimantan Barat | Sistem Alur Kalbar
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>