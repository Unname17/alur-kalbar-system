<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pesanan - {{ $contract->nomor_sp }}</title>
    <style>
        /* Pengaturan Kertas A4 */
        @page { margin: 2cm; }
        body { font-family: 'Arial', sans-serif; font-size: 11pt; line-height: 1.4; color: #000; }
        
        /* Typography */
        .header { text-align: center; font-weight: bold; font-size: 14pt; margin-bottom: 5px; text-transform: uppercase; }
        .subheader { text-align: center; font-weight: bold; font-size: 11pt; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; text-decoration: underline; }
        
        /* Table Style */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10pt; }
        table, th, td { border: 1px solid black; }
        th { background-color: #f2f2f2; padding: 8px; text-align: center; font-weight: bold; text-transform: uppercase; }
        td { padding: 6px; vertical-align: top; }
        .no-border, .no-border tr, .no-border td { border: none !important; padding: 2px 0; }
        
        /* Layout Helpers */
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .page-break { page-break-before: always; }
        .signature-container { margin-top: 30px; width: 100%; }
        .signature-box { width: 45%; display: inline-block; vertical-align: top; text-align: center; }
    </style>
</head>
<body>

    {{-- HALAMAN 1: SURAT PESANAN --}}
    <div class="header">SURAT PESANAN (SP)</div>
    <div class="subheader">NOMOR: {{ $contract->nomor_sp }}</div>

    <p>Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($contract->tanggal_sp)->translatedFormat('d F Y') }}</strong>, yang bertanda tangan di bawah ini:</p>

    <table class="no-border">
        <tr>
            <td style="width: 30px;">I.</td>
            <td style="width: 100px;">Nama</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ $contract->nama_pejabat_penandatangan ?? $package->nama_pa_kpa }}</strong></td>
        </tr>
        <tr>
            <td></td>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $contract->jabatan_pejabat }}</td>
        </tr>
        <tr>
            <td></td>
            <td>NIP</td>
            <td>:</td>
            <td>{{ $contract->nip_pejabat_penandatangan ?? $package->nip_pa_kpa }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $package->lokasi_pekerjaan }}</td>
        </tr>
    </table>
    <p style="margin-left: 30px; font-size: 9pt; font-style: italic;">selanjutnya disebut sebagai Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian.</p>

    <table class="no-border">
        <tr>
            <td style="width: 30px;">II.</td>
            <td style="width: 100px;">Nama</td>
            <td style="width: 10px;">:</td>
            {{-- Menggunakan nama_direktur dari database --}}
            <td><strong>{{ $vendor->nama_direktur }}</strong></td>
        </tr>
        <tr>
            <td></td>
            <td>Jabatan</td>
            <td>:</td>
            {{-- Menggunakan jabatan_direktur dari database --}}
            <td>{{ $vendor->jabatan_direktur }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Perusahaan</td>
            <td>:</td>
            <td>{{ $vendor->nama_perusahaan }} ({{ $vendor->bentuk_usaha }})</td>
        </tr>
        <tr>
            <td></td>
            <td>NPWP</td>
            <td>:</td>
            <td>{{ $vendor->npwp }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $vendor->alamat }}</td>
        </tr>
    </table>

    <p style="margin-left: 30px; font-size: 9pt; font-style: italic; margin-bottom: 20px;">selanjutnya disebut sebagai Penyedia.</p>

    <p>Bersama ini memerintahkan kepada Penyedia untuk melaksanakan pekerjaan dengan rincian sebagai berikut:</p>

    {{-- TABEL RINCIAN BARANG --}}
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
            @foreach($items as $item)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $item->nama_item }}</td>
                <td style="text-align: center;">{{ number_format($item->volume, 0) }}</td>
                <td style="text-align: center;">{{ $item->satuan }}</td>
                <td style="text-align: right;">{{ number_format($item->harga_satuan_hps, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($item->total_hps, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right font-bold">TOTAL NILAI KONTRAK</td>
                <td style="text-align: right;" class="font-bold">Rp {{ number_format($contract->nilai_kontrak_final, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- PARAMETER JAMINAN --}}
    <div class="section-title">PARAMETER KEWAJIBAN & JAMINAN (SUKK)</div>
    <table>
        <tr>
            <td style="width: 40%;">Jaminan Pelaksanaan (5%)</td>
            <td class="font-bold">Rp {{ number_format($contract->nilai_jaminan_pelaksanaan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Penerbit Jaminan</td>
            <td>{{ $contract->penerbit_jaminan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Denda Keterlambatan</td>
            <td style="font-style: italic;">{{ $contract->syarat_khusus_tambahan }}</td>
        </tr>
    </table>

    {{-- INFORMASI PELAKSANAAN --}}
    <ol style="font-size: 10pt;">
        <li><strong>Waktu Penyelesaian:</strong> {{ $contract->waktu_penyelesaian }} hari kalender.</li>
        <li><strong>Alamat Penyerahan:</strong> {{ $contract->alamat_penyerahan }}.</li>
        <li><strong>Jenis Pembayaran:</strong> {{ $contract->jenis_pembayaran }}.</li>
        <li><strong>Sumber Dana:</strong> {{ $contract->sumber_dana }}.</li>
    </ol>

    {{-- TANDA TANGAN --}}
    <div class="signature-container">
        <div class="signature-box">
            <p>Menerima dan Menyetujui,<br>Penyedia</p>
            <br><br><br>
            <p><strong>({{ $vendor->nama_direktur }})</strong><br>{{ $vendor->jabatan_direktur }}</p>
        </div>
        <div class="signature-box">
            <p>Untuk dan Atas Nama,<br>Pejabat Penandatangan/Pengesahan</p>
            <br><br><br>
            <p><strong>({{ $contract->nama_pejabat_penandatangan ?? $package->nama_pa_kpa }})</strong><br>NIP. {{ $contract->nip_pejabat_penandatangan ?? $package->nip_pa_kpa }}</p>
        </div>
    </div>

    {{-- HALAMAN LAMPIRAN SUKK (STATIS) --}}
    <div class="page-break"></div>
    <div class="header" style="font-size: 11pt;">LAMPIRAN: SYARAT UMUM DAN KHUSUS KONTRAK SURAT PESANAN</div>
    
    <div style="font-size: 9pt; text-align: justify;">
        <p><strong>1. Hak dan Kewajiban</strong> [cite: 30]</p>
        <p>Penyedia memiliki hak menerima pembayaran sesuai total harga dan waktu yang tercantum dalam SP[cite: 32]. Penyedia berkewajiban melaksanakan pekerjaan sesuai spesifikasi, bertanggung jawab atas kualitas, dan memberikan layanan purna jual[cite: 33, 38, 41]. Pejabat Penandatangan memiliki hak menerima pekerjaan sesuai spesifikasi dan berkewajiban melakukan pembayaran[cite: 44, 45, 50, 51].</p>

        <p><strong>2. Pemeriksaan dan Pengujian</strong> [cite: 60]</p>
        <p>Pejabat Penandatangan berhak melakukan pemeriksaan atas hasil pekerjaan untuk memastikan kecocokan dengan spesifikasi[cite: 65]. Jika hasil tidak sesuai, Pejabat Penandatangan berhak menolak hasil pekerjaan tersebut[cite: 72].</p>

        <p><strong>3. Harga dan Perpajakan</strong> [cite: 74, 78]</p>
        <p>Harga SP telah memperhitungkan keuntungan, pajak, biaya overhead, pengiriman, dan asuransi[cite: 76]. Penyedia berkewajiban membayar semua pajak dan pungutan yang sah sesuai hukum yang berlaku[cite: 79].</p>

        <p><strong>4. Jaminan Pelaksanaan</strong> [cite: 81]</p>
        <p>Untuk nilai transaksi di atas Rp 200 juta, diberlakukan Jaminan Pelaksanaan sebesar 5% dari nilai kontrak[cite: 81, 88]. Jaminan harus bersifat tidak bersyarat dan mudah dicairkan[cite: 85].</p>

        <p><strong>5. Jaminan Bebas Cacat Mutu/Garansi</strong> [cite: 130]</p>
        <p>Penyedia menjamin hasil pekerjaan tidak mengandung cacat mutu akibat desain, bahan, atau cara kerja[cite: 131]. Garansi berlaku sesuai yang tertera dalam spesifikasi teknis[cite: 132].</p>

        <p><strong>6. Sanksi dan Denda</strong> [cite: 164, 205]</p>
        <p>Penyedia yang terlambat menyelesaikan pekerjaan dikenakan denda keterlambatan sebesar 1/1000 (satu perseribu) dari total harga SP untuk setiap hari keterlambatan[cite: 206].</p>

        <p><strong>7. Keadaan Kahar dan Perselisihan</strong> [cite: 207, 213]</p>
        <p>Keadaan Kahar adalah kondisi di luar kehendak para pihak[cite: 208]. Perselisihan akan diupayakan selesai secara damai, atau melalui arbitrase/pengadilan negeri[cite: 214, 215].</p>

        <p style="margin-top: 20px; font-style: italic;">(Sesuai Standar Lampiran 10 - Syarat-Syarat Kontrak Surat Pesanan E-Purchasing Jasa Lainnya) [cite: 29]</p>
    </div>

</body>
</html>