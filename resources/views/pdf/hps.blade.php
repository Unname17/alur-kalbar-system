<!DOCTYPE html>
<html>
<head>
    <title>HPS - {{ $pengadaan->id }}</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; margin: 1cm; }
        .kop { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 50px; float: right; width: 250px; text-align: center; }
    </style>
</head>
<body>
    <div class="kop">
        <h3>PEMERINTAH PROVINSI KALIMANTAN BARAT</h3>
        <h2>DINAS KOMUNIKASI DAN INFORMATIKA</h2>
    </div>

    <div class="judul">HARGA PERKIRAAN SENDIRI (HPS)</div>

    <p><strong>Nama Paket:</strong> {{ $pengadaan->rkaPerencanaan->judul_kak ?? 'N/A' }}</p>
    <p><strong>Nomor RKA:</strong> {{ $pengadaan->rka->nomor_rka ?? '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Deskripsi Barang/Jasa</th>
                <th>Volume</th>
                <th>Satuan</th>
                <th>Harga Satuan (Rp)</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $total = $pengadaan->rka->total_anggaran ?? 0;
                $target = $pengadaan->target_volume ?: 1;
                $hargaSatuan = $total / $target;
            @endphp
            <tr>
                <td style="text-align: center;">1</td>
                <td>{{ $pengadaan->rkaPerencanaan->judul_kak }}</td>
                <td style="text-align: center;">{{ $pengadaan->target_volume }}</td>
                <td style="text-align: center;">Unit</td>
                <td class="text-right">{{ number_format($hargaSatuan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="5" style="text-align: center;">JUMLAH TOTAL</td>
                <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Pontianak, {{ date('d F Y') }}</p>
        <p>Pejabat Pembuat Komitmen (PPK)</p>
        <br><br><br>
        <p><strong>(..........................................)</strong></p>
    </div>
</body>
</html>