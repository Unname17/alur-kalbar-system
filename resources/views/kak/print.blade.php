<!DOCTYPE html>
<html>
<head>
    <title>KAK - {{ $rka->subActivity->nama_sub ?? 'Kegiatan' }}</title>
    <style>
        @page { margin: 2cm 1.5cm; }
        body { font-family: 'Arial', sans-serif; font-size: 11pt; line-height: 1.4; color: #000; }
        
        /* Typography */
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 14pt; font-weight: bold; margin: 0; text-decoration: underline; text-transform: uppercase; }
        .header p { font-size: 11pt; font-weight: bold; margin: 5px 0 0; text-transform: uppercase; }
        
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; text-transform: uppercase; }
        
        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 10px; page-break-inside: avoid; }
        .table-data th, .table-data td { border: 1px solid black; padding: 5px; vertical-align: top; }
        .table-data th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .no-border td { border: none; padding: 2px 5px; vertical-align: top; }
        
        /* Lists & Text */
        .content-text { text-align: justify; margin-left: 20px; margin-bottom: 5px; }
        ol, ul { margin: 0 0 0 20px; padding-left: 15px; }
        li { margin-bottom: 3px; text-align: justify; }
        
        /* Matrix Schedule */
        .matrix-table th { font-size: 8pt; padding: 2px; }
        .matrix-table td { font-size: 8pt; text-align: center; padding: 2px; }
        .check-mark { font-family: DejaVu Sans, sans-serif; font-weight: bold; color: #000; }
        .block-fill { background-color: #333; color: #333; font-size: 0px; height: 10px; display: block; margin: 2px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>KERANGKA ACUAN KERJA (KAK)</h1>
        <p>TAHUN ANGGARAN {{ $rka->created_at ? $rka->created_at->format('Y') : date('Y') }}</p>
    </div>

    {{-- 1. IDENTITAS KEGIATAN --}}
    <div class="section-title">1. IDENTITAS KEGIATAN</div>
    <table class="no-border">
        <tr><td width="150">Urusan Pemerintahan</td><td width="10">:</td><td>Urusan Pemerintahan Bidang Komunikasi dan Informatika</td></tr>
        <tr><td>Unit Organisasi</td><td>:</td><td>Dinas Komunikasi dan Informatika</td></tr>
        <tr><td>Program</td><td>:</td><td>{{ $rka->subActivity->activity->program->nama_program ?? '-' }}</td></tr>
        <tr><td>Kegiatan</td><td>:</td><td>{{ $rka->subActivity->activity->nama_kegiatan ?? '-' }}</td></tr>
        <tr><td>Sub Kegiatan</td><td>:</td><td>{{ $rka->subActivity->nama_sub ?? '-' }}</td></tr>
        <tr><td>Lokasi Kegiatan</td><td>:</td><td>{{ $kak->tempat_pelaksanaan ?? $rka->lokasi_kegiatan }}</td></tr>
    </table>

    {{-- 2. PENDAHULUAN --}}
    <div class="section-title">2. PENDAHULUAN</div>
    
    <div style="margin-left: 20px;"><b>a. Latar Belakang</b></div>
    <div class="content-text">{!! nl2br(e($kak->latar_belakang ?? '-')) !!}</div>

    <div style="margin-left: 20px;"><b>b. Dasar Hukum</b></div>
    <ol>
        @if(!empty($kak->dasar_hukum) && is_array($kak->dasar_hukum))
            @foreach($kak->dasar_hukum as $hukum)
                @if(!empty($hukum)) <li>{{ $hukum }}</li> @endif
            @endforeach
        @else
            <li>-</li>
        @endif
    </ol>

    {{-- 3. MAKSUD DAN TUJUAN --}}
    <div class="section-title">3. MAKSUD DAN TUJUAN</div>
    
    <div style="margin-left: 20px;"><b>a. Maksud</b></div>
    <div class="content-text">{!! nl2br(e($kak->maksud ?? '-')) !!}</div>

    <div style="margin-left: 20px;"><b>b. Tujuan</b></div>
    <ol style="list-style-type: lower-alpha;">
        @if(!empty($kak->tujuan) && is_array($kak->tujuan))
            @foreach($kak->tujuan as $tuj)
                @if(!empty($tuj)) <li>{{ $tuj }}</li> @endif
            @endforeach
        @else
            <li>-</li>
        @endif
    </ol>

    {{-- 4. SASARAN KINERJA KEGIATAN --}}
    <div class="section-title">4. SASARAN KINERJA KEGIATAN</div>
    <div class="content-text">
        Indikator Kinerja untuk kegiatan ini adalah sebagai berikut:
    </div>
    <table class="table-data" style="width: 100%;">
        <thead>
            <tr>
                <th width="20%">Sasaran</th>
                <th width="35%">Indikator Kinerja</th>
                <th width="20%">Target</th>
                <th width="25%">Anggaran</th>
            </tr>
        </thead>
        <tbody>
            {{-- Program --}}
            <tr>
                <td><b>Program</b></td>
                <td>{{ $rka->subActivity->activity->program->indikator_program ?? '-' }}</td>
                <td align="center">100 %</td>
                <td align="center" rowspan="3" style="vertical-align: middle; background-color: #fdfdfd;">
                    <b>Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}</b>
                </td>
            </tr>
            {{-- Kegiatan --}}
            <tr>
                <td><b>Kegiatan</b></td>
                <td>{{ $rka->subActivity->activity->indikator_kegiatan ?? '-' }}</td>
                <td align="center">100 %</td>
            </tr>
            {{-- Sub Kegiatan --}}
            <tr>
                <td><b>Sub Kegiatan</b><br>(Output)</td>
                <td>{{ $rka->subActivity->indikator_sub ?? '-' }}</td>
                <td align="center">
                    {{ $rka->subActivity->tahun_1 ?? 0 }} {{ $rka->subActivity->satuan }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- 5. KELUARAN (OUTPUT) --}}
    <div class="section-title">5. KELUARAN (OUTPUT)</div>
    <div class="content-text">
        Keluaran (Output) yang dihasilkan dari kegiatan ini adalah <b>{{ $rka->subActivity->indikator_sub ?? '-' }}</b> dengan target sebesar <b>{{ $rka->subActivity->tahun_1 ?? 0 }} {{ $rka->subActivity->satuan ?? '-' }}</b>.
    </div>

    {{-- 6. HASIL YANG DIHARAPKAN (OUTCOME) --}}
    <div class="section-title">6. HASIL YANG DIHARAPKAN (OUTCOME)</div>
    <div class="content-text">{!! nl2br(e($kak->penerima_manfaat ?? '-')) !!}</div>

    {{-- 7. RINCIAN KEGIATAN / METODE PELAKSANAAN --}}
    <div class="section-title">7. RINCIAN KEGIATAN / METODE PELAKSANAAN</div>
    <div class="content-text">
        Kegiatan ini dilaksanakan dengan metode <b>{{ $kak->metode_pelaksanaan ?? 'Swakelola' }}</b> melalui tahapan-tahapan sebagai berikut:
    </div>
    <ol>
        @if(!empty($kak->tahapan_pelaksanaan) && is_array($kak->tahapan_pelaksanaan))
            @foreach($kak->tahapan_pelaksanaan as $tahap)
                @if(!empty($tahap['uraian']))
                    <li>
                        <b>{{ $tahap['uraian'] }}</b>
                        @if(!empty($tahap['output'])) <br><i>Output: {{ $tahap['output'] }}</i> @endif
                    </li>
                @endif
            @endforeach
        @else
            <li>-</li>
        @endif
    </ol>

    {{-- 8. RENCANA REALISASI FISIK (MATRIKS JADWAL) --}}
    <div class="section-title">8. RENCANA REALISASI FISIK</div>
    <div class="content-text">Rencana pelaksanaan kegiatan (Time Schedule) adalah sebagai berikut:</div>
    
    <table class="table-data matrix-table" style="width: 100%;">
        <thead>
            <tr>
                <th rowspan="2" width="30%">Tahapan Kegiatan</th>
                <th colspan="12">Bulan (Tahun {{ $rka->created_at ? $rka->created_at->format('Y') : date('Y') }})</th>
            </tr>
            <tr>
                @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'] as $bln)
                    <th width="5.8%">{{ $bln }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if(!empty($kak->tahapan_pelaksanaan) && is_array($kak->tahapan_pelaksanaan))
                @foreach($kak->tahapan_pelaksanaan as $tahap)
                    @if(!empty($tahap['uraian']))
                    <tr>
                        <td align="left">{{ $tahap['uraian'] }}</td>
                        @php $months = $tahap['months'] ?? []; @endphp
                        
                        {{-- Loop Bulan Jan-Des --}}
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'] as $m)
                            <td>
                                @if(in_array($m, $months))
                                    {{-- Block hitam jika bulan dipilih --}}
                                    <span class="block-fill">V</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endif
                @endforeach
            @else
                <tr><td colspan="13" align="center">- Jadwal Belum Diisi -</td></tr>
            @endif
        </tbody>
    </table>

    {{-- 9. LOKASI KEGIATAN --}}
    <div class="section-title">9. LOKASI KEGIATAN</div>
    <div class="content-text">
        Kegiatan ini dilaksanakan di <b>{{ $kak->tempat_pelaksanaan ?? $rka->lokasi_kegiatan }}</b>.
    </div>

    {{-- 10. RINCIAN BIAYA --}}
    <div class="section-title">10. RINCIAN BIAYA</div>
    <div class="content-text">
        Biaya yang dibebankan pada APBD Provinsi Kalimantan Barat sebesar <b>Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}</b> dengan rincian sebagai berikut:
    </div>
    <table class="table-data">
        <thead>
            <tr>
                <th>Uraian Belanja</th>
                <th width="10%">Vol</th>
                <th width="10%">Sat</th>
                <th width="15%">Harga</th>
                <th width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rka->details as $detail)
            <tr>
                <td>
                    {{ $detail->uraian_belanja }}
                    @if($detail->spesifikasi) <br><i style="font-size: 9pt;">Spec: {{ $detail->spesifikasi }}</i> @endif
                </td>
                <td align="center">{{ number_format($detail->koefisien, 0, ',', '.') }}</td>
                <td align="center">{{ $detail->satuan }}</td>
                <td align="right">{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                <td align="right">{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" align="center">- Belum ada rincian belanja -</td></tr>
            @endforelse
            <tr style="background-color: #f0f0f0;">
                <td colspan="4" align="right"><b>TOTAL ANGGARAN</b></td>
                <td align="right"><b>Rp {{ number_format($rka->total_anggaran, 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>

    {{-- 11. PENANGGUNG JAWAB --}}
    <div class="section-title">11. PENANGGUNG JAWAB KEGIATAN</div>
    <div class="content-text">
        Penanggung jawab pelaksanaan kegiatan ini adalah <b>{{ $pejabat->jabatan ?? 'Kepala Dinas' }}</b> Dinas Komunikasi dan Informatika.
    </div>

    {{-- 12. PENUTUP --}}
    <div class="section-title">12. PENUTUP</div>
    <div class="content-text">
        Demikian Kerangka Acuan Kerja (KAK) ini disusun sebagai pedoman dalam pelaksanaan kegiatan agar dapat berjalan efektif, efisien, dan sesuai dengan ketentuan yang berlaku.
    </div>

    <br><br>
    
    {{-- TANDA TANGAN --}}
    <table class="no-border" style="width: 100%">
        <tr>
            <td width="50%"></td>
            <td align="center">
                Pontianak, {{ date('d F Y') }}<br>
                <span style="font-weight: bold;">{{ strtoupper($pejabat->jabatan) }}</span>
                <br><br><br><br><br>
                
                <b><u>{{ $pejabat->nama }}</u></b><br>
                NIP. {{ $pejabat->nip }}
            </td>
        </tr>
    </table>

</body>
</html>