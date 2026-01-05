<!DOCTYPE html>
<html>
<head>
    <title>KAK - {{ $rka->subActivity->nama_sub }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11pt; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 2px; }
        .subtitle { text-align: center; font-size: 10pt; margin-bottom: 20px; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .table-data th, .table-data td { border: 1px solid black; padding: 4px; }
        .table-data th { background-color: #eee; text-align: center; }
        .no-border td { border: none; padding: 2px; vertical-align: top; }
        ul, ol { margin: 0; padding-left: 20px; }
    </style>
</head>
<body>

    <div class="title">KERANGKA ACUAN KERJA (KAK)</div>
    <div class="subtitle">TAHUN ANGGARAN 2025</div>

    {{-- I. IDENTITAS --}}
    <div class="section-title">I. IDENTITAS KEGIATAN</div>
    <table class="no-border">
        <tr><td width="140">Unit Organisasi</td><td width="10">:</td><td>Dinas Komunikasi dan Informatika</td></tr>
        <tr><td>Kegiatan</td><td>:</td><td>{{ $rka->subActivity->activity->nama_kegiatan }}</td></tr>
        <tr><td>Sub Kegiatan</td><td>:</td><td>{{ $rka->subActivity->nama_sub }}</td></tr>
        <tr><td>Lokasi</td><td>:</td><td>{{ $kak->tempat_pelaksanaan ?? $rka->lokasi_kegiatan }}</td></tr>
    </table>

    {{-- II. PENDAHULUAN --}}
    <div class="section-title">II. PENDAHULUAN</div>
    <div style="margin-left: 15px;">
        <b>1. Latar Belakang</b><br>
        <div style="text-align: justify; margin-bottom: 5px;">{!! nl2br(e($kak->latar_belakang)) !!}</div>
        
        <b>2. Dasar Hukum</b>
        <ol>
            @foreach($kak->dasar_hukum ?? [] as $h)
                <li>{{ $h }}</li>
            @endforeach
        </ol>
    </div>

    {{-- III. MAKSUD DAN TUJUAN (DIPECAH TAPI SATU BAB) --}}
    <div class="section-title">III. MAKSUD DAN TUJUAN</div>
    <div style="margin-left: 15px;">
        <b>1. Maksud</b><br>
        <div style="text-align: justify; margin-bottom: 5px;">{!! nl2br(e($kak->maksud)) !!}</div>

        <b>2. Tujuan</b><br>
        <div style="text-align: justify;">{!! nl2br(e($kak->tujuan)) !!}</div>
    </div>

    {{-- V. KELUARAN --}}
    <div class="section-title">V. KELUARAN (OUTPUT)</div>
    <div style="margin-left: 15px;">
        Indikator: {{ $rka->subActivity->indikator_sub }}<br>
        Target: {{ $rka->subActivity->target_2025 }} {{ $rka->subActivity->satuan }}
    </div>

    {{-- VII. METODE --}}
    <div class="section-title">VII. METODE PELAKSANAAN</div>
    <div style="margin-left: 15px;">
        Dilaksanakan secara: <b>{{ $kak->metode_pelaksanaan }}</b>
    </div>

    {{-- VIII. RENCANA FISIK (TANGGAL DARI DB) --}}
    <div class="section-title">VIII. RENCANA REALISASI FISIK</div>
    <div style="margin-left: 15px;">
        Waktu Pelaksanaan: <b>{{ $rka->waktu_pelaksanaan }}</b>
    </div>

    {{-- X. RINCIAN BIAYA (DARI DB RKA_DETAILS) --}}
    <div class="section-title">X. RINCIAN BIAYA</div>
    <table class="table-data">
        <thead>
            <tr>
                <th>Uraian</th>
                <th>Spek</th>
                <th>Vol</th>
                <th>Sat</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rka->details as $detail)
            <tr>
                <td>{{ $detail->uraian_belanja }}</td>
                <td>{{ $detail->spesifikasi }}</td>
                <td align="center">{{ $detail->koefisien }}</td>
                <td align="center">{{ $detail->satuan }}</td>
                <td align="right">{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                <td align="right">{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" align="right"><b>TOTAL</b></td>
                <td align="right"><b>Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>

    <br><br>
    <table class="no-border" style="width: 100%">
        <tr>
            <td width="60%"></td>
            <td align="center">
                Pontianak, {{ date('d F Y') }}<br>
                Pejabat Pembuat Komitmen<br><br><br><br>
                <b><u>NAMA PEJABAT</u></b><br>
                NIP. ....................
            </td>
        </tr>
    </table>

</body>
</html>