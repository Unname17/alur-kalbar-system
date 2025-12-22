<!DOCTYPE html>
<html>
<head>
    <title>Spesifikasi Teknis - {{ $pengadaan->id }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 1cm; }
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h2, .kop-surat h3 { margin: 0; text-transform: uppercase; }
        .judul-dokumen { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 30px; }
        .isi-dokumen { margin-bottom: 15px; }
        .label { font-weight: bold; width: 200px; display: inline-block; vertical-align: top; }
        .titik-dua { display: inline-block; width: 10px; vertical-align: top; }
        .nilai { display: inline-block; width: 450px; vertical-align: top; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid black; padding: 8px; }
        .tanda-tangan { margin-top: 50px; float: right; width: 250px; text-align: center; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h3>PEMERINTAH PROVINSI KALIMANTAN BARAT</h3>
        <h2>DINAS KOMUNIKASI DAN INFORMATIKA</h2>
        <p style="font-size: 10pt; margin: 5px 0;">Jl. Ahmad Yani No. 1, Pontianak, Kalimantan Barat</p>
    </div>

    <div class="judul-dokumen">
        SPESIFIKASI TEKNIS PEKERJAAN
    </div>

    <div class="isi-dokumen">
        <div class="label">Nama Paket Pekerjaan</div>
        <div class="titik-dua">:</div>
        <div class="nilai">{{ $pengadaan->rkaPerencanaan->judul_kak ?? 'N/A' }}</div>
    </div>

    <div class="isi-dokumen">
        <div class="label">Nomor RKA/DPA</div>
        <div class="titik-dua">:</div>
        <div class="nilai">{{ $pengadaan->rka->nomor_rka ?? '-' }}</div>
    </div>

    <div class="isi-dokumen">
        <div class="label">Metode Pengadaan</div>
        <div class="titik-dua">:</div>
        <div class="nilai text-uppercase">{{ $pengadaan->metode_pengadaan }}</div>
    </div>

    <div class="isi-dokumen" style="margin-top: 20px;">
        <strong>A. Latar Belakang / Tujuan Pekerjaan</strong>
        <p>{{ $pengadaan->rkaPerencanaan->latar_belakang ?? 'Tujuan pekerjaan ini adalah untuk menunjang capaian kinerja organisasi sesuai rencana aksi.' }}</p>
    </div>

    <div class="isi-dokumen">
        <strong>B. Spesifikasi Barang/Jasa dan Volume</strong>
        <table>
            <thead>
                <tr style="background-color: #eee;">
                    <th>No</th>
                    <th>Uraian Spesifikasi</th>
                    <th>Volume Target</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
    {{-- Tambahkan pengecekan null dengan ?? [] --}}
    @forelse($pengadaan->rka->details ?? [] as $index => $item)
    <tr>
        <td style="text-align: center;">{{ $index + 1 }}</td>
        <td>{{ $item->nama_barang ?? $item->uraian }}</td> {{-- Sesuaikan nama kolomnya --}}
        <td style="text-align: center;">{{ $item->volume }}</td>
        <td style="text-align: center;">{{ $item->satuan }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="4" style="text-align: center;">Data rincian belanja tidak ditemukan di RKA ini.</td>
    </tr>
    @endforelse
</tbody>
        </table>
        <p style="font-size: 10pt; font-style: italic; margin-top: 5px;">* Data ditarik otomatis dari sistem integrasi KAK-RKA-Pengadaan.</p>
    </div>

    <div class="tanda-tangan">
        <p>Pontianak, {{ date('d F Y') }}</p>
        <p>Pejabat Pembuat Komitmen (PPK)</p>
        <br><br><br>
        <p><strong>(..........................................)</strong><br>NIP. ..........................................</p>
    </div>

</body>
</html>