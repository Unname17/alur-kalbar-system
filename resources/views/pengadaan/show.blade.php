<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengadaan - Alur Kalbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .list-group-item { border-left: none; border-right: none; padding: 1.25rem 1.5rem; transition: 0.3s; }
        .list-group-item:hover { background-color: #fcfcfc; }
        .bg-dark-card { background-color: #1a1d20; color: white; }
        .step-icon { width: 35px; height: 35px; font-size: 14px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pengadaan.index') }}" class="text-decoration-none">Pengadaan</a></li>
            <li class="breadcrumb-item active">Detail Berkas</li>
        </ol>
    </nav>
    
    <h2 class="fw-bold mb-1">{{ $pengadaan->rkaPerencanaan->judul_kak ?? 'Detail Paket' }}</h2>
    <p class="text-muted mb-4 small">Kelola kelengkapan 9 dokumen pengadaan sesuai metode yang telah dikunci.</p>

    {{-- BAGIAN PILIH METODE (Hanya muncul jika belum pilih) --}}
    @if(!$pengadaan->metode_pengadaan)
    <div class="card bg-primary text-white shadow-sm mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1"><i class="bi bi-gear-wide-connected me-2"></i>Tentukan Metode Pengadaan</h5>
                <p class="mb-0 small opacity-75">Metode ini akan menentukan alur verifikasi berkas selanjutnya.</p>
            </div>
            <form action="{{ route('pengadaan.update_metode', $pengadaan->id) }}" method="POST" class="btn-group bg-white p-1 rounded-pill shadow-sm">
                @csrf @method('PUT')
                <button name="metode" value="katalog" class="btn btn-white text-primary fw-bold px-3 btn-sm rounded-pill">E-Katalog</button>
                <button name="metode" value="pl" class="btn btn-white text-primary fw-bold px-3 btn-sm rounded-pill border-start">PL</button>
                <button name="metode" value="tender" class="btn btn-white text-primary fw-bold px-3 btn-sm rounded-pill border-start">Tender</button>
            </form>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-folder-check me-2"></i>Dokumen Administrasi</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($pengadaan->documents as $doc)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="step-icon {{ $doc->file_path ? 'bg-success text-white' : 'bg-light text-muted' }} me-3">
                                @if($doc->file_path) <i class="bi bi-check-lg"></i> @else {{ $doc->urutan_dokumen }} @endif
                            </div>
                            <div>
                                <div class="fw-bold mb-0 {{ $doc->file_path ? 'text-success' : 'text-dark' }}">{{ $doc->nama_dokumen }}</div>
                                <small class="text-muted" style="font-size: 11px;">
                                    @if($doc->urutan_dokumen <= 2) <i class="bi bi-magic me-1"></i>Otomatis dari RKA @else <i class="bi bi-cloud-arrow-up me-1"></i>Upload PDF Manual @endif
                                </small>
                            </div>
                        </div>
                        <div>
                            @if($doc->urutan_dokumen <= 2)
                                {{-- Tombol Cetak PDF akan kita buat di langkah berikutnya --}}
                                <a href="{{ route('pengadaan.print', ['id' => $pengadaan->id, 'doc' => $doc->urutan_dokumen]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Generate</a>
                            @else
                                <button class="btn btn-sm {{ $doc->file_path ? 'btn-light border' : 'btn-primary' }} rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modal{{ $doc->id }}">
                                    <i class="bi bi-upload me-1"></i> {{ $doc->file_path ? 'Update' : 'Upload' }}
                                </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Progres Fisik (Pohon Kinerja) --}}
            <div class="card bg-dark-card shadow-sm mb-4">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold opacity-50 mb-3">Realisasi Pohon Kinerja</h6>
                    <div class="h2 fw-bold text-success mb-1">{{ $pengadaan->realisasi_volume }} / {{ $pengadaan->target_volume }}</div>
                    <p class="small opacity-50 mb-3">Unit Fisik Terpenuhi</p>
                    <div class="progress bg-secondary bg-opacity-25" style="height: 8px;">
                        @php $persen = $pengadaan->target_volume > 0 ? ($pengadaan->realisasi_volume / $pengadaan->target_volume) * 100 : 0; @endphp
                        <div class="progress-bar bg-success" style="width: {{ $persen }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Informasi Vendor --}}
            <div class="card shadow-sm">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold text-muted mb-3 text-start border-bottom pb-2">Informasi Vendor</h6>
                    @if($pengadaan->vendor)
                        <i class="bi bi-building-check fs-1 text-primary d-block mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">{{ $pengadaan->vendor->nama_perusahaan }}</h6>
                        <small class="text-muted d-block mb-3">NPWP: {{ $pengadaan->vendor->npwp }}</small>
                        <button class="btn btn-light btn-sm w-100 rounded-pill border">Ubah Vendor</button>
                    @else
                        <div class="py-3 text-muted">
                            <i class="bi bi-person-exclamation fs-2 opacity-25 d-block mb-2"></i>
                            <p class="small italic">Vendor belum dikunci. Pilih vendor saat mengunggah Dokumen Ke-4.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL UPLOAD UNTUK DOKUMEN 3-9 --}}
@foreach($pengadaan->documents as $doc)
    @if($doc->urutan_dokumen > 2)
    <div class="modal fade" id="modal{{ $doc->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('pengadaan.document.upload', $doc->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold small">Upload {{ $doc->nama_dokumen }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small fw-bold">Pilih Berkas PDF:</label>
                        <input type="file" name="file_pdf" class="form-control" accept=".pdf" required>
                    </div>

                    {{-- Khusus Dokumen 4: Pilih Vendor --}}
                    @if($doc->urutan_dokumen == 4)
                    <div class="mb-3">
                        <label class="small fw-bold text-primary">Penetapan Vendor Pelaksana:</label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ $pengadaan->vendor_id == $v->id ? 'selected' : '' }}>{{ $v->nama_perusahaan }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Khusus Dokumen 8: Input Realisasi --}}
                    @if($doc->urutan_dokumen == 8)
                    <div class="mb-3">
                        <label class="small fw-bold text-success">Jumlah Realisasi Fisik (Unit):</label>
                        <input type="number" name="realisasi_volume" class="form-control" value="{{ $pengadaan->target_volume }}" required>
                        <small class="text-muted">Masukkan jumlah barang/jasa yang benar-benar diterima.</small>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Simpan Dokumen</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>