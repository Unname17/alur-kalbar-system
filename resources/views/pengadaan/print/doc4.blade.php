<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Doc 4 - Spesifikasi Teknis E-Purchasing</title>
    <style>
        @page { margin: 1.2cm; }
        body { font-family: 'Arial', sans-serif; font-size: 10px; line-height: 1.4; color: #000; }
        .header { text-align: center; font-weight: bold; font-size: 12px; text-transform: uppercase; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; table-layout: fixed; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; padding: 6px; border: 1px solid #000; }
        td { border: 1px solid #000; padding: 6px; vertical-align: top; word-wrap: break-word; }
        
        .no-border td { border: none !important; padding: 1px 4px; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 8px; text-decoration: underline; text-transform: uppercase; font-size: 11px; }
        .item-card { border: 1px solid #000; padding: 10px; margin-bottom: 15px; page-break-inside: avoid; }
        
        .footer-table { margin-top: 30px; border: none; }
        .footer-table td { border: none; text-align: center; width: 50%; padding-left: 50%; }
        .sig-space { height: 60px; }
        .check-box { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
    </style>
</head>
<body>

    <div class="header">
        DOKUMEN SPESIFIKASI TEKNIS<br>
        PENGADAAN BARANG/JASA MELALUI E-PURCHASING
    </div>

    {{-- IDENTITAS PAKET --}}
    <table class="no-border">
        <tr><td width="30%">PAKET PENGADAAN</td><td width="2%">:</td><td style="font-weight: bold;">{{ $package->nama_paket }}</td></tr>
        <tr><td>PPK</td><td>:</td><td>{{ $package->nama_pa_kpa ?? '....................' }}</td></tr>
        <tr><td>ID RUP / RKA</td><td>:</td><td>{{ $package->rka_main_id ?? '....................' }}</td></tr>
    </table>

    {{-- 1. DAFTAR KUANTITAS --}}
    <div class="section-title">1. SPESIFIKASI JUMLAH / DAFTAR KUANTITAS</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="55%">Item Barang/Jasa</th>
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
    <p style="font-size: 8px; font-style: italic; margin-top: -8px;">* Harga perolehan sudah memperhitungkan komponen perpajakan, asuransi, dan biaya pengiriman ke lokasi.</p>

    {{-- 2. SPESIFIKASI MUTU & TEKNIS PER ITEM --}}
    <div class="section-title">2. SPESIFIKASI MUTU/KUALITAS DAN TEKNIS</div>
    
    @foreach($package->items as $item)
    <div class="item-card">
        <div style="font-weight: bold; background: #eee; padding: 4px; border-bottom: 1px solid #000; margin: -10px -10px 10px -10px;">
            ITEM {{ $loop->iteration }}: {{ $item->nama_item }}
        </div>
        <table class="no-border">
            <tr><td width="30%">Merek / Tipe</td><td width="2%">:</td><td>{{ $item->merk_tipe ?? '-' }}</td></tr>
            <tr><td>Masa Garansi</td><td>:</td><td>{{ $item->masa_garansi ?? '-' }}</td></tr>
            <tr><td>Standar Mutu (SNI/TKDN)</td><td>:</td><td>{{ $item->standar_mutu ?? '-' }}</td></tr>
            <tr>
                <td>Link Katalog</td>
                <td>:</td>
                {{-- FIX: Mengambil link unik per item barang --}}
                <td style="color: blue; font-size: 8px;">{{ $item->link_produk_katalog ?? '-' }}</td>
            </tr>
        </table>
        
        <div style="margin-top: 5px; font-weight: bold;">Spesifikasi Teknis Detail:</div>
        <div style="padding: 5px; border-top: 1px dashed #ccc; font-size: 9px; white-space: pre-line;">
            {{ $item->deskripsi_spesifikasi ?? 'Spesifikasi detail belum diidentifikasi.' }}
        </div>
    </div>
    @endforeach

    {{-- 3. SPESIFIKASI WAKTU --}}
    <div class="section-title">3. SPESIFIKASI WAKTU</div>
    <table>
        <tr><th width="35%">Kriteria Pelaksanaan</th><th>Uraian Deskripsi</th></tr>
        <tr>
            <td>Waktu Pelaksanaan Pekerjaan</td>
            <td>Pekerjaan harus diselesaikan dalam waktu <strong>{{ $package->jadwal_pelaksanaan ?? '........' }}</strong>.</td>
        </tr>
        <tr>
            <td>Kebutuhan Waktu Pelayanan</td>
            <td>Penyedia wajib memberikan dukungan teknis selama jam kerja (08.00 - 16.00 WIB).</td>
        </tr>
        <tr>
            <td>Lokasi Kedatangan Barang</td>
            <td>{{ $package->lokasi_pekerjaan ?? 'Dinas Komunikasi dan Informatika Prov. Kalbar' }}</td>
        </tr>
        <tr>
            <td>Metode Transportasi & Pengepakan</td>
            <td>Menggunakan jasa kurir/logistik terpercaya dengan pengepakan standar anti-guncangan/bubble wrap untuk menjamin keamanan barang sampai tujuan.</td>
        </tr>
    </table>

    {{-- 4. PRIORITAS PRODUK DALAM NEGERI --}}
    <div class="section-title">4. PRIORITAS PRODUK DALAM NEGERI</div>
    <div style="margin-left: 10px;">
        @php
            $opsi = $package->opsi_pdn ?? 1;
            $labels = [
                1 => 'Barang/jasa memiliki TKDN dan BMP di atas 40%',
                2 => 'Barang/jasa memiliki TKDN kurang dari 25%',
                3 => 'Barang/jasa Produk Dalam Negeri non-TKDN',
                4 => 'Barang Impor'
            ];
        @endphp
        @foreach($labels as $key => $label)
            <div style="margin-bottom: 2px;">
                <span class="check-box">{{ $opsi == $key ? '[X]' : '[ ]' }}</span> 
                {{ $key }}. {{ $label }}
            </div>
        @endforeach
    </div>

    {{-- 5. SPESIFIKASI PELAYANAN --}}
    <div class="section-title">5. SPESIFIKASI PELAYANAN</div>
    <div style="margin-left: 10px; text-align: justify;">
        Penyedia diwajibkan memberikan layanan purna jual yang mencakup:
        <ul style="margin-top: 5px;">
            <li><strong>Dukungan Teknis:</strong> Menyediakan layanan bantuan teknis respons cepat maksimal 1x24 jam melalui telepon, email, atau kunjungan lapangan jika diperlukan.</li>
            <li><strong>Pemeliharaan:</strong> Memberikan panduan pemeliharaan rutin dan menjamin ketersediaan suku cadang asli selama masa garansi berlangsung.</li>
            <li><strong>Garansi:</strong> Jaminan perbaikan atau penggantian perangkat jika terjadi kerusakan fungsional yang bukan diakibatkan oleh kelalaian pengguna (force majeure).</li>
            <li><strong>Pelatihan:</strong> Jika diperlukan, penyedia memberikan penjelasan/pelatihan singkat tata cara penggunaan perangkat kepada staf teknis pengguna.</li>
        </ul>
    </div>

    {{-- TANDA TANGAN --}}
    <table class="footer-table">
        <tr>
            <td>
                Pontianak, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Pejabat Pembuat Komitmen
                <div class="sig-space"></div>
                <strong>{{ $package->nama_pa_kpa ?? '................................' }}</strong><br>
                NIP. {{ $package->nip_pa_kpa ?? '................................' }}
            </td>
        </tr>
    </table>

</body>
</html>