<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit/Perbaikan KAK - {{ $kak->judul_kak }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .navbar { background: #1a237e !important; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .section-title { 
            border-left: 5px solid #1a237e; 
            padding-left: 15px; 
            margin-bottom: 25px; 
            font-weight: 700; 
            color: #1a237e;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .form-label { font-weight: 600; color: #444; font-size: 0.85rem; }
        .btn-update { background-color: #ffc107; border: none; color: #000; padding: 10px 25px; font-weight: bold; }
        .btn-update:hover { background-color: #ffb300; }
        .bg-light-custom { background-color: #f8f9fa; border: 1px solid #e9ecef; }
        .alert-reject { border-left: 5px solid #dc3545; background-color: #fff5f5; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('kak.index') }}">
                <i class="bi bi-arrow-left-circle me-2"></i>
                <span>KEMBALI KE DAFTAR KAK</span>
            </a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                @if($kak->status == 3)
                <div class="alert alert-reject shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-3 me-3"></i>
                        <div>
                            <h6 class="fw-bold text-danger mb-1">Catatan Penolakan Sekretariat:</h6>
                            <p class="mb-0 text-dark italic">"{{ $kak->catatan_sekretariat ?? 'Mohon perbaiki data sesuai ketentuan.' }}"</p>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('kak.update', $kak->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="pohon_kinerja_id" value="{{ $subKegiatan->id }}">

                    <div class="card">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark">PERBAIKAN DOKUMEN KAK</h5>
                            <span class="badge bg-warning text-dark px-3 py-2">Mode Edit</span>
                        </div>
                        <div class="card-body p-4 p-md-5">

                            <div class="section-title">I. Identitas Sub Kegiatan</div>
                            <div class="p-3 mb-4 rounded bg-light-custom">
                                <div class="row">
                                    <div class="col-md-3 text-muted small fw-bold">NAMA SUB KEGIATAN:</div>
                                    <div class="col-md-9 fw-bold text-primary">{{ $subKegiatan->nama_kinerja }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-3 text-muted small fw-bold">INDIKATOR:</div>
                                    <div class="col-md-9 small">
                                        <ul class="mb-0 ps-3">
                                            @foreach($subKegiatan->indikators as $ind)
                                                <li>{{ $ind->indikator }} (Target: {{ $ind->target }} {{ $ind->satuan }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label small">JUDUL KAK / NAMA PROYEK <span class="text-danger">*</span></label>
                                    <input type="text" name="judul_kak" class="form-control" value="{{ $kak->judul_kak }}" required shadow-sm>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small">KODE PROYEK</label>
                                    <input type="text" name="kode_proyek" class="form-control" value="{{ $kak->kode_proyek }}">
                                </div>
                            </div>

                            <div class="section-title">II. Gambaran Umum</div>
                            <div class="mb-4">
                                <label class="form-label small">LATAR BELAKANG (WHY) <span class="text-danger">*</span></label>
                                <textarea name="latar_belakang" class="form-control" rows="4" required>{{ $kak->latar_belakang }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small">DASAR HUKUM</label>
                                <textarea name="dasar_hukum" class="form-control" rows="3">{{ $kak->dasar_hukum }}</textarea>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small">MAKSUD & TUJUAN</label>
                                    <textarea name="maksud_tujuan" class="form-control" rows="3">{{ $kak->maksud_tujuan }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small">SASARAN / OUTPUT</label>
                                    <textarea name="sasaran" class="form-control" rows="3">{{ $kak->sasaran }}</textarea>
                                </div>
                            </div>

                            <div class="section-title">III. Rencana Pelaksanaan</div>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small">METODE PELAKSANAAN</label>
                                    <select name="metode_pelaksanaan" class="form-select">
                                        <option value="Swakelola" {{ $kak->metode_pelaksanaan == 'Swakelola' ? 'selected' : '' }}>Swakelola</option>
                                        <option value="Penyedia" {{ $kak->metode_pelaksanaan == 'Penyedia' ? 'selected' : '' }}>Penyedia (E-Katalog/Tender)</option>
                                        <option value="Hibah" {{ $kak->metode_pelaksanaan == 'Hibah' ? 'selected' : '' }}>Hibah / Bantuan</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small">LOKASI KEGIATAN</label>
                                    <input type="text" name="lokasi" class="form-control" value="{{ $kak->lokasi }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small">PENERIMA MANFAAT</label>
                                    <input type="text" name="penerima_manfaat" class="form-control" value="{{ $kak->penerima_manfaat }}">
                                </div>
                            </div>

                            <div class="section-title">IV. Tim Pelaksana</div>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered align-middle" id="tableTim">
                                    <thead class="bg-light small fw-bold">
                                        <tr>
                                            <th>NAMA PERSONIL</th>
                                            <th>NIP</th>
                                            <th>PERAN DALAM TIM</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kak->timPelaksana as $tim)
                                        <tr>
                                            <td><input type="text" name="nama_personil[]" class="form-control form-control-sm" value="{{ $tim->nama_personil }}"></td>
                                            <td><input type="text" name="nip[]" class="form-control form-control-sm" value="{{ $tim->nip }}"></td>
                                            <td><input type="text" name="peran_dalam_tim[]" class="form-control form-control-sm" value="{{ $tim->peran_dalam_tim }}"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 removeRow"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td><input type="text" name="nama_personil[]" class="form-control form-control-sm"></td>
                                            <td><input type="text" name="nip[]" class="form-control form-control-sm"></td>
                                            <td><input type="text" name="peran_dalam_tim[]" class="form-control form-control-sm"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 removeRow"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border fw-bold text-primary mb-5" id="addRow">
                                <i class="bi bi-plus-circle me-1"></i> TAMBAH PERSONIL
                            </button>

                            <div class="d-flex justify-content-between border-top pt-4">
                                <a href="{{ route('kak.index') }}" class="btn btn-light px-4 fw-bold text-muted small">BATAL</a>
                                <button type="submit" class="btn btn-update px-5 shadow">
                                    <i class="bi bi-send-check me-1"></i> SIMPAN PERUBAHAN & AJUKAN LAGI
                                </button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#addRow').click(function() {
                let html = `<tr>
                    <td><input type="text" name="nama_personil[]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="nip[]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="peran_dalam_tim[]" class="form-control form-control-sm"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 removeRow"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
                $('#tableTim tbody').append(html);
            });

            $(document).on('click', '.removeRow', function() {
                if ($('#tableTim tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                }
            });
        });
    </script>
</body>
</html>