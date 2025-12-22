<!DOCTYPE html>
<html>
<head>
    <title>Surat Pesanan - {{ $pengadaan->id }}</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.3; }
        .header { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        .section-title { font-weight: bold; margin-top: 15px; border-bottom: 1px solid #000; display: inline-block; }
        table { width: 100%; margin-top: 10px; }
        td { vertical-align: top; padding: 2px 0; }
    </style>
</head>
<body>
    <div class="header">SURAT PESANAN (KONTRAK)</div>
    
    <p>Yang bertanda tangan di bawah ini menetapkan pesanan kepada:</p>
    
    <table>
        <tr>
            <td width="150">Nama Perusahaan</td>
            <td width="10">:</td>
            <td><strong>{{ $pengadaan->vendor->nama_perusahaan ?? 'Belum Dipilih' }}</strong> </td>
        </tr>
        <tr>
            <td>NPWP</td>
            <td>:</td>
            <td>{{ $pengadaan->vendor->npwp ?? '-' }} </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $pengadaan->vendor->alamat ?? '-' }} </td>
        </tr>
        <tr>
            <td>Nama Direktur</td>
            <td>:</td>
            <td>{{ $pengadaan->vendor->nama_direktur ?? '-' }} </td>
        </tr>
    </table>

    <div class="section-title">RINCIAN PEKERJAAN</div>
    <p>Paket pekerjaan <strong>{{ $pengadaan->rkaPerencanaan->judul_kak }}</strong> dengan rincian anggaran sebesar 
    <strong>Rp {{ number_format($pengadaan->rka->total_anggaran ?? 0, 0, ',', '.') }}</strong>[cite: 112].</p>

    <div class="section-title">INFORMASI PEMBAYARAN</div>
    <p>Pembayaran akan dilakukan melalui transfer ke rekening berikut:</p>
    <table>
        <tr>
            <td width="150">Nama Bank</td>
            <td>: {{ $pengadaan->vendor->nama_bank ?? '-' }} </td>
        </tr>
        <tr>
            <td>Nomor Rekening</td>
            <td>: {{ $pengadaan->vendor->nomor_rekening ?? '-' }} </td>
        </tr>
        <tr>
            <td>Atas Nama</td>
            <td>: {{ $pengadaan->vendor->nama_rekening ?? '-' }} </td>
        </tr>
    </table>

    <div style="margin-top: 50px;">
        <div style="float: left; width: 200px; text-align: center;">
            Menerima/Menyetujui<br>Direktur Perusahaan<br><br><br><br>
            (........................)
        </div>
        <div style="float: right; width: 200px; text-align: center;">
            Pontianak, {{ date('d/m/Y') }}<br>PPK Diskominfo<br><br><br><br>
            (........................)
        </div>
    </div>
</body>
</html>