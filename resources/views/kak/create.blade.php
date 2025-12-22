<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Susun KAK - {{ $subKegiatan->nama_kinerja }}</title>
    
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
        .btn-primary { background-color: #1a237e; border: none; padding: 10px 25px; }
        .btn-primary:hover { background-color: #0d47a1; }
        .bg-light-custom { background-color: #f8f9fa; border: 1px solid #e9ecef; }
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
                
                <form action="{{ route('kak.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pohon_kinerja_id" value="{{ $subKegiatan->id }}">

                    <div class="card">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-dark text-center">FORMULIR KERANGKA ACUAN KERJA (KAK)</h5>
                        </div>
                        <div class="card-body p-4 p-md-5">

                            <div class="section-title">I. Identitas Sub Kegiatan (Referensi Modul Kinerja)</div>
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
                                    <input type="text" name="judul_kak" class="form-control" placeholder="Masukkan judul yang spesifik untuk KAK ini" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small">KODE PROYEK</label>
                                    <input type="text" name="kode_proyek" class="form-control" placeholder="Contoh: PRJ-2025-01">
                                </div>
                            </div>

                            <div class="section-title">II. Gambaran Umum</div>
                            <div class="mb-4">
                                <label class="form-label small">LATAR BELAKANG (WHY) <span class="text-danger">*</span></label>
                                <textarea name="latar_belakang" class="form-control" rows="4" placeholder="Jelaskan dasar pemikiran dan urgensi kegiatan ini..." required></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small">DASAR HUKUM</label>
                                <textarea name="dasar_hukum" class="form-control" rows="3" placeholder="Sebutkan UU, Perda, atau SK yang mendasari..."></textarea>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small">MAKSUD & TUJUAN</label>
                                    <textarea name="maksud_tujuan" class="form-control" rows="3" placeholder="Apa yang ingin dicapai?"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small">SASARAN / OUTPUT</label>
                                    <textarea name="sasaran" class="form-control" rows="3" placeholder="Hasil akhir yang diharapkan?"></textarea>
                                </div>
                            </div>

                            <div class="section-title">III. Rencana Pelaksanaan</div>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small">METODE PELAKSANAAN</label>
                                    <select name="metode_pelaksanaan" class="form-select">
                                        <option value="Swakelola">Swakelola</option>
                                        <option value="Penyedia">Penyedia (E-Katalog/Tender)</option>
                                        <option value="Hibah">Hibah / Bantuan</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small">LOKASI KEGIATAN</label>
                                    <input type="text" name="lokasi" class="form-control" placeholder="Pontianak / Seluruh Kalbar">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small">PENERIMA MANFAAT</label>
                                    <input type="text" name="penerima_manfaat" class="form-control" placeholder="Masyarakat / ASN / OPD">
                                </div>
                            </div>

                            <div class="section-title">IV. Tim Pelaksana</div>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered align-middle" id="tableTim">
                                    <thead class="bg-light small fw-bold">
                                        <tr>
                                            <th>NAMA PERSONIL</th>
                                            <th>NIP (OPSIONAL)</th>
                                            <th>PERAN DALAM TIM</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="nama_personil[]" class="form-control form-control-sm" placeholder="Nama Lengkap"></td>
                                            <td><input type="text" name="nip[]" class="form-control form-control-sm" placeholder="19XXXXXXXXXXXX"></td>
                                            <td><input type="text" name="peran_dalam_tim[]" class="form-control form-control-sm" placeholder="Ketua / Anggota"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 removeRow"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border fw-bold text-primary mb-5" id="addRow">
                                <i class="bi bi-plus-circle me-1"></i> TAMBAH PERSONIL
                            </button>

                            <div class="d-flex justify-content-between border-top pt-4">
                                <a href="{{ route('kak.index') }}" class="btn btn-light px-4 fw-bold text-muted small">BATAL</a>
                                <button type="submit" class="btn btn-primary px-5 fw-bold shadow">
                                    <i class="bi bi-check2-circle me-1"></i> SIMPAN DOKUMEN KAK
                                </button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fungsi Tambah Baris
            $('#addRow').click(function() {
                let html = `<tr>
                    <td><input type="text" name="nama_personil[]" class="form-control form-control-sm" placeholder="Nama Lengkap"></td>
                    <td><input type="text" name="nip[]" class="form-control form-control-sm" placeholder="19XXXXXXXXXXXX"></td>
                    <td><input type="text" name="peran_dalam_tim[]" class="form-control form-control-sm" placeholder="Ketua / Anggota"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 removeRow"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
                $('#tableTim tbody').append(html);
            });

            // Fungsi Hapus Baris
            $(document).on('click', '.removeRow', function() {
                // Jangan hapus jika baris tinggal satu
                if ($('#tableTim tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    alert("Minimal harus ada satu personil pelaksana.");
                }
            });
        });
$(document).ready(function() {
    const idPohon = "{{ $subKegiatan->id }}"; // Diambil dari variabel Controller

    function autoFillAILogic() {
        // Berikan feedback visual bahwa AI sedang bekerja
        $('textarea').addClass('bg-light').val('Sedang memproses narasi otomatis...');

        fetch(`/kinerja/api/sub-detail/${idPohon}`) // Mengambil data dari Service Kinerja
            .then(response => response.json())
            .then(data => {
                $('textarea').removeClass('bg-light');

                // 1. AUTO-FILL JUDUL
                $('input[name="judul_kak"]').val(`Penyelenggaraan ${data.nama_kinerja} Tahun 2025`);

                // 2. AI GENERATED LATAR BELAKANG
                let narasiLatar = `Sehubungan dengan pelaksanaan program strategis di lingkungan Pemerintah Provinsi Kalimantan Barat, kegiatan "${data.nama_kinerja}" menjadi prioritas untuk dilaksanakan. \n\n`;
                narasiLatar += `Hal ini didasarkan pada kebutuhan untuk mencapai target kinerja berikut:\n`;
                
                data.indikators.forEach(ind => {
                    narasiLatar += `- Tercapainya ${ind.indikator} dengan target ${ind.target} ${ind.satuan}.\n`;
                });
                
                narasiLatar += `\nTanpa adanya kegiatan ini, dikhawatirkan pemenuhan standar pelayanan pada unit kerja akan terhambat.`;
                $('textarea[name="latar_belakang"]').val(narasiLatar);

                // 3. AI GENERATED MAKSUD & TUJUAN
                let narasiMaksud = `Maksud dari kegiatan ini adalah untuk mengoptimalisasi ${data.nama_kinerja}. \n\n`;
                narasiMaksud += `Tujuannya adalah menyediakan kerangka teknis yang terukur bagi ${data.penanggung_jawab ?? 'unit kerja'} dalam mengelola sumber daya secara efektif.`;
                $('textarea[name="maksud_tujuan"]').val(narasiMaksud);

                // 4. AI GENERATED SASARAN
                let narasiSasaran = `Tersedianya output berupa laporan dan dokumentasi pelaksanaan ${data.nama_kinerja} yang akuntabel sesuai dengan indikator "${data.indikators[0]?.indikator ?? 'kinerja'}".`;
                $('textarea[name="sasaran"]').val(narasiSasaran);

                // 5. DEFAULT VALUE LAINNYA
                $('input[name="lokasi"]').val('Pontianak, Kalimantan Barat');
                $('input[name="penerima_manfaat"]').val('ASN dan Masyarakat Umum');
            })
            .catch(error => console.error('Gagal menjalankan AI Auto-fill:', error));
    }

    // Jalankan otomatis saat halaman terbuka
    autoFillAILogic();
});

    </script>
</body>
</html>