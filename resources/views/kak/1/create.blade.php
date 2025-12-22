<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Susun KAK - {{ $subKegiatan->nama_kinerja }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .section-title { border-left: 5px solid #1a237e; padding-left: 15px; margin-bottom: 25px; font-weight: 700; color: #1a237e; }
    </style>
</head>
<body>
    <div class="container py-5">
        <form action="{{ route('kak.store') }}" method="POST">
            @csrf
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-center">FORMULIR PENYUSUNAN KAK</h5>
                </div>
                <div class="card-body p-4">
                    @include('kak.partials.form-identitas')
                    @include('kak.partials.form-gambaran')
                    @include('kak.partials.form-tim')

                    <div class="d-flex justify-content-between pt-4 border-top">
                        <a href="{{ route('kak.index') }}" class="btn btn-light">BATAL</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold">SIMPAN & AJUKAN</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // FUNGSI API: Narik data otomatis dari Modul Kinerja
            fetch(`/api/kinerja/sub-detail/{{ $subKegiatan->id }}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('judul_kak').value = "KAK: " + data.nama_kinerja;
                    let narasi = "Kegiatan ini direncanakan untuk mendukung indikator: \n";
                    data.indikators.forEach(ind => {
                        narasi += `- ${ind.indikator} (Target: ${ind.target} ${ind.satuan})\n`;
                    });
                    document.getElementById('latar_belakang').value = narasi;
                });

            // Handler Tambah/Hapus Personil Tim
            $('#addRow').click(function() {
                let html = `<tr>
                    <td><input type="text" name="nama_personil[]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="nip[]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="peran_dalam_tim[]" class="form-control form-control-sm"></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 removeRow"><i class="bi bi-trash"></i></button></td>
                </tr>`;
                $('#tableTim tbody').append(html);
            });
            $(document).on('click', '.removeRow', function() { $(this).closest('tr').remove(); });
        });
    </script>
</body>
</html>