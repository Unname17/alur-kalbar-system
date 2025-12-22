<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak RKA - {{ $rka->nomor_rka }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Cetak Sekarang</button>
        <a href="{{ route('rka.pilih_kak') }}">Kembali</a>
    </div>

    <div class="header">
        <h2>PEMERINTAH PROVINSI KALIMANTAN BARAT</h2>
        <h3>RINCIAN KEGIATAN ANGGARAN (RKA)</h3>
        <p>Nomor: {{ $rka->nomor_rka }}</p>
    </div>

    <table>
        <tr>
            <td><strong>Judul Kegiatan</strong></td>
            <td>: {{ $kak->judul_kak }}</td>
        </tr>
        <tr>
            <td><strong>Pagu Validasi</strong></td>
            <td>: Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr style="background: #eee;">
                <th>No</th>
                <th>Rincian Barang/Jasa</th>
                <th>Volume</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rincian as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td class="text-center">{{ $item->volume }}</td>
                <td>{{ $item->satuan }}</td>
                <td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->total_harga, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #eee;">
                <td colspan="5" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 50px; float: right; width: 300px; text-align: center;">
        <p>Pontianak, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>( ________________________ )</strong></p>
        <p>NIP. .................................</p>
    </div>
</body>
</html>