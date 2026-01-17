<!DOCTYPE html>
<html>
<head>
    <title>Surat Pesanan - {{ $contract->nomor_sp }}</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; color: #000; }
        .header { text-align: center; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
        .content { margin-top: 20px; text-align: justify; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table, th, td { border: 1px solid black; }
        th { background-color: #f2f2f2; padding: 8px; font-size: 11pt; text-align: center; }
        td { padding: 8px; font-size: 11pt; vertical-align: top; }
        .signature-table { border: none !important; margin-top: 50px; }
        .signature-table td { border: none !important; width: 50%; text-align: center; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        SURAT PESANAN (SP)<br>
        NOMOR: {{ $contract->nomor_sp }}
    </div>

    <div class="content">
        Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($contract->tanggal_sp)->translatedFormat('d F Y') }}</strong>, yang bertanda tangan di bawah ini:<br><br>
        
        <strong>I. Nama: {{ $package->nama_pa_kpa }}</strong><br>
        Jabatan: {{ $contract->jabatan_pejabat ?? 'Pejabat Pembuat Komitmen (PPK)' }}<br>
        NIP: {{ $package->nip_pa_kpa }}<br>
        Alamat: Kantor Dinas Komunikasi dan Informatika Prov. Kalbar<br>
        selanjutnya disebut sebagai <strong>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian</strong>.<br><br>

        <strong>II. Nama: {{ $vendor->nama_direktur }}</strong><br>
        Jabatan: {{ $vendor->jabatan_direktur ?? 'Direktur' }}<br>
        Perusahaan: {{ $vendor->nama_perusahaan }}<br>
        Alamat: {{ $vendor->alamat }}<br>
        selanjutnya disebut sebagai <strong>Penyedia</strong>.<br><br>

        Bersama ini memerintahkan kepada Penyedia untuk melaksanakan pekerjaan dengan rincian sebagai berikut:
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Item Barang/Jasa</th>
                <th>Volume</th>
                <th>Satuan</th>
                <th>Harga Satuan (Rp)</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->nama_item }}</td>
                <td style="text-align: center;">{{ number_format($item->volume, 0) }}</td>
                <td style="text-align: center;">{{ $item->satuan }}</td>
                <td style="text-align: right;">{{ number_format($item->harga_satuan_hps, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($item->total_hps, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold;">TOTAL NILAI KONTRAK</td>
                <td style="text-align: right; font-weight: bold;">
                    Rp {{ number_format($contract->nilai_kontrak_final, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="content">
        1. <strong>Waktu Penyelesaian</strong>: {{ $contract->waktu_penyelesaian }} hari kalender.<br>
        2. <strong>Alamat Penyerahan</strong>: {{ $contract->alamat_penyerahan }}.<br>
        3. <strong>Jenis Pembayaran</strong>: {{ $contract->jenis_pembayaran }}.<br>
        4. <strong>Sumber Dana</strong>: {{ $contract->sumber_dana }}.
    </div>

    <table class="signature-table">
        <tr>
            <td>
                Menerima dan Menyetujui,<br>
                <strong>Penyedia</strong><br><br><br><br>
                ( {{ $vendor->nama_direktur }} )<br>
                {{ $vendor->jabatan_direktur ?? 'Direktur' }}
            </td>
            <td>
                Untuk dan Atas Nama,<br>
                <strong>Pejabat Penandatangan/Pengesahan</strong><br><br><br><br>
                ( {{ $package->nama_pa_kpa }} )<br>
                NIP. {{ $package->nip_pa_kpa }}
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <div class="header">LAMPIRAN: SYARAT UMUM DAN KHUSUS KONTRAK</div>
    <div class="content" style="font-size: 10pt;">
        <strong>1. Hak dan Kewajiban</strong>: Penyedia memiliki hak menerima pembayaran dan kewajiban melaksanakan pekerjaan sesuai spesifikasi.<br>
        <strong>2. Masa Berlaku SP</strong>: SP ini berlaku sejak tanggal ditandatangani sampai selesainya pekerjaan.<br>
        <strong>3. Penyelesaian Perselisihan</strong>: Segala perselisihan akan diselesaikan secara musyawarah atau melalui pengadilan negeri.
    </div>
</body>
</html>