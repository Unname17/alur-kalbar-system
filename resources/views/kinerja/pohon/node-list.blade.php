@php
    $nodeName = $node['nama_kinerja'] ?? 'Tanpa Nama';
    $jenis = $node['jenis_kinerja'] ?? '';
    $status = $node['status'] ?? 'disetujui';
    $cleanName = str_replace('[CROSS_CUTTING]', '', $nodeName);

    // Warna Badge
    $badgeClass = 'bg-secondary';
    if ($jenis == 'sasaran_opd') $badgeClass = 'bg-primary';
    elseif ($jenis == 'program') $badgeClass = 'bg-info text-dark';
    elseif ($jenis == 'kegiatan') $badgeClass = 'bg-warning text-dark';
    elseif ($jenis == 'skp') $badgeClass = 'bg-dark';
    elseif ($jenis == 'rencana_aksi') $badgeClass = 'bg-success';
@endphp

<div class="mb-2">
    <details {{ $jenis == 'rencana_aksi' ? '' : 'open' }}>
        <summary class="p-2 border rounded bg-white shadow-sm d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge {{ $badgeClass }}">{{ strtoupper(str_replace('_', ' ', $jenis)) }}</span>
                <span class="fw-bold">{{ $cleanName }}</span>
            </div>
            @if(isset($node['anggaran']) && $node['anggaran'] > 0)
                <span class="badge bg-light text-dark border">Rp {{ number_format($node['anggaran'], 0, ',', '.') }}</span>
            @endif
        </summary>

        <div class="ms-4 mt-2 p-3 bg-light rounded border-start border-3" style="font-size: 0.85rem;">
            @if(isset($node['indikators']) && count($node['indikators']) > 0)
                @foreach($node['indikators'] as $ind)
                    <div class="row border-bottom mb-1 pb-1">
                        <div class="col-md-8"><strong>Indikator:</strong> {{ $ind['indikator'] }}</div>
                        <div class="col-md-4"><strong>Target:</strong> {{ $ind['target'] }} {{ $ind['satuan'] }}</div>
                    </div>
                @endforeach
            @endif

            @if($jenis == 'rencana_aksi')
                <div class="mt-2 text-primary fw-bold small">
                    Target Tahunan: T1:{{ $node['target_t1'] }} | T2:{{ $node['target_t2'] }} | T3:{{ $node['target_t3'] }}
                </div>
            @endif
        </div>

        @if(isset($node['children']) && count($node['children']) > 0)
            <div class="ms-4 mt-2 ps-3 border-start">
                @foreach($node['children'] as $child)
                    @include('kinerja.pohon.node-list', ['node' => $child])
                @endforeach
            </div>
        @endif
    </details>
</div>