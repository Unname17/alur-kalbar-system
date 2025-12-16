@php
    // --- MODE ARRAY: Gunakan kurung siku ['key'] ---
    
    $nodeName = $node['nama_kinerja'] ?? 'Tanpa Nama';
    $jenis = $node['jenis_kinerja'] ?? '';
    $status = $node['status'] ?? 'disetujui';
    
    // Cek Cross Cutting
    $isCross = str_contains($nodeName, '[CROSS_CUTTING]');
    $cleanName = str_replace('[CROSS_CUTTING]', '', $nodeName);

    // Warna Border & Badge
    $colorClass = 'border-secondary';
    $badgeClass = 'bg-secondary';

    if($isCross) {
        $colorClass = 'border-danger'; $badgeClass = 'bg-danger';
    } elseif ($jenis == 'sasaran_daerah') { $colorClass = 'border-success'; $badgeClass = 'bg-success'; }
    elseif ($jenis == 'sasaran_opd') { $colorClass = 'border-primary'; $badgeClass = 'bg-primary'; }
    elseif ($jenis == 'program') { $colorClass = 'border-info'; $badgeClass = 'bg-info text-dark'; }
    elseif ($jenis == 'kegiatan') { $colorClass = 'border-warning'; $badgeClass = 'bg-warning text-dark'; }

    // Ambil Data Detail (Gunakan Null Coalescing ??)
    $prog = $node['detail_program'] ?? null;
    $keg  = $node['detail_kegiatan'] ?? null;
    $sub  = $node['detail_sub_kegiatan'] ?? null;
@endphp

<div class="mb-2">
    <details open>
        <summary class="p-2 border rounded {{ $colorClass }} bg-white shadow-sm d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                {{-- BADGE JENIS --}}
                <span class="badge {{ $badgeClass }}">
                    {{ $isCross ? 'LINTAS SEKTOR' : strtoupper(str_replace('_', ' ', $jenis)) }}
                </span>
                
                {{-- NAMA KINERJA --}}
                <span class="fw-bold {{ $isCross ? 'text-danger' : 'text-dark' }}">
                    {{ $cleanName }}
                </span>

                {{-- BADGE STATUS (Jika bukan disetujui) --}}
                @if($status == 'pengajuan')
                    <span class="badge bg-warning text-dark">PENGAJUAN</span>
                @elseif($status == 'ditolak')
                    <span class="badge bg-danger">DITOLAK</span>
                @endif
            </div>

            {{-- ANGGARAN (Jika ada) --}}
            @if($sub && isset($sub['anggaran']))
                <span class="badge bg-light text-dark border">
                    Rp {{ number_format($sub['anggaran'], 0, ',', '.') }}
                </span>
            @endif
        </summary>

        {{-- AREA DETAIL --}}
        <div class="ms-4 mt-2 p-3 bg-light rounded border-start border-3 {{ $colorClass }}" style="font-size: 0.9rem;">
            @if($prog)
                <div class="row">
                    <div class="col-md-8"><strong>Indikator:</strong> {{ $prog['indikator_program'] }}</div>
                    <div class="col-md-4"><strong>Target:</strong> {{ $prog['target_program'] }} {{ $prog['satuan_target'] }}</div>
                </div>
            @elseif($keg)
                 <div class="row">
                    <div class="col-md-8"><strong>Indikator:</strong> {{ $keg['indikator_kegiatan'] }}</div>
                    <div class="col-md-4"><strong>Target:</strong> {{ $keg['target_kegiatan'] }} {{ $keg['satuan_target'] }}</div>
                </div>
            @elseif($sub)
                 <div class="row">
                    <div class="col-md-6"><strong>Indikator:</strong> {{ $sub['indikator_sub_kegiatan'] }}</div>
                    <div class="col-md-3"><strong>Target:</strong> {{ $sub['target_sub_kegiatan'] }} {{ $sub['satuan_target'] }}</div>
                    <div class="col-md-3"><strong>PJ:</strong> {{ $sub['penanggung_jawab'] }}</div>
                </div>
            @else
                <em class="text-muted">Tidak ada detail indikator.</em>
            @endif
        </div>

        {{-- REKURSIF CHILDREN (Looping Anak) --}}
        @if(isset($node['children']) && is_array($node['children']) && count($node['children']) > 0)
            <div class="ms-4 mt-2 ps-3 border-start">
                @foreach($node['children'] as $child)
                    @include('kinerja.pohon.node-list', ['node' => $child])
                @endforeach
            </div>
        @endif
    </details>
</div>