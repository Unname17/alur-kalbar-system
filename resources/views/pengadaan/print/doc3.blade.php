<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Doc 3 - Kertas Kerja Induk Analisis Persiapan</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Arial', sans-serif; font-size: 10px; line-height: 1.3; color: #000; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .title { text-align: center; font-weight: bold; font-size: 12px; margin-bottom: 15px; text-transform: uppercase; line-height: 1.5; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; table-layout: fixed; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; padding: 5px; border: 1px solid #000; word-wrap: break-word; }
        td { border: 1px solid #000; padding: 5px; vertical-align: top; word-wrap: break-word; }
        
        .no-border td { border: none !important; padding: 1px 4px; }
        .section-title { font-weight: bold; margin-top: 10px; margin-bottom: 5px; background: #eee; padding: 3px 5px; border: 1px solid #000; text-transform: uppercase; }
        .sub-section { font-weight: bold; margin: 8px 0 4px 0; padding-left: 2px; }
        
        .check-box { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .footer-table { margin-top: 20px; border: none; }
        .footer-table td { border: none; text-align: center; width: 50%; padding-top: 10px; }
        .sig-space { height: 60px; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="header">
        <div style="font-size: 12px; font-weight: bold;">PEMERINTAH PROVINSI KALIMANTAN BARAT</div>
        <div style="font-size: 14px; font-weight: bold;">DINAS KOMUNIKASI DAN INFORMATIKA</div>
        <div style="font-size: 9px;">Jalan Jenderal Ahmad Yani, Pontianak, Kalimantan Barat</div>
    </div>

    {{-- JUDUL DOKUMEN --}}
    <div class="title">
        KERTAS KERJA INDUK<br>
        ANALISIS PERSIAPAN PENGADAAN BARANG/JASA MELALUI E-PURCHASING<br>
        <span style="font-size: 9px; font-weight: normal;">Nomor: {{ $analysisData['calon']->nomor_analisis ?? '..../ANALISIS-EP/2026' }}</span>
    </div>

    {{-- BAGIAN A --}}
    <div class="section-title">A. IDENTITAS PAKET PENGADAAN</div>
    <table class="no-border">
        <tr><td width="30%">Nama Paket Pengadaan</td><td width="2%">:</td><td>{{ $package->nama_paket }}</td></tr>
        <tr><td>Unit Kerja/OPD Pengusul</td><td>:</td><td>Dinas Komunikasi dan Informatika Prov. Kalbar</td></tr>
        <tr><td>Nilai Pagu Anggaran (Rp)</td><td>:</td><td>Rp {{ number_format($package->pagu_paket, 0, ',', '.') }} [cite: 40]</td></tr>
        <tr><td>Nama Pejabat Pengadaan</td><td>:</td><td>{{ $package->nama_pa_kpa ?? '....................' }} [cite: 41]</td></tr>
        <tr><td>Tanggal Analisis</td><td>:</td><td>{{ $analysisData['calon']->updated_at ? \Carbon\Carbon::parse($analysisData['calon']->updated_at)->format('d F Y') : '....................' }} [cite: 44]</td></tr>
    </table>

    <div class="sub-section">A.1. IDENTITAS CALON PENYEDIA DAN PRODUK DI KATALOG ELEKTRONIK</div>
    <table>
        <tr><th width="30%">Uraian</th><th>Keterangan</th></tr>
        <tr><td>Nama Penyedia</td><td>{{ $analysisData['calon']->nama_calon_penyedia ?? '-' }} [cite: 47]</td></tr>
        <tr><td>Nama Produk Katalog</td><td>{{ $analysisData['calon']->produk_katalog ?? '-' }} [cite: 47]</td></tr>
        <tr><td>Harga Satuan Tayang (Rp)</td><td>Rp {{ number_format($analysisData['calon']->harga_tayang_katalog ?? 0, 0, ',', '.') }} [cite: 47]</td></tr>
        <tr><td>ID Produk Katalog</td><td>{{ $analysisData['calon']->produk_katalog ?? '-' }} [cite: 47]</td></tr>
        <tr><td>Link Produk Katalog</td><td><span style="font-size: 8px; color: blue;">{{ $analysisData['calon']->link_produk_katalog ?? '-' }}</span></td></tr>
    </table>

    {{-- BAGIAN B --}}
    <div class="section-title">B. ANALISIS PERSIAPAN PENGADAAN</div>

    <div class="sub-section">B.1. Evaluasi Spesifikasi Teknis</div>
    <table>
        <tr><th width="5%">No</th><th width="40%">Poin Evaluasi</th><th>Hasil Reviu & Uraian [cite: 103-104]</th></tr>
        @php
            $labelsTeknis = [
                '1.1' => 'Kelengkapan Uraian Spesifikasi',
                '1.2' => 'Kesesuaian dengan Kebutuhan',
                '1.3' => 'Karakteristik Produk (ukuran, bahan, dll)',
                '1.4' => 'Kinerja Produk (ketahanan, efisiensi, dll)',
                '1.5' => 'Standar yang Digunakan (SNI/ISO)',
                '1.6' => 'Jumlah/Kuantitas'
            ];
        @endphp
        @foreach($labelsTeknis as $key => $label)
        <tr>
            <td align="center">{{ $key }}</td>
            <td>{{ $label }}</td>
            <td>
                @php $val = $analysisData['teknis'][$key] ?? null; @endphp
                <span class="check-box">[{{ ($val['status'] ?? '') == 'Sesuai' ? 'X' : ' ' }}]</span> Sesuai / 
                <span class="check-box">[{{ ($val['status'] ?? '') == 'Tidak' ? 'X' : ' ' }}]</span> Tidak Sesuai
                <br>Uraian: {{ $val['catatan'] ?? '-' }}
            </td>
        </tr>
        @endforeach
    </table>

    <div class="sub-section">B.2. Evaluasi Referensi Harga</div>
    <table>
        <tr><th width="5%">No</th><th width="40%">Poin Evaluasi</th><th>Hasil Reviu & Uraian [cite: 107]</th></tr>
        @php
            $labelsHarga = [
                '2.1' => 'Ketersediaan Referensi Harga',
                '2.2' => 'Perbandingan Harga (pasar/kontrak sejenis)',
                '2.3' => 'Kewajaran Harga Perkiraan'
            ];
        @endphp
        @foreach($labelsHarga as $key => $label)
        <tr>
            <td align="center">{{ $key }}</td>
            <td>{{ $label }}</td>
            <td>
                @php $val = $analysisData['harga'][$key] ?? null; @endphp
                <span class="check-box">[{{ ($val['status'] ?? '') == 'Cukup' ? 'X' : ' ' }}]</span> Wajar / 
                <span class="check-box">[{{ ($val['status'] ?? '') == 'Kurang' ? 'X' : ' ' }}]</span> Tidak Wajar
                <br>Uraian: {{ $val['catatan'] ?? '-' }}
            </td>
        </tr>
        @endforeach
    </table>

    {{-- BAGIAN TAMBAHAN B.3 & B.4 --}}
    <div class="sub-section">B.3. Evaluasi Rancangan Kontrak (Surat Pesanan)</div>
    <table>
        <tr><th width="5%">No</th><th width="40%">Poin Evaluasi</th><th>Status Kelengkapan & Uraian [cite: 110-111]</th></tr>
        @php
            $labelsKontrak = [
                '3.1' => 'Kelengkapan Pokok Kontrak (Nama, Dana, Nilai)',
                '3.2' => 'Syarat-Syarat Umum & Khusus Kontrak',
                '3.3' => 'Kesesuaian Jenis Kontrak'
            ];
        @endphp
        @foreach($labelsKontrak as $key => $label)
        <tr>
            <td align="center">{{ $key }}</td>
            <td>{{ $label }}</td>
            <td>
                @php $val = $analysisData['kontrak'][$key] ?? null; @endphp
                <span class="check-box">[{{ ($val['status'] ?? '') == 'Sesuai' ? 'X' : ' ' }}]</span> Lengkap / 
                <span class="check-box">[{{ ($val['status'] ?? '') == 'Tidak' ? 'X' : ' ' }}]</span> Belum Lengkap
                <br>Uraian: {{ $val['catatan'] ?? '-' }}
            </td>
        </tr>
        @endforeach
    </table>

    <div class="sub-section">B.4. Evaluasi Ketersediaan Produk pada Katalog Elektronik</div>
    <table>
        <tr><th width="5%">No</th><th width="40%">Poin Evaluasi</th><th>Hasil Reviu & Uraian [cite: 114]</th></tr>
        @php
            $labelsKatalog = [
                '4.1' => 'Ketersediaan Produk di Katalog',
                '4.2' => 'Jumlah Penyedia yang Menawarkan Sejenis'
            ];
        @endphp
        @foreach($labelsKatalog as $key => $label)
        <tr>
            <td align="center">{{ $key }}</td>
            <td>{{ $label }}</td>
            <td>
                @php $val = $analysisData['katalog'][$key] ?? null; @endphp
                <span class="check-box">[{{ ($val['status'] ?? '') == 'Sesuai' ? 'X' : ' ' }}]</span> Tersedia / 
                <span class="check-box">[{{ ($val['status'] ?? '') == 'Tidak' ? 'X' : ' ' }}]</span> Tidak Tersedia
                <br>Uraian: {{ $val['catatan'] ?? '-' }}
            </td>
        </tr>
        @endforeach
    </table>

    {{-- PENGESAHAN --}}
    <div class="section-title">C. PERSETUJUAN DAN PENGESAHAN</div>
    <p style="margin-bottom: 15px;">Dengan ini, Kertas Kerja Induk Analisis Persiapan Pengadaan melalui E-Purchasing telah disusun untuk dipergunakan pada tahap selanjutnya. [cite: 116]</p>

    <table class="footer-table">
        <tr>
            <td>Dianalisis & Disiapkan oleh:<br>Pejabat Pengadaan [cite: 117]</td>
            <td>Disetujui oleh:<br>Pejabat Pembuat Komitmen (PPK) [cite: 117]</td>
        </tr>
        <tr>
            <td class="sig-space"></td>
            <td class="sig-space"></td>
        </tr>
        <tr>
            <td>( {{ $package->nama_pejabat_pengadaan ?? '................................' }} )<br>NIP. {{ $package->nip_pejabat_pengadaan ?? '................................' }}</td>
            <td>( {{ $package->nama_pa_kpa ?? '................................' }} ) [cite: 92]<br>NIP. {{ $package->nip_pa_kpa ?? '................................' }} [cite: 92]</td>
        </tr>
    </table>

</body>
</html>