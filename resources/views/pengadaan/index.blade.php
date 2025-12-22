<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrean Pengadaan | Alur-Kalbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7fa; font-family: 'Plus Jakarta Sans', sans-serif; color: #2d3436; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.07); }
        .table thead th { background-color: #f8fafc; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; color: #64748b; padding: 1.25rem 1rem; }
        .progress { border-radius: 50px; background-color: #e2e8f0; }
        .badge-soft { font-weight: 700; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.7rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">Daftar Antrean Pengadaan</h2>
            <p class="text-muted mb-0">Manajemen paket pekerjaan hasil sinkronisasi dari RKA yang disetujui.</p>
        </div>
        <form action="{{ route('pengadaan.sync') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-arrow-repeat me-2"></i>Sinkronkan Data RKA
            </button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Kegiatan / Paket</th>
                        <th>Metode</th>
                        <th>Progres Fisik (BAST)</th>
                        <th class="text-center">Dokumen</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarPengadaan as $item)
                    <tr>
                        <td class="ps-4">
                            {{-- Pastikan relasi rkaPerencanaan sudah di-eager load di Controller --}}
                            <div class="fw-bold text-dark mb-0">{{ $item->rkaPerencanaan->judul_kak ?? 'Judul Tidak Tersedia' }}</div>
                            <small class="text-muted">Nomor RKA: {{ $item->rka->nomor_rka ?? '-' }}</small>
                        </td>
                        <td>
                            @if($item->metode_pengadaan)
                                <span class="badge badge-soft bg-primary bg-opacity-10 text-primary text-uppercase">{{ $item->metode_pengadaan }}</span>
                            @else
                                <span class="badge badge-soft bg-light text-muted border">Belum Dipilih</span>
                            @endif
                        </td>
                        <td style="min-width: 200px;">
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="fw-bold">{{ $item->realisasi_volume }} / {{ $item->target_volume }} Unit</span>
                                <span class="text-muted">{{ $item->target_volume > 0 ? round(($item->realisasi_volume / $item->target_volume) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                @php $persen = $item->target_volume > 0 ? ($item->realisasi_volume / $item->target_volume) * 100 : 0; @endphp
                                <div class="progress-bar bg-success" style="width: {{ $persen }}%"></div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $item->documents->whereNotNull('file_path')->count() }}</span><span class="text-muted small">/9</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('pengadaan.show', $item->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                                Kelola Berkas <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted opacity-25 d-block mb-3"></i>
                            <p class="text-muted mb-0">Belum ada paket pengadaan. Klik sinkronkan untuk menarik data dari RKA.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>