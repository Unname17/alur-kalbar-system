@php
    $isCross = str_contains($node->nama_kinerja, '[CROSS_CUTTING]');
    $cleanName = str_replace('[CROSS_CUTTING]', '', $node->nama_kinerja);
    
    // Class untuk Kotak (Link)
    $boxClass = 'box-normal';
    if($isCross) $boxClass = 'box-cross'; // Kotak Merah Putus-putus
    else if($node->jenis_kinerja == 'sasaran_daerah') $boxClass = 'bg-success text-white';
    else if($node->jenis_kinerja == 'sasaran_opd') $boxClass = 'bg-primary text-white';
    else if($node->jenis_kinerja == 'program') $boxClass = 'bg-info text-white';
    else if($node->jenis_kinerja == 'kegiatan') $boxClass = 'bg-warning text-dark';
    else $boxClass = 'bg-secondary text-white';

    // Class untuk List Item (Wrapper) - PENTING UNTUK GARIS
    $liClass = $isCross ? 'li-cross' : '';
@endphp

<li class="{{ $liClass }}">
    <div class="node-wrapper">
        <a href="javascript:void(0)" class="{{ $boxClass }}">
            <div class="node-title">{{ $cleanName }}</div>
            
            @if(!$isCross)
                <div class="node-detail">
                    @if($node->detailProgram)
                       Target: {{ $node->detailProgram->target_program }} {{ $node->detailProgram->satuan_target }}
                    @elseif($node->detailKegiatan)
                       {{ $node->detailKegiatan->target_kegiatan }} {{ $node->detailKegiatan->satuan_target }}
                    @elseif($node->detailSubKegiatan)
                       Rp {{ number_format($node->detailSubKegiatan->anggaran) }}
                    @endif
                </div>
            @endif
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