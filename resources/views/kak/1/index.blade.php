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
        .bg-waiting { background-color: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb; }
        .bg-approved { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .bg-rejected { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>
    @include('layouts.navbar_kak') <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Daftar Sub Kegiatan</h4>
            <a href="/" class="btn btn-light btn-sm border rounded-pill px-3">Kembali ke Modul Kinerja</a>
        </div>

        <div class="card shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="small fw-bold">
                            <th class="ps-4 py-3">NO</th>
                            <th>INFORMASI SUB KEGIATAN</th>
                            <th class="text-center">STATUS VERIFIKASI</th>
                            <th class="text-end pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listSubKegiatan as $index => $item)
                        <tr>
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $item->nama_kinerja }}</div>
                                <small class="text-muted">ID: {{ $item->id }}</small>
                            </td>
                            <td class="text-center">
                                @if(!$item->kak)
                                    <span class="badge bg-light text-muted border px-3 py-2">Belum Dibuat</span>
                                @elseif($item->kak->status == 1)
                                    <span class="badge bg-waiting px-3 py-2">Menunggu Verifikasi</span>
                                @elseif($item->kak->status == 2)
                                    <span class="badge bg-approved px-3 py-2">Disetujui</span>
                                @elseif($item->kak->status == 3)
                                    <span class="badge bg-rejected px-3 py-2">Ditolak</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($item->kak)
                                    <a href="{{ route('kak.show', $item->kak->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Detail</a>
                                @else
                                    <a href="{{ route('kak.create', $item->id) }}" class="btn btn-primary btn-sm rounded-pill px-3">Susun KAK</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-5">Data tidak tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>