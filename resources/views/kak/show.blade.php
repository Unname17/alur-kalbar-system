<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail KAK - {{ $kak->judul_kak }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { background: #525659; font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; }
        .page-container { padding: 30px 0; }
        .paper { 
            background: white; width: 210mm; min-height: 297mm; 
            padding: 20mm; margin: 0 auto; box-shadow: 0 0 10px rgba(0,0,0,0.3); 
            position: relative; box-sizing: border-box; 
        }
        .header-kak { text-align: center; margin-bottom: 20px; text-transform: uppercase; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header-title { font-weight: bold; font-size: 14pt; text-decoration: underline; }
        .nomor-surat { text-align: center; margin-bottom: 20px; font-weight: bold; }
        .section-title { font-weight: bold; text-transform: uppercase; margin-top: 20px; margin-bottom: 5px; text-decoration: underline; }
        p { margin-bottom: 10px; text-align: justify; }
        
        /* Table Styling */
        .table-bordered th, .table-bordered td { border: 1px solid black !important; padding: 4px; }
        .bg-schedule { background-color: #0d6efd !important; -webkit-print-color-adjust: exact; }
        
        .watermark { 
            position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); 
            font-size: 80px; font-weight: bold; color: rgba(255, 0, 0, 0.08); 
            pointer-events: none; text-transform: uppercase; z-index: 0; 
            border: 10px solid rgba(255, 0, 0, 0.08); padding: 10px 40px;
        }

        @media print {
            body { background: white; }
            .d-print-none { display: none !important; }
            .page-container { padding: 0; }
            .paper { box-shadow: none; border: none; width: 100%; padding: 15mm; }
            @page { size: A4 portrait; margin: 0; }
        }
    </style>
</head>
<body>

<div class="page-container">
    <div class="container d-print-none mb-4" style="width: 210mm;">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm">
            <a href="{{ route('kak.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
            <div class="btn-group">
    @if($kak->status != 2)
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTimeline">
            <i class="bi bi-calendar-range"></i> Atur Jadwal
        </button>
    @endif

    @if($kak->status == 2)
        <button onclick="window.print()" class="btn btn-danger btn-sm">
            <i class="bi bi-printer"></i> Cetak PDF
        </button>
    @endif
</div>
        </div>
    </div>

    <div class="paper">
        @if($kak->status != 2)
            <div class="watermark">{{ $kak->status == 3 ? 'DITOLAK' : 'DRAFT' }}</div>
        @endif

        <div class="header-kak">
            <div class="header-title">KERANGKA ACUAN KERJA (KAK)</div>
            <div class="fw-bold">{{ $kak->pohonKinerja->nama_kinerja }}</div>
            <div class="fw-bold small">TAHUN ANGGARAN 2025</div>
        </div>

        <div class="nomor-surat">
            NOMOR : {{ $kak->nomor_kak ?? '..................../ALUR-KALBAR/KAK/2025' }}
        </div>

        <table class="table table-borderless mb-3" style="font-size: 11pt;">
            <tr><td width="180"><strong>Judul KAK</strong></td><td width="10">:</td><td>{{ $kak->judul_kak }}</td></tr>
            <tr><td><strong>Kode Proyek</strong></td><td>:</td><td>{{ $kak->kode_proyek ?? '-' }}</td></tr>
            <tr><td><strong>Lokasi</strong></td><td>:</td><td>{{ $kak->lokasi ?? '-' }}</td></tr>
        </table>

        <div class="section-title">I. LATAR BELAKANG</div>
        <p>{!! nl2br(e($kak->latar_belakang)) !!}</p>

        <div class="section-title">II. MAKSUD DAN TUJUAN</div>
        <p>{{ $kak->maksud_tujuan }}</p>

        <div class="section-title">III. TIM PELAKSANA</div>
        <table class="table table-bordered small">
            <thead class="table-light text-center">
                <tr><th width="40">No</th><th>Nama Personil / NIP</th><th>Peran</th></tr>
            </thead>
            <tbody>
                @foreach($kak->timPelaksana as $no => $tim)
                <tr>
                    <td class="text-center">{{ $no + 1 }}</td>
                    <td><strong>{{ $tim->nama_personil }}</strong><br>NIP. {{ $tim->nip }}</td>
                    <td>{{ $tim->peran_dalam_tim }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">IV. JADWAL PELAKSANAAN</div>
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle small" style="font-size: 9pt;">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle">Tahapan Kegiatan</th>
                        <th colspan="12">Bulan Ke- (Tahun 2025)</th>
                    </tr>
                    <tr>
                        @for($i=1; $i<=12; $i++) <th width="25">{{ $i }}</th> @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse($kak->timelines as $tm)
                    <tr>
                        <td class="text-start ps-2"><strong>{{ $tm->nama_tahapan }}</strong></td>
                        @for($i=1; $i<=12; $i++)
                            @php $b = 'b'.$i; @endphp
                            <td class="{{ $tm->$b ? 'bg-schedule' : '' }}"></td>
                        @endfor
                    </tr>
                    @empty
                    <tr><td colspan="13" class="text-center text-muted py-3">Jadwal belum diatur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 40px; page-break-inside: avoid;">
            <table class="table table-borderless">
                <tr>
                    <td width="60%"></td>
                    <td width="40%" class="text-center">
                        <p>Pontianak, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                        <p class="fw-bold" style="margin-bottom: 60px;">Penanggung Jawab,</p>
                        <p class="fw-bold text-decoration-underline mb-0">( ........................................ )</p>
                        <p>NIP. ........................................</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="modal fade d-print-none" id="modalTimeline" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form action="{{ route('kak.timeline.store', $kak->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-calendar3 me-2"></i> Pengaturan Jadwal Kegiatan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm" id="tableTimeline">
                    <thead class="table-light text-center small font-sans-serif">
                        <tr>
                            <th width="300">Nama Tahapan</th>
                            @for($i=1; $i<=12; $i++) <th>{{ $i }}</th> @endfor
                            <th width="50">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kak->timelines as $key => $tm)
                        <tr>
                            <td><input type="text" name="nama_tahapan[]" class="form-control form-control-sm" value="{{ $tm->nama_tahapan }}" required></td>
                            @for($i=1; $i<=12; $i++)
                            <td class="text-center"><input type="checkbox" name="b{{ $i }}[{{ $key }}]" value="1" {{ $tm->{'b'.$i} ? 'checked' : '' }}></td>
                            @endfor
                            <td><button type="button" class="btn btn-outline-danger btn-sm remove-row border-0"><i class="bi bi-trash"></i></button></td>
                        </tr>
                        @empty
                        <tr>
                            <td><input type="text" name="nama_tahapan[]" class="form-control form-control-sm" placeholder="Contoh: Persiapan..." required></td>
                            @for($i=1; $i<=12; $i++) <td class="text-center"><input type="checkbox" name="b{{ $i }}[0]" value="1"></td> @endfor
                            <td></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="addRow"><i class="bi bi-plus-circle"></i> Tambah Tahapan Baru</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm px-4">Simpan Perubahan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let rowIdx = {{ $kak->timelines->count() > 0 ? $kak->timelines->count() : 1 }};
    document.getElementById('addRow').addEventListener('click', function() {
        let tbody = document.querySelector('#tableTimeline tbody');
        let tr = document.createElement('tr');
        let content = `<td><input type="text" name="nama_tahapan[]" class="form-control form-control-sm" required></td>`;
        for(let i=1; i<=12; i++) {
            content += `<td class="text-center"><input type="checkbox" name="b${i}[${rowIdx}]" value="1"></td>`;
        }
        content += `<td><button type="button" class="btn btn-outline-danger btn-sm remove-row border-0"><i class="bi bi-trash"></i></button></td>`;
        tr.innerHTML = content;
        tbody.appendChild(tr);
        rowIdx++;
    });
    document.addEventListener('click', e => { if(e.target.closest('.remove-row')) e.target.closest('tr').remove(); });
</script>

</body>
</html>