@php
    $hasChild = $node->children->count() > 0;
    $padding = ($level ?? 0) * 20;
    $isPdf = $isPdf ?? false; // Cek apakah sedang proses cetak PDF
@endphp

@if($isPdf)
    {{-- STRUKTUR UNTUK PDF (TABLE) --}}
    <tr>
        <td style="text-align: center; font-weight: bold; font-size: 8px;">{{ strtoupper(str_replace('_',' ',$node->jenis_kinerja)) }}</td>
        <td style="padding-left: {{ $padding + 5 }}px;">{{ $node->nama_kinerja }}</td>
        <td>
            @foreach($node->indikators as $ind)
                <div style="border-bottom: 0.5px solid #ccc; margin-bottom: 2px;">• {{ $ind->indikator }}</div>
            @endforeach
        </td>
        <td style="text-align: center;">
            @foreach($node->indikators as $ind)
                <div style="border-bottom: 0.5px solid #ccc; margin-bottom: 2px;">{{ $ind->target }} {{ $ind->satuan }}</div>
            @endforeach
        </td>
        <td style="text-align: right;">{{ $node->anggaran > 0 ? number_format($node->anggaran,0,',','.') : '-' }}</td>
    </tr>
@else
    {{-- STRUKTUR UNTUK WEB (GRID/DROPDOWN) --}}
    <div class="m-item">
        <div class="m-row lvl-{{ $node->jenis_kinerja }}" id="row-{{ $node->id }}">
            <div class="m-col fw-bold text-center small text-uppercase">{{ str_replace('_',' ',$node->jenis_kinerja) }}</div>
            <div class="m-col" style="padding-left: {{ $padding + 10 }}px">
                @if($hasChild)
                    <span class="btn-drop" onclick="toggleRow('{{ $node->id }}')"><i class="fas fa-chevron-down"></i></span>
                @else
                    <span class="btn-drop text-muted" style="opacity:0.3"><i class="fas fa-minus"></i></span>
                @endif
                {{ $node->nama_kinerja }}
            </div>
            <div class="m-col flex-column align-items-start">
                @foreach($node->indikators as $ind)
                    <div class="small border-bottom w-100 mb-1 pb-1">{{ $ind->indikator }}</div>
                @endforeach
            </div>
            <div class="m-col flex-column text-center">
                @foreach($node->indikators as $ind)
                    <div class="small border-bottom w-100 mb-1 pb-1">{{ $ind->target }} {{ $ind->satuan }}</div>
                @endforeach
            </div>
            <div class="m-col text-end fw-bold">
                {{ $node->anggaran > 0 ? 'Rp '.number_format($node->anggaran,0,',','.') : '-' }}
            </div>
        </div>

        @if($hasChild)
            <div id="child-{{ $node->id }}" style="display: block;">
                @foreach($node->children as $child)
                    @include('kinerja.pohon.partial-cascading-row', ['node' => $child, 'level' => ($level ?? 0) + 1, 'isPdf' => false])
                @endforeach
            </div>
        @endif
    </div>
@endif

{{-- Rekursi untuk PDF --}}
@if($isPdf && $hasChild)
    @foreach($node->children as $child)
        @include('kinerja.pohon.partial-cascading-row', ['node' => $child, 'level' => ($level ?? 0) + 1, 'isPdf' => true])
    @endforeach
@endif