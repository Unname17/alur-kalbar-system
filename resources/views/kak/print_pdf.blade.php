<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .nomor { text-align: center; margin-bottom: 20px; }
        .section-title { font-weight: bold; margin-top: 15px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; padding: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">PEMERINTAH PROVINSI KALIMANTAN BARAT</h2>
        <h3 style="margin:0">SEKRETARIAT DAERAH</h3>
        <p style="margin:0">Jl. Ahmad Yani No. 1, Pontianak</p>
    </div>

    <div class="judul">KERANGKA ACUAN KERJA (KAK)</div>
    <div class="nomor">Nomor: {{ $kak->nomor_kak ?? '...........................' }}</div>

    <div class="section-title">I. LATAR BELAKANG</div>
    <p>{{ $kak->latar_belakang }}</p>

    <div class="section-title">II. MAKSUD DAN TUJUAN</div>
    <p>{{ $kak->maksud_tujuan }}</p>

    <div class="section-title">III. TIM PELAKSANA</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama / NIP</th>
                <th>Peran dalam Tim</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kak->timPelaksana as $key => $tim)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $tim->nama_personil }} <br> <small>NIP. {{ $tim->nip }}</small></td>
                <td>{{ $tim->peran_dalam_tim }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; float: right; width: 200px; text-align: center;">
        <p>Pontianak, {{ date('d F Y') }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p><b>( ........................... )</b><br>NIP. ...........................</p>
    </div>
</body>
</html>