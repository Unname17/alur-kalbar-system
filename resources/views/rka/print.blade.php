<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RKA SKPD - {{ $rka->subActivity->nama_sub ?? '-' }}</title>
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 8pt; line-height: 1.3; margin: 0; padding: 0; }
        
        /* Blok Tabel dengan Margin Sesuai PDF */
        .table-block { width: 100%; border-collapse: collapse; margin-bottom: 12px; table-layout: fixed; }
        .table-block td, .table-block th { border: 1px solid black; padding: 4px; vertical-align: top; }
        
        .no-border td { border: none !important; padding: 1px 4px; }
        .bg-grey { background-color: #dfdfdf !important; -webkit-print-color-adjust: exact; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .underline { text-decoration: underline; }

        /* Header Layout */
        .header-table td { padding: 10px; font-weight: bold; font-size: 9.5pt; text-align: center; }
    </style>
</head>
<body>

    <table class="table-block header-table">
        <tr>
            <td width="70%">
                RENCANA KERJA DAN ANGGARAN<br>
                SATUAN KERJA PERANGKAT DAERAH
            </td>
            <td width="30%">Formulir<br>RKA-BELANJA<br>SKPD</td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 9pt; padding: 5px;">
                Pemerintahan Provinsi Kalimantan Barat Tahun Anggaran 2025
            </td>
        </tr>
    </table>

    <table class="table-block">
        <tr>
            <td class="bg-grey text-center font-bold">Rincian Anggaran Belanja Menurut Program, Kegiatan dan Sub Kegiatan</td>
        </tr>
        <tr>
            <td style="padding: 5px;">
                <table class="no-border" style="width: 100%;">
                    <tr><td width="160">Urusan Pemerintahan</td><td width="10">:</td><td>2 URUSAN PEMERINTAHAN WAJIB YANG TIDAK BERKAITAN DENGAN PELAYANAN DASAR</td></tr>
                    <tr><td>Bidang Urusan</td><td>:</td><td>2.16 URUSAN PEMERINTAHAN BIDANG KOMUNIKASI DAN INFORMATIKA</td></tr>
                    <tr><td>Unit Organisasi</td><td>:</td><td>2.16.2.20.2.21.01.0000 DINAS KOMUNIKASI DAN INFORMATIKA PROVINSI KALIMANTAN BARAT</td></tr>
                    
                    <tr><td>Sub Unit Organisasi</td><td>:</td><td>{{ $rka->sub_unit_organisasi ?? '-' }}</td></tr>
                    
                    <tr><td>Program</td><td>:</td><td>{{ $rka->subActivity->activity->program->nama_program ?? '-' }}</td></tr>
                    <tr><td>Kegiatan</td><td>:</td><td>{{ $rka->subActivity->activity->nama_kegiatan ?? '-' }}</td></tr>
                    <tr><td>Sub Kegiatan</td><td>:</td><td>{{ $rka->subActivity->kode_sub ?? '-' }} {{ $rka->subActivity->nama_sub ?? '-' }}</td></tr>
                    
                    <tr><td>SPM</td><td>:</td><td>{{ $rka->spm ?? '( - )' }}</td></tr>
                    <tr><td>Jenis Layanan</td><td>:</td><td>{{ $rka->jenis_layanan ?? '( - )' }}</td></tr>
                    
                    <tr><td>Sumber Pendanaan</td><td>:</td><td>{{ $rka->sumber_dana ?? '-' }}</td></tr>
                    <tr><td>Lokasi</td><td>:</td><td>{{ $rka->lokasi_kegiatan ?? '-' }}</td></tr>
                    <tr><td>Waktu Pelaksanaan</td><td>:</td><td>{{ $rka->waktu_pelaksanaan ?? '-' }}</td></tr>
                    <tr><td>Kelompok Sasaran</td><td>:</td><td>{{ $rka->kelompok_sasaran ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="table-block">
        <tr class="bg-grey"><td colspan="3" class="text-center">Indikator dan Tolak Ukur Kinerja Kegiatan</td></tr>
        <tr class="bg-grey text-center">
            <th width="20%">Indikator</th><th width="55%">Tolok Ukur Kinerja</th><th width="25%">Target Kinerja</th>
        </tr>
        <tr><td>Capaian Program</td><td>Persentase Total Bobot Domain Evaluasi SPBE</td><td class="text-center">73.60%</td></tr>
        <tr><td>Masukan</td><td>Dana yang dibutuhkan</td><td class="text-center">Rp {{ number_format($rka->total_anggaran ?? 0, 2, ',', '.') }}</td></tr>
        <tr><td>Keluaran</td><td>Jumlah dokumen koordinasi Fasilitasi Promosi Literasi SPBE...</td><td class="text-center">1 Dokumen</td></tr>
        <tr><td>Hasil</td><td>Persentase pengelolaan e-government...</td><td class="text-center">100%</td></tr>
    </table>

    <table class="table-block" style="margin-bottom: 0;">
        <thead>
            <tr class="bg-grey text-center">
                <th width="15%" rowspan="2">Kode Rekening</th>
                <th width="35%" rowspan="2">Uraian</th>
                <th colspan="4">Rinci Perhitungan</th>
                <th width="15%" rowspan="2">Jumlah</th>
            </tr>
            <tr class="bg-grey text-center">
                <th>Koefisien</th><th>Satuan</th><th>Harga</th><th>PPN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details as $items)
                <tr class="font-bold bg-grey">
                    <td>{{ $items->first()->rekening->kode_rekening ?? '-' }}</td>
                    <td colspan="5">{{ $items->first()->rekening->nama_rekening ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($items->sum('sub_total') ?? 0, 2, ',', '.') }}</td>
                </tr>
                @foreach($items as $item)
                <tr>
                    <td></td>
                    <td>{{ $item->uraian_belanja ?? '-' }}<br><span style="font-size: 7pt; font-style: italic;">Spesifikasi: {{ $item->spesifikasi ?? '-' }}</span></td>
                    <td class="text-center">{{ $item->koefisien ?? '-' }}</td>
                    <td class="text-center">{{ $item->satuan ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->harga_satuan ?? 0, 2, ',', '.') }}</td>
                    <td class="text-center">0%</td>
                    <td class="text-right">Rp {{ number_format($item->sub_total ?? 0, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            @empty
                <tr><td colspan="7" class="text-center">- Tidak ada rincian belanja -</td></tr>
            @endforelse
            <tr class="bg-grey font-bold">
                <td colspan="6" class="text-right">JUMLAH TOTAL USULAN</td>
                <td class="text-right">Rp {{ number_format($rka->total_anggaran ?? 0, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="table-block" style="border-top: none;">
        <tr>
            <td width="60%" style="border-right: none;"></td>
            <td class="text-center" style="border-left: none; padding: 15px;">
                Provinsi Kalimantan Barat, {{ date('d F Y', strtotime($rka->created_at ?? now())) }}<br>
                Kepala Dinas KOMUNIKASI DAN INFORMATIKA PROVINSI KALIMANTAN BARAT<br><br><br><br>
                <span class="font-bold underline">{{ $namaPptk ?? '-' }}</span><br>
                NIP. {{ $nipPptk ?? '-' }}
            </td>
        </tr>
    </table>

    <table class="table-block">
        <tr><td width="150" class="bg-grey">Pembahasan</td><td class="bg-grey">:</td></tr>
        <tr><td>Tanggal</td><td>: {{ date('d F Y') }}</td></tr>
        <tr><td>Catatan</td><td>: <br> 1. <br> 2. <br> Dst. </td></tr>
    </table>

<table class="table-block">
    <tr class="bg-grey text-center font-bold"><td colspan="5">TIM ANGGARAN PEMERINTAH DAERAH</td></tr>
    <tr class="bg-grey text-center font-bold">
        <th width="30">NO</th><th>NAMA</th><th>NIP</th><th>JABATAN</th><th width="100">TANDA TANGAN</th>
    </tr>
    @php $tim = json_decode($rka->tim_anggaran); @endphp
    @forelse($tim ?? [] as $index => $person)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td>{{ $person->nama }}</td>
        <td>{{ $person->nip }}</td>
        <td>{{ $person->jabatan ?? '-' }}</td>
        <td></td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="text-center" style="font-style: italic; color: #777; padding: 15px;">
            ( Data Kosong )
        </td>
    </tr>
    @endforelse
</table>



</body>
</html>