<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perancangan RKA - {{ $kak->judul_kak }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }
        
        /* Navbar & Layout */
        .navbar-rka { background: #0f172a; color: white; padding: 1rem 0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .sticky-cart { position: sticky; top: 20px; z-index: 100; }
        
        /* Catalog Cards */
        .card-catalog { border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s; background: white; }
        .card-catalog:hover { border-color: #3b82f6; transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        
        /* Scrolls */
        .scroll-catalog { height: calc(100vh - 220px); overflow-y: auto; padding-right: 8px; }
        .scroll-cart { max-height: 400px; overflow-y: auto; }

        /* Badges & Colors */
        .badge-ssh { background-color: #dbeafe; color: #1e40af; }
        .badge-sbu { background-color: #ffedd5; color: #9a3412; }
    </style>
</head>
<body>

<nav class="navbar-rka mb-4">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('rka.pilih_kak') }}" class="text-white text-decoration-none d-flex align-items-center hover-opacity">
            <i class="bi bi-arrow-left-circle me-3 fs-4"></i>
            <div>
                <div class="fw-bold">Perancangan Anggaran</div>
                <div class="small opacity-75" style="font-size: 0.8rem;">{{ Str::limit($kak->judul_kak, 70) }}</div>
            </div>
        </a>
        <div class="text-end">
            <span class="badge bg-primary bg-opacity-25 text-primary-light border border-primary px-3 py-2 rounded-pill">TA 2025</span>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="bg-white p-3 rounded-4 shadow-sm mb-4 border">
                <div class="row g-2 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchSsh" class="form-control border-start-0 bg-light" placeholder="Cari barang, jasa, atau kode SSH...">
                        </div>
                    </div>
                    <div class="col-md-5 text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-success rounded-pill fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalImport">
                                <i class="bi bi-file-earmark-excel me-2"></i>Import
                            </button>
                            
                            <button class="btn btn-primary rounded-pill fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalManual">
                                <i class="bi bi-plus-lg me-2"></i>Baru
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="scroll-catalog pe-2" id="containerSsh">
                <div class="row g-3">
                    @foreach($katalog as $item)
                    <div class="col-xl-4 col-md-6 card-item-ssh" data-nama="{{ strtolower($item->nama_barang) }} {{ strtolower($item->kode_barang) }}">
                        <div class="card card-catalog h-100 p-3 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge {{ $item->kategori == 'SSH' ? 'badge-ssh' : 'badge-sbu' }} rounded-pill px-3">
                                    {{ $item->kategori }}
                                </span>
                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">{{ $item->kode_barang }}</small>
                            </div>
                            
                            <h6 class="fw-bold text-dark mb-1">{{ $item->nama_barang }}</h6>
                            <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.8rem;">
                                {{ Str::limit($item->spesifikasi ?? 'Tanpa spesifikasi khusus', 50) }}
                            </p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Harga Satuan</small>
                                        <span class="fw-bold text-primary">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                                    </div>
                                    <span class="badge bg-light text-dark border">/ {{ $item->satuan }}</span>
                                </div>
                                
                                <form action="{{ route('rka.store', $kak->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="ssh_id" value="{{ $item->id }}">
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="volume" class="form-control" placeholder="Vol" step="0.1" min="0" required>
                                        <button type="submit" class="btn btn-dark px-3">Pilih</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 sticky-cart overflow-hidden">
                
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cart-check me-2 text-success"></i>Rincian Belanja (Sah)</h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">ACC</span>
                </div>

                <div class="scroll-cart bg-white">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="bg-light small">
                            <tr>
                                <th class="ps-3 border-0 py-2">Item</th>
                                <th class="text-end border-0 py-2 pe-3">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalSah = 0; @endphp
                            
                            {{-- Hanya Item Yang Terverifikasi (Status 2) --}}
                            @forelse($kak->rincianBelanja->where('is_verified', 2) as $rb)
                                @php $totalSah += $rb->total_harga; @endphp
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="fw-bold text-dark small">{{ $rb->nama_barang }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            {{ $rb->volume }} {{ $rb->satuan }} &times; {{ number_format($rb->harga_satuan, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="fw-bold small">{{ number_format($rb->total_harga, 0, ',', '.') }}</div>
                                        <form action="{{ route('rka.destroy', $rb->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 small text-decoration-none" style="font-size: 0.7rem;">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted small fst-italic">Belum ada item sah di keranjang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-dark p-3 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small opacity-75">Total Anggaran Sah:</span>
                        <h4 class="fw-bold mb-0">Rp {{ number_format($totalSah, 0, ',', '.') }}</h4>
                    </div>
                </div>

                @php $usulanPending = $kak->rincianBelanja->where('is_verified', '!=', 2); @endphp
                
                @if($usulanPending->count() > 0)
                <div class="bg-light border-top p-3">
                    <h6 class="fw-bold text-muted small mb-3 text-uppercase ls-1">
                        <i class="bi bi-clock-history me-1"></i> Status Usulan Manual
                    </h6>
                    
                    <div class="d-flex flex-column gap-2">
                        @foreach($usulanPending as $pending)
                        <div class="p-3 rounded-3 shadow-sm bg-white border {{ $pending->is_verified == 0 ? 'border-danger' : 'border-warning' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="fw-bold small text-dark lh-sm">{{ $pending->nama_barang }}</div>
                                    
                                    @if($pending->is_verified == 0)
                                        <span class="badge bg-danger text-white mt-1" style="font-size: 0.6rem;">DITOLAK ADMIN</span>
                                    @else
                                        <span class="badge bg-warning text-dark mt-1" style="font-size: 0.6rem;">MENUNGGU VERIFIKASI</span>
                                    @endif
                                </div>

                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" 
                                            data-bs-toggle="modal" data-bs-target="#modalEdit{{ $pending->id }}" title="Edit Usulan">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    
                                    <form action="{{ route('rka.destroy', $pending->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center text-muted border-top pt-2 mt-2" style="font-size: 0.75rem;">
                                <span>{{ $pending->volume }} {{ $pending->satuan }} x {{ number_format($pending->harga_satuan) }}</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($pending->total_harga) }}</span>
                            </div>
                        </div>
                        {{-- NOTE: KODE MODAL EDIT SUDAH DIPINDAH KE BAWAH FILE AGAR Z-INDEX AMAN --}}
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="p-3 bg-white border-top">
                     <form action="{{ route('rka.finalisasi', $kak->id) }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm"
        onclick="return confirm('Apakah Anda yakin? Setelah finalisasi, RKA tidak dapat diubah lagi sampai diperiksa Admin.')">
        <i class="bi bi-send-check me-2"></i>Finalisasi RKA
    </button>
</form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalManual" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Usulan Item Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('rka.store', $kak->id) }}" method="POST">
                @csrf
                <input type="hidden" name="is_manual" value="1">
                
                <div class="modal-body p-4">
                    <div class="alert alert-info d-flex align-items-start small p-2 mb-3 rounded-3">
                        <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                        <div>Item ini akan diperiksa oleh Sekretariat sebelum masuk ke Total Anggaran.</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small mb-1">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select bg-light" required>
                            <option value="" disabled selected>-- Pilih Jenis Item --</option>
                            <option value="SBU">SBU (Jasa, Honor, Konsumsi, Sewa)</option>
                            <option value="SSH">SSH (Barang Fisik, ATK, Aset)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small mb-1">Nama Barang / Jasa <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Jasa Catering Rapat..." required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="fw-bold small mb-1">Estimasi Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="harga_satuan" class="form-control" placeholder="0" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="fw-bold small mb-1">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" placeholder="Pax/Unit" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small mb-1">Volume <span class="text-danger">*</span></label>
                        <input type="number" name="volume" class="form-control" placeholder="Jumlah..." step="0.1" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold small mb-1">Keterangan / Spesifikasi</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Jelaskan spesifikasi detail..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Kirim Usulan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="fw-bold text-success"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Import Rincian Belanja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rka.import', $kak->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-light border-success border text-success small mb-3">
                        <i class="bi bi-lightbulb me-1"></i> Gunakan fitur ini untuk upload banyak item sekaligus. Semua item akan masuk status <b>Menunggu Verifikasi</b>.
                    </div>

                    <div class="mb-3 text-center">
                        <a href="{{ route('rka.download_template') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="bi bi-download me-1"></i> Download Template Excel
                        </a>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Upload File (.xlsx / .csv)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold">Upload & Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($usulanPending) && $usulanPending->count() > 0)
    @foreach($usulanPending as $pending)
    <div class="modal fade" id="modalEdit{{ $pending->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Usulan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('rka.update_manual', $pending->id) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="modal-body p-4">
                        <div class="alert alert-light border d-flex align-items-start gap-3 mb-4 rounded-3">
                            <div class="bg-white p-2 rounded-3 shadow-sm text-primary">
                                <i class="bi bi-box-seam fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Item Usulan</small>
                                <div class="fw-bold text-dark">{{ $pending->nama_barang }}</div>
                                <div class="small text-muted">Kategori: {{ $pending->kategori ?? '-' }}</div>
                                <input type="hidden" name="nama_barang" value="{{ $pending->nama_barang }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-secondary">Harga Satuan (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 fw-bold text-muted">Rp</span>
                                    <input type="number" name="harga_satuan" class="form-control border-start-0 ps-1" 
                                           value="{{ $pending->harga_satuan }}" required min="0">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-secondary">Volume / Satuan</label>
                                <div class="input-group">
                                    <input type="number" name="volume" class="form-control" 
                                           value="{{ $pending->volume }}" required min="0" step="any">
                                    <span class="input-group-text bg-light text-muted small">{{ $pending->satuan }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold text-secondary">Keterangan / Revisi</label>
                            <textarea name="keterangan" class="form-control bg-light" rows="3">{{ $pending->keterangan }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Filter Pencarian Katalog
    document.getElementById('searchSsh').addEventListener('keyup', function() {
        let keyword = this.value.toLowerCase();
        let cards = document.querySelectorAll('.card-item-ssh');
        
        cards.forEach(card => {
            let text = card.getAttribute('data-nama');
            if (text.includes(keyword)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>