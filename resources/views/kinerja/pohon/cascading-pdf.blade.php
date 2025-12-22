<!DOCTYPE html>
<html>
<head>
    <title>Cetak Cascading</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        th { background-color: #f2f2f2; text-transform: uppercase; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .lvl-visi { background-color: #eee; }
    </style>
</head>
<body>
    <div class="text-center">
        <h2 style="margin-bottom: 5px;">MATRIKS CASCADING KINERJA</h2>
        <h3>{{ $opd->nama_perangkat_daerah }}</h3>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th width="8%">LEVEL</th>
                <th>URAIAN KINERJA</th>
                <th width="25%">INDIKATOR</th>
                <th width="12%">TARGET</th>
                <th width="15%">ANGGARAN</th>
            </tr>
        </thead>
        <tbody>
    @foreach($pohons as $node)
        @include('kinerja.pohon.partial-cascading-row', ['node' => $node, 'level' => 0, 'isPdf' => true])
    @endforeach
</tbody>
    </table>
</body>
</html>