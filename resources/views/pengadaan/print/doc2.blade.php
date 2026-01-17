<!DOCTYPE html>
<html>
<head>
    <title>DOKUMEN 2 - JUSTIFIKASI STRATEGIS</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; font-weight: bold; margin-bottom: 20px; font-size: 14px; }
        .section { background: #f0f0f0; font-weight: bold; padding: 5px; margin: 15px 0 10px 0; border: 1px solid #000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table, th, td { border: 1px solid black; padding: 8px; }
        .no-border table, .no-border td { border: none !important; }
    </style>
</head>
<body>
    <div class="header">DOKUMEN 2: FORMULIR JUSTIFIKASI STRATEGIS<br>PEMILIHAN METODE E-PURCHASING</div>

    <div class="section">A. IDENTITAS PENGADAAN</div>
    <table>
        <tr><td width="30%">Nama Paket</td><td>{{ $package->nama_paket }}</td></tr>
        <tr><td>Unit Pengusul</td><td>Dinas Komunikasi dan Informatika</td></tr>
        <tr><td>Nilai Pagu Anggaran</td><td>Rp {{ number_format($package->pagu_paket, 0, ',', '.') }}</td></tr>
        <tr><td>Tanggal Analisis</td><td>{{ date('d F Y') }}</td></tr>
    </table>

    <div class="section">B. PENENTUAN JALUR STRATEGIS PENGADAAN</div>
    <p>Dipilih berdasarkan Urutan Prioritas:</p>
    <table>
        <tr>
            <td width="5%" style="text-align: center">{!! $package->preparation->jalur_prioritas == 1 ? '<b>[ X ]</b>' : '[  ]' !!}</td>
            <td><b>Prioritas 1:</b> Jalur Wajib Regulasi (Pekerjaan Konstruksi)</td>
        </tr>
        <tr>
            <td style="text-align: center">{!! $package->preparation->jalur_prioritas == 2 ? '<b>[ X ]</b>' : '[  ]' !!}</td>
            <td><b>Prioritas 2:</b> Jalur Utama (Pengadaan Jasa/Barang Kompleks/Strategis)</td>
        </tr>
        <tr>
            <td style="text-align: center">{!! $package->preparation->jalur_prioritas == 3 ? '<b>[ X ]</b>' : '[  ]' !!}</td>
            <td><b>Prioritas 3:</b> Jalur Pengecualian (Barang Komoditas Murni)</td>
        </tr>
    </table>

    <div class="section">D. KEPUTUSAN FINAL & RENCANA STRATEGIS</div>
    <table>
        <tr><td width="30%">Metode Final</td><td><b>{{ $package->preparation->jalur_strategis }}</b></td></tr>
        <tr><td>Justifikasi</td><td>{{ $package->preparation->justifikasi_pilihan }}</td></tr>
        <tr>
            <td>Target Strategis</td>
            <td>
                <ul>
                    @foreach(json_decode($package->preparation->target_strategis ?? '[]') as $target)
                        <li>{{ $target }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
    </table>

    <div style="margin-top: 50px;">
        <table style="border: none !important" class="no-border">
            <tr>
                <td width="50%" style="text-align: center">Dianalisis oleh,<br>Pejabat Pengadaan<br><br><br><br>( ............................... )</td>
                <td width="50%" style="text-align: center">Disetujui oleh,<br>Pejabat Pembuat Komitmen<br><br><br><br><b>{{ $package->nama_ppk ?? 'Kabid Aptika Kominfo' }}</b></td>
            </tr>
        </table>
    </div>
</body>
</html>