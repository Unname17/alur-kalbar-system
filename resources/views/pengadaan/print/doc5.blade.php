<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Doc 5 - Spesifikasi Teknis Umum</title>
    <style>
        @page { margin: 1.2cm; }
        body { font-family: 'Arial', sans-serif; font-size: 10px; line-height: 1.5; color: #000; }
        .header { text-align: center; font-weight: bold; font-size: 12px; text-transform: uppercase; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; padding: 8px; border: 1px solid #000; text-transform: uppercase; }
        td { border: 1px solid #000; padding: 8px; vertical-align: top; word-wrap: break-word; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 8px; font-size: 11px; text-transform: uppercase; }
        .footer-table { margin-top: 40px; border: none; }
        .footer-table td { border: none; text-align: center; width: 50%; padding-left: 50%; }
        .sig-space { height: 70px; }
        .check-box { font-family: DejaVu Sans, sans-serif; }
        .hint { font-size: 9px; font-style: italic; color: #444; margin-bottom: 10px; display: block; }
    </style>
</head>
<body>

    <div class="header">
        SPESIFIKASI TEKNIS JENIS PENGADAAN BARANG DAN JASA LAINNYA
    </div>

    <div class="section-title">Bagian 1: Identitas Paket Pengadaan [cite: 335]</div>
    <table>
        <tr><th width="35%">Kriteria</th><th>Deskripsi</th></tr>
        <tr><td>Nama Paket Pengadaan</td><td style="font-weight: bold;">{{ $package->nama_paket }}</td></tr>
        <tr><td>PPK / Satuan Kerja</td><td>{{ $package->nama_pa_kpa ?? '-' }} / Dinas Kominfo Prov. Kalbar</td></tr>
    </table>

    <div class="section-title">Bagian 2: Spesifikasi Mutu/Kinerja [cite: 337]</div>
    <table>
        <thead>
            <tr>
                <th width="30%">Jenis Garansi/KPI</th>
                <th width="40%">Deskripsi</th>
                <th width="30%">Target Angka Kinerja Minimal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($package->items as $item)
            <tr>
                <td>Jaminan Purna Jual {{ $item->nama_item }}</td>
                <td>Dukungan teknis dan ketersediaan komponen pengganti.</td>
                <td align="center">{{ $item->masa_garansi ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Bagian 3: Spesifikasi Teknis [cite: 339]</div>
    <span class="hint">*) Tabel ini digunakan untuk PPK dalam menentukan spesifikasi teknis minimal [cite: 342]</span>
    <table>
        <thead>
            <tr>
                <th width="30%">Parameter Teknis</th>
                <th width="40%">Deskripsi</th>
                <th width="30%">Angka Minimal </th>
            </tr>
        </thead>
        <tbody>
            @foreach($package->items as $item)
            <tr>
                <td>Fungsi dan Kinerja {{ $item->nama_item }}</td>
                <td>{{ $item->fungsi_kinerja ?? '-' }}</td>
                <td align="center">Sesuai Kebutuhan</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Spesifikasi Teknis Penawaran [cite: 343]</div>
    <span class="hint">*) Tabel ini digunakan untuk pelaku usaha yang menyampaikan penawaran [cite: 345]</span>
    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="30%">Parameter Teknis Dibutuhkan [cite: 344]</th>
                <th width="30%">Parameter Ditawarkan</th>
                <th width="20%">Merek/Tipe</th>
                <th width="15%">Brosur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($package->items as $item)
            <tr>
                <td align="center">{{ $loop->iteration }}</td>
                <td>Fungsi & Kinerja {{ $item->nama_item }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Bagian 4: Spesifikasi Jumlah [cite: 346]</div>
    <table>
        <thead>
            <tr>
                <th width="10%">No.</th>
                <th width="50%">Nama Barang/Jasa</th>
                <th width="20%">Volume</th>
                <th width="20%">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($package->items as $item)
            <tr>
                <td align="center">{{ $loop->iteration }}</td>
                <td>{{ $item->nama_item }}</td>
                <td align="center">{{ number_format($item->volume, 0, ',', '.') }}</td>
                <td align="center">{{ $item->satuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="font-size: 8px; font-style: italic; margin-top: -10px;">
        Harga sudah memperhitungkan komponen Perpajakan, Asuransi, dan Ongkos Kirim. [cite: 348-351]
    </p>

    <div class="section-title">Bagian 5: Spesifikasi Waktu [cite: 353]</div>
    <table>
        <tr><th width="35%">Kriteria</th><th>Deskripsi</th></tr>
        <tr><td>Waktu Pelaksanaan Pekerjaan</td><td>{{ $package->jadwal_pelaksanaan ?? '-' }}</td></tr>
        <tr><td>Kebutuhan Waktu Pelayanan</td><td>Dukungan teknis respons cepat maksimal 1x24 jam selama hari kerja. [cite: 354]</td></tr>
        <tr><td>Lokasi Kedatangan Barang</td><td>{{ $package->lokasi_pekerjaan ?? '-' }}</td></tr>
        <tr><td>Metode Pengepakan</td><td>Pengepakan aman menggunakan bahan anti-guncangan. [cite: 354]</td></tr>
    </table>

    <div class="section-title">Bagian 6: Spesifikasi Pelayanan [cite: 355]</div>
    <table>
        <tr><th width="35%">Kriteria</th><th>Deskripsi</th></tr>
        <tr><td>Tingkat Pelayanan</td><td>Vendor bertanggung jawab atas instalasi dan operasional awal. [cite: 356]</td></tr>
        <tr><td>Kebutuhan Pelatihan</td><td>Pelatihan penggunaan bagi staf teknis minimal satu kali sesi. [cite: 356]</td></tr>
        <tr><td>Aspek Pemeliharaan</td><td>{{ $package->items->first()->aspek_pemeliharaan ?? '-' }}</td></tr>
    </table>

    <div class="section-title">Bagian 7: Penetapan Prioritas PDN [cite: 357]</div>
    <table>
        <thead><tr><th width="15%">Prioritas</th><th>Deskripsi Kategori [cite: 359]</th></tr></thead>
        <tbody>
            @php
                $opsi = $package->opsi_pdn ?? 1;
                $labels = [
                    1 => 'Barang/jasa memiliki TKDN dan BMP di atas 40%',
                    2 => 'Barang/jasa memiliki TKDN kurang dari 25%',
                    3 => 'Barang/jasa Produk Dalam Negeri non-TKDN',
                    4 => 'Barang impor'
                ];
            @endphp
            @foreach($labels as $key => $label)
            <tr>
                <td align="center" class="check-box">{{ $opsi == $key ? '[X]' : '[ ]' }}</td>
                <td>{{ $label }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td>
                Ditetapkan di Pontianak, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Pejabat Pembuat Komitmen [cite: 361]
                <div class="sig-space"></div>
                <strong>{{ $package->nama_pa_kpa ?? '................................' }}</strong><br>
                NIP. {{ $package->nip_pa_kpa ?? '................................' }}
            </td>
        </tr>
    </table>
</body>
</html>