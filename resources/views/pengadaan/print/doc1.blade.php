<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dokumen 1 - Identifikasi Kebutuhan</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 8.5pt; color: #333; line-height: 1.4; }
        .header { text-align: center; font-weight: bold; font-size: 11pt; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; text-transform: uppercase; }
        .section-title { background-color: #f1f5f9; font-weight: bold; padding: 4px 8px; border: 1px solid #000; margin-top: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        .no-border { border: none !important; }
        .no-border td { border: none !important; padding: 2px 0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .checkbox { font-family: DejaVu Sans, sans-serif; font-size: 10pt; }
    </style>
</head>
<body>
    <div class="header">
        FORMULIR PERENCANAAN PENGADAAN<br>
        IDENTIFIKASI KEBUTUHAN BARANG/JASA
    </div>

    <div class="section-title">1. INFORMASI UMUM</div>
    <table class="no-border" style="margin-top: 5px;">
        <tr>
            <td width="25%"><strong>1.1 Status Dokumen</strong></td>
            <td>: Perubahan ke-{{ $package->perubahan_ke ?? 0 }} (Tgl: {{ $package->tanggal_perubahan ?? '-' }}) </td>
        </tr>
    </table>

    <table style="margin-top: 5px;">
        <tr>
            <td width="25%">Kementerian/Lembaga/PD</td>
            <td>Pemerintah Provinsi Kalimantan Barat / Dinas Komunikasi dan Informatika </td>
        </tr>
        <tr>
            <td>Nama Paket Pengadaan</td>
            <td><strong>{{ $package->nama_paket }}</strong> [cite: 67]</td>
        </tr>
    </table>

    <p style="font-weight: bold; margin-top: 8px; margin-bottom: 2px;">1.2 Identitas Organisasi (Konsolidasi)</p>
    <table>
        <tr style="background-color: #f8fafc;">
            <th width="5%">No</th>
            <th>Program / Kegiatan / Output [cite: 62-64]</th>
        </tr>
        @foreach($identitasOrganisasi as $org)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>
                <strong>Prog:</strong> {{ $org->program }}<br>
                <strong>Keg:</strong> {{ $org->kegiatan }}<br>
                <strong>Out:</strong> {{ $org->output }}
            </td>
        </tr>
        @endforeach
    </table>

    <div class="section-title">2. INFORMASI ANGGARAN DAN AKUN</div>
    <table>
        <tr>
            <td width="25%">Anggaran Pengadaan</td>
            <td><strong>Rp {{ number_format($package->pagu_paket, 0, ',', '.') }}</strong> [cite: 71]</td>
        </tr>
        <tr>
            <td>Sumber Dana / TA</td>
            <td>APBD / {{ date('Y') }} [cite: 72-73]</td>
        </tr>
    </table>

    <p style="font-weight: bold; margin-top: 8px; margin-bottom: 2px;">2.2 Rincian Akun [cite: 74-75]</p>
    <table>
        <thead>
            <tr style="background-color: #f8fafc;">
                <th width="5%">No</th>
                <th width="20%">Kode Akun</th>
                <th>Nama Akun</th>
                <th width="20%">Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accountDetails as $acc)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $acc['kode'] }}</td>
                <td>{{ $acc['nama'] }}</td>
                <td class="text-right">{{ number_format($acc['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="font-size: 8pt; margin-top: 2px;"><strong>Pertimbangan Penggunaan Akun:</strong> {{ $package->pertimbangan_akun ?? '-' }} [cite: 76]</p>

    <div class="section-title">3. SPESIFIKASI DAN PRIORITAS PENGADAAN</div>
    <p style="font-weight: bold; margin-top: 8px; margin-bottom: 2px;">3.1 Rincian Kebutuhan [cite: 78-79]</p>
    <table>
        <thead>
            <tr style="background-color: #f8fafc;">
                <th width="5%">No</th>
                <th>Item Barang/Jasa</th>
                <th>Spesifikasi</th>
                <th width="15%">Kuantitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($package->items as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->nama_item }}</td>
                <td>{{ $item->deskripsi_spesifikasi ?? 'Sesuai Standar Teknis' }}</td>
                <td class="text-center">{{ $item->volume }} {{ $item->satuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-weight: bold; margin-top: 8px; margin-bottom: 2px;">3.2 Prioritas Penggunaan Produk Dalam Negeri [cite: 80-89]</p>
    <table class="no-border">
        <tr>
            <td width="5%"><span class="checkbox">{{ ($package->opsi_pdn == 1) ? '☑' : '☐' }}</span></td>
            <td>1. Barang dengan TKDN dan BMP di atas 40%</td>
        </tr>
        <tr>
            <td><span class="checkbox">{{ ($package->opsi_pdn == 2) ? '☑' : '☐' }}</span></td>
            <td>2. Barang PDN dengan TKDN di bawah 25%</td>
        </tr>
        <tr>
            <td><span class="checkbox">{{ ($package->opsi_pdn == 3) ? '☑' : '☐' }}</span></td>
            <td>3. Barang PDN tanpa informasi TKDN</td>
        </tr>
        <tr>
            <td><span class="checkbox">{{ ($package->opsi_pdn == 4) ? '☑' : '☐' }}</span></td>
            <td>4. Barang Impor</td>
        </tr>
    </table>
    <p style="font-size: 8pt; margin-top: 2px;"><strong>Alasan Pemilihan:</strong> {{ $package->alasan_pdn ?? '-' }} [cite: 90]</p>

<div class="section-title">4. CARA PENGADAAN</div>
<table>
    <tr>
        <td width="35%">4.1 Swakelola</td>
        <td>Bukan merupakan paket swakelola</td>
    </tr>
    <tr>
        <td>4.2 Penyedia</td>
        <td>Ya, melalui pihak ketiga</td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">4.2.1 Metode Pemilihan</td>
        <td>{{ $package->metode_pemilihan }}</td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">4.2.2 Kriteria Pelaku Usaha</td>
        <td>{{ $package->is_umkm ? 'Usaha Mikro, Kecil, dan Koperasi' : 'Non-Kecil' }}</td>
    </tr>
    <tr>
        <td>4.3.3 Klasifikasi Komoditas</td>
        <td>Kode KBKI: {{ $package->kode_kbki }} <br> Deskripsi: {{ $package->deskripsi_kbki }}</td>
    </tr>
</table>

<div class="section-title">5. INFORMASI PELAKSANAAN</div>
<table>
    <tr>
        <td width="35%">5.1 Jadwal Pelaksanaan</td>
        <td>{{ $package->jadwal_pelaksanaan }}</td>
    </tr>
    <tr>
        <td>Jangka Waktu</td>
        <td>12 (dua belas) bulan kalender</td>
    </tr>
    <tr>
        <td>5.1 Lokasi Pekerjaan</td>
        <td>{{ $package->lokasi_pekerjaan }}</td>
    </tr>
    <tr>
        <td>5.2 Uraian Pekerjaan</td>
        <td>{{ $package->uraian_pekerjaan }}</td>
    </tr>
</table>

    <div class="section-title">5. INFORMASI PELAKSANAAN</div>
    <table>
        <tr>
            <td width="25%">Jadwal Pelaksanaan</td>
            <td>{{ $package->jadwal_pelaksanaan }} [cite: 169]</td>
        </tr>
        <tr>
            <td>Lokasi Pekerjaan</td>
            <td>{{ $package->lokasi_pekerjaan }} [cite: 171]</td>
        </tr>
        <tr>
            <td>Uraian Pekerjaan</td>
            <td>{{ $package->uraian_pekerjaan ?? '-' }} [cite: 172-173]</td>
        </tr>
    </table>

    <div class="section-title">6. PENGESAHAN</div>
    <p style="font-size: 8pt; margin-top: 5px;"><strong>6.1 Penyusunan:</strong> Disusun tanggal {{ $package->tanggal_penyusunan ?? date('d F Y') }} oleh PPK [cite: 175-177]</p>
    
    <p style="font-weight: bold; margin-top: 8px; margin-bottom: 2px;">6.2 Persetujuan </p>
    <table style="width: 100%;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th width="35%">Jabatan</th>
                <th width="30%">Nama</th>
                <th width="15%">Tanda Tangan</th>
                <th width="20%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pengguna Anggaran (PA/KPA)</td>
                <td>{{ $package->nama_pa_kpa ?? '-' }}</td>
                <td height="40px"></td>
                <td></td>
            </tr>
            <tr>
                <td>Pejabat Pembuat Komitmen (PPK)</td>
                <td>{{ auth()->user()->nama_lengkap }}</td>
                <td height="40px"></td>
                <td></td>
            </tr>
            @if($package->nama_tenaga_ahli)
            <tr>
                <td>Tenaga Ahli</td>
                <td>{{ $package->nama_tenaga_ahli }}</td>
                <td height="40px"></td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>