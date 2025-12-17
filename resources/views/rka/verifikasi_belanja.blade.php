<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Belanja - Sekretariat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; overflow-x: hidden; }
        
        /* SIDEBAR STYLE */
        .sidebar-rka {
            min-height: 100vh;
            background: #1e293b;
            color: white;
            width: 260px;
            position: fixed;
            top: 0; left: 0;
            padding-top: 20px;
            z-index: 1000;
        }
        .sidebar-rka .nav-link {
            color: #94a3b8;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar-rka .nav-link:hover { background: #334155; color: white; }
        .sidebar-rka .nav-link.active {
            background: #0f172a;
            color: #38bdf8;
            border-left-color: #38bdf8;
            font-weight: 600;
        }
        .sidebar-rka .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
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
                @php $cnt = \App\Models\Rka\KakDetail::where('is_manual', 1)->where('is_verified', 1)->count(); @endphp
                @if($cnt > 0) <span class="badge bg-danger rounded-pill ms-auto" style="font-size:0.7rem">{{ $cnt }}</span> @endif
            </a>
            <a href="{{ route('verifikasi.belanja') }}" class="nav-link active">
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
                <h4 class="fw-bold text-dark mb-1">Verifikasi Dokumen RKA</h4>
                <div class="text-muted small">Daftar KAK yang sudah difinalisasi oleh Bidang/OPD.</div>
            </div>
            <div class="bg-white px-3 py-2 rounded shadow-sm border">
                <i class="bi bi-person-circle me-2 text-primary"></i> <span class="fw-bold small">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-inbox me-2"></i>RKA Masuk</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Judul Kegiatan (KAK)</th>
                                <th>Diajukan Oleh</th>
                                <th>Total Anggaran</th>
                                <th>Tanggal Finalisasi</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($daftarRka as $kak)
                                {{-- Hitung Total Belanja --}}
                                @php 
                                    $total = $kak->rincianBelanja()->sum('total_harga');
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ Str::limit($kak->judul_kak, 50) }}</div>
                                        <small class="text-muted">Kode: {{ $kak->kode_kegiatan ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">{{ $kak->user->nama_lengkap ?? 'User OPD' }}</div>
                                        <div class="small text-muted">{{ $kak->user->nip ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <h6 class="fw-bold text-primary mb-0">Rp {{ number_format($total, 0, ',', '.') }}</h6>
                                        <small class="text-muted">{{ $kak->rincianBelanja()->count() }} Item Belanja</small>
                                    </td>
                                    <td>
                                        <div class="small">{{ $kak->updated_at->format('d M Y') }}</div>
                                        <div class="small text-muted">{{ $kak->updated_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="text-end pe-4">
    <a href="{{ route('verifikasi.show', $kak->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
        <i class="bi bi-eye me-1"></i> Periksa
    </a>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="opacity-50 text-muted">
                                            <i class="bi bi-folder2-open display-4"></i>
                                            <div class="mt-2 fw-bold">Belum ada RKA masuk</div>
                                            <small>User belum melakukan finalisasi.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>