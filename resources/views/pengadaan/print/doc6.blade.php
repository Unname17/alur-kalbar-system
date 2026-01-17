<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Doc 6 - Analisis Referensi dan Kewajaran Harga</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Arial', sans-serif; font-size: 8.5px; line-height: 1.4; color: #000; }
        .header { text-align: center; font-weight: bold; font-size: 11px; text-transform: uppercase; margin-bottom: 5px; }
        .sub-header { text-align: center; font-weight: bold; font-size: 10px; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; table-layout: fixed; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; padding: 6px; border: 1px solid #000; text-transform: uppercase; font-size: 8px; }
        td { border: 1px solid #000; padding: 6px; vertical-align: top; word-wrap: break-word; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; font-size: 9px; text-transform: uppercase; background: #eee; padding: 4px; border: 1px solid #000; }
        .footer-table { margin-top: 30px; border: none; }
        .footer-table td { border: none; text-align: center; width: 50%; padding: 0 20px; }
        .sig-space { height: 60px; }
        .disclaimer-box { border: 1px solid #000; padding: 10px; font-size: 8px; margin-top: 15px; background: #fdfdfd; font-style: italic; }
        /* Font khusus agar simbol centang (check-mark) tercetak sempurna */
        .check-mark { font-family: DejaVu Sans, sans-serif; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">ANALISIS REFERENSI DAN KEWAJARAN HARGA </div>
    <div class="sub-header">PENGADAAN BARANG MELALUI E-PURCHASING </div>

    {{-- BAGIAN I: INFORMASI UMUM PAKET PENGADAAN --}}
    <div class="section-title">BAGIAN I: INFORMASI UMUM PAKET PENGADAAN</div>
    <table>
        <tr><td width="35%">Nama Paket Pengadaan</td><td><strong>{{ $package->nama_paket }}</strong> [cite: 134]</td></tr>
        <tr><td>Uraian & Spesifikasi Kunci</td><td>{{ $package->items->pluck('deskripsi_spesifikasi')->implode(', ') }} [cite: 134]</td></tr>
        <tr><td>Pejabat Pembuat Komitmen (PPK)</td><td>{{ $package->nama_pa_kpa ?? '-' }} [cite: 162]</td></tr>
        <tr><td>Satuan Kerja</td><td>Dinas Komunikasi dan Informatika Prov. Kalbar [cite: 134]</td></tr>
        <tr><td>Tanggal Analisis</td><td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} [cite: 134]</td></tr>
    </table>

    {{-- BAGIAN II.A.1: ANALISIS KUALITATIF [cite: 172-175] --}}
    <div class="section-title">BAGIAN II.A.1: Analisis Kualitatif Produk Berdasarkan Ulasan Daring</div>
    <table>
        <thead>
            <tr>
                <th width="20%">Merek & Model</th>
                <th width="25%">Tautan / Sumber Link</th>
                <th width="45%">Ringkasan Ulasan (Kelebihan & Kekurangan)</th>
                <th width="10%">Kesimpulan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($package->price_references->where('type', 'qualitative') as $ref)
            <tr>
                <td>{{ $ref->merek_model }}</td>
                <td><small style="color: blue;">{{ $ref->link_url }}</small></td>
                <td>
                    <strong>Kelebihan:</strong> {{ $ref->kelebihan }}<br>
                    <strong>Kekurangan:</strong> {{ $ref->kekurangan }}
                </td>
                <td align="center">Layak [cite: 144]</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- BAGIAN II.A.2: SURVEI HARGA PASAR [cite: 176-179] --}}
    <div class="section-title">BAGIAN II.A.2: Survei Harga Pasar (E-Commerce)</div>
    <table>
        <thead>
            <tr>
                <th width="20%">Merek & Model</th>
                <th width="15%">Penyedia</th>
                <th width="15%">Harga (Rp)</th>
                <th width="20%">Garansi & Layanan</th>
                <th width="20%">Tautan Produk</th>
                <th width="10%">Bukti</th>
            </tr>
        </thead>
        <tbody>
            @foreach($package->price_references->where('type', 'market') as $ref)
            <tr>
                <td>{{ $ref->merek_model }}</td>
                <td>{{ $ref->sumber_nama }}</td>
                <td align="right">{{ number_format($ref->harga_satuan, 0, ',', '.') }}</td>
                <td>{{ $ref->garansi_layanan ?? '-' }} [cite: 145]</td>
                <td><small style="color: blue;">{{ $ref->link_url }}</small></td>
                <td align="center">Lampiran {{ $loop->iteration }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- BAGIAN II.B: REFERENSI SBU (DIPISAHKAN) [cite: 180-182] --}}
    <div class="section-title">BAGIAN II.B: Sumber Informasi Biaya Resmi (SBU/ASB)</div>
    <table>
        <thead>
            <tr>
                <th width="40%">Nama Dokumen & Penerbit</th>
                <th width="20%">Nomor/Tgl Dokumen</th>
                <th width="20%">Harga (Rp)</th>
                <th width="20%">Catatan Relevansi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($package->price_references->where('type', 'sbu') as $ref)
            <tr>
                <td>{{ $ref->sumber_nama }}</td>
                <td>{{ $ref->nomor_tanggal_dok }}</td>
                <td align="right">{{ number_format($ref->harga_satuan, 0, ',', '.') }}</td>
                <td>{{ $ref->catatan_relevansi ?? 'Sebagai batas atas biaya' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" align="center">Tidak ada data SBU yang diinput.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- BAGIAN II.C: KONTRAK MASA LALU (DIPISAHKAN) [cite: 183-185] --}}
    <div class="section-title">BAGIAN II.C: Dokumen Kontrak Sejenis Masa Lalu</div>
    <table>
        <thead>
            <tr>
                <th width="40%">Nama Paket / Kontrak</th>
                <th width="10%">Tahun</th>
                <th width="20%">Harga (Rp)</th>
                <th width="30%">Penyesuaian (Inflasi/Spek)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($package->price_references->where('type', 'contract') as $ref)
            <tr>
                <td>{{ $ref->sumber_nama }}</td>
                <td align="center">{{ $ref->tahun_anggaran }}</td>
                <td align="right">{{ number_format($ref->harga_satuan, 0, ',', '.') }}</td>
                <td>{{ $ref->catatan_penyesuaian }} [cite: 149]</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- BAGIAN III.A: REKAPITULASI HARGA [cite: 187-189] --}}
    <div class="section-title">BAGIAN III.A: REKAPITULASI DATA HARGA YANG VALID DAN RELEVAN</div>
    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="60%">Sumber Data / Referensi</th>
                <th width="30%">Keterangan Validasi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($package->price_references->whereIn('type', ['market', 'sbu', 'contract']) as $rekap)
            <tr>
                <td align="center">{{ $no++ }}</td>
                <td>{{ $rekap->sumber_nama }} {{ $rekap->merek_model ? '('.$rekap->merek_model.')' : '' }} - <strong>Rp {{ number_format($rekap->harga_satuan, 0, ',', '.') }}</strong></td>
                <td>Data valid, spesifikasi barang memenuhi kebutuhan teknis utama. [cite: 151]</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- BAGIAN III.B: HASIL ANALISIS [cite: 191] --}}
    <div class="section-title">BAGIAN III.B: HASIL ANALISIS KEWAJARAN HARGA</div>
    <table>
        <tr><td width="70%">Harga Wajar Referensi Terendah</td><td align="right">Rp {{ number_format($package->hps_terendah ?? 0, 0, ',', '.') }} [cite: 153]</td></tr>
        <tr><td>Harga Wajar Referensi Tertinggi</td><td align="right">Rp {{ number_format($package->hps_tertinggi ?? 0, 0, ',', '.') }} [cite: 153]</td></tr>
        <tr style="background-color: #f9f9f9;"><td><strong>HARGA RATA-RATA PEMBANDING</strong></td><td align="right"><strong>Rp {{ number_format($package->hps_hitung_rata_rata ?? 0, 0, ',', '.') }}</strong> [cite: 157]</td></tr>
    </table>

    {{-- BAGIAN IV: DAFTAR LAMPIRAN (LOGIKA PENGECEKAN GANDA) [cite: 196-197] --}}
    <div class="section-title">BAGIAN IV: DAFTAR LAMPIRAN BUKTI PENDUKUNG</div>
    <table>
        <thead>
            <tr><th width="10%">No</th><th width="60%">Jenis Bukti</th><th width="30%">Status Keberadaan</th></tr>
        </thead>
        <tbody>
            {{-- 1. Screenshot Ulasan --}}
            <tr>
                <td align="center">1</td>
                <td>Screenshot ulasan produk (dari A.1)</td>
                <td align="center">
                    @if($package->price_references->where('type', 'qualitative')->whereNotNull('file_bukti')->count() > 0)
                        <span class="check-mark">Ada (&#10003;)</span>
                    @else
                        Tidak Ada (X)
                    @endif
                </td>
            </tr>
            {{-- 2. Screenshot Harga Pasar --}}
            <tr>
                <td align="center">2</td>
                <td>Screenshot harga dari sumber pasar (dari A.2)</td>
                <td align="center">
                    @if($package->price_references->where('type', 'market')->whereNotNull('file_bukti')->count() > 0)
                        <span class="check-mark">Ada (&#10003;)</span>
                    @else
                        Tidak Ada (X)
                    @endif
                </td>
            </tr>
            {{-- 3. Dokumen SBU (Cek SS Baris ATAU File Utama) --}}
            <tr>
                <td align="center">3</td>
                <td>Salinan dokumen SBU/ASB (dari Bagian B)</td>
                <td align="center">
                    @php 
                        $hasSbuSs = $package->price_references->where('type', 'sbu')->whereNotNull('file_bukti')->count() > 0;
                        $hasSbuPdf = !empty($package->file_sbu);
                    @endphp
                    @if($hasSbuSs || $hasSbuPdf)
                        <span class="check-mark">Ada (&#10003;)</span>
                    @else
                        Tidak Ada (X)
                    @endif
                </td>
            </tr>
            {{-- 4. Salinan Kontrak (Cek SS Baris ATAU File Utama) --}}
            <tr>
                <td align="center">4</td>
                <td>Salinan kontrak terdahulu (dari Bagian C)</td>
                <td align="center">
                    @php 
                        $hasContractSs = $package->price_references->where('type', 'contract')->whereNotNull('file_bukti')->count() > 0;
                        $hasContractPdf = !empty($package->file_kontrak_lama);
                    @endphp
                    @if($hasContractSs || $hasContractPdf)
                        <span class="check-mark">Ada (&#10003;)</span>
                    @else
                        Tidak Ada (X)
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- BAGIAN V: DISCLAIMER --}}
    <div class="disclaimer-box">
        <strong>PERNYATAAN (DISCLAIMER):</strong> Dokumen ini disusun secara internal hanya untuk tujuan persiapan pelaksanaan e-purchasing pada paket {{ $package->nama_paket }}. [cite: 158] Analisis, data, dan rentang harga yang tercantum berfungsi sebagai referensi negosiasi harga dan tidak dapat dijadikan dasar pembuktian hukum untuk tujuan lain. [cite: 158]
    </div>

    {{-- BAGIAN VI: PENGESAHAN  --}}
    <table class="footer-table">
        <tr>
            <td>
                Disusun oleh,<br>Pejabat Pengadaan / Staf Pendukung 
                <div class="sig-space"></div>
                ( ......................................... )<br>NIP. 
            </td>
            <td>
                Disetujui oleh,<br>Pejabat Pembuat Komitmen (PPK) 
                <div class="sig-space"></div>
                <strong>{{ $package->nama_pa_kpa }}</strong> <br>NIP. {{ $package->nip_pa_kpa }} 
            </td>
        </tr>
    </table>

</body>
</html>