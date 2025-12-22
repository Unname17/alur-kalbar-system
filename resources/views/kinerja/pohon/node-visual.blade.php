@php
    $isCross = str_contains($node->nama_kinerja, '[CROSS_CUTTING]');
    $cleanName = str_replace('[CROSS_CUTTING]', '', $node->nama_kinerja);
    
    // Class untuk Kotak berdasarkan Jenis Kinerja
    $boxClass = 'box-normal';
    if($isCross) $boxClass = 'box-cross';
    else if($node->jenis_kinerja == 'sasaran_opd') $boxClass = 'bg-primary text-white';
    else if($node->jenis_kinerja == 'program') $boxClass = 'bg-info text-white';
    else if($node->jenis_kinerja == 'kegiatan') $boxClass = 'bg-warning text-dark';
    else if($node->jenis_kinerja == 'sub_kegiatan') $boxClass = 'bg-secondary text-white';
    else if($node->jenis_kinerja == 'skp') $boxClass = 'bg-dark text-white'; // SKP Warna Gelap
    else if($node->jenis_kinerja == 'rencana_aksi') $boxClass = 'bg-white border-primary text-primary';
@endphp

<li>
    <div class="node-wrapper">
        <a href="javascript:void(0)" class="{{ $boxClass }} p-2 rounded shadow-sm d-block text-decoration-none" style="min-width: 150px">
            <div class="node-title fw-bold" style="font-size: 0.8rem;">{{ $cleanName }}</div>
            <div class="node-detail small" style="font-size: 0.7rem;">
                @if($node->indikators->count() > 0)
                   Tgt: {{ $node->indikators->first()->target }} {{ $node->indikators->first()->satuan }}
                @elseif($node->anggaran > 0)
                   Rp {{ number_format($node->anggaran, 0, ',', '.') }}
                @endif
            </div>
        </a>
    </div>

    @if($node->children->count() > 0)
        <ul>
            @foreach($node->children as $child)
                @include('kinerja.pohon.node-visual', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>