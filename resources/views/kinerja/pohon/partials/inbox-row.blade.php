@php
    $padding = $level * 25;
    $isPending = ($node->status === 'pengajuan');
@endphp
<div class="m-row {{ $isPending ? 'bg-light' : '' }}" style="{{ $isPending ? 'border-left: 5px solid #f6c23e;' : '' }}">
    <div class="m-col fw-bold text-center small text-uppercase" style="background: #f8f9fc; font-size: 0.7rem;">{{ str_replace('_',' ',$node->jenis_kinerja) }}</div>
    <div class="m-col ps-3" style="padding-left: {{ $padding + 15 }}px !important;">
        <div class="d-flex align-items-start">
            <i class="fas fa-level-up-alt fa-rotate-90 me-2 mt-1 text-muted small"></i>
            <div>
                <div class="{{ $isPending ? 'fw-bold text-primary' : 'text-secondary' }}">{{ $node->nama_kinerja }}</div>
                <small class="text-muted" style="font-size: 0.65rem;">ID: {{ $node->id }}</small>
            </div>
        </div>
    </div>
    <div class="m-col flex-column align-items-start">
        @foreach($node->indikators as $ind) <div class="small border-bottom w-100 pb-1">• {{ $ind->indikator }}</div> @endforeach
    </div>
    <div class="m-col flex-column text-center">
        @foreach($node->indikators as $ind) <div class="small border-bottom w-100 pb-1">{{ $ind->target }} {{ $ind->satuan }}</div> @endforeach
    </div>
    <div class="m-col justify-content-center">
        @if($isPending)
            <button class="btn btn-sm btn-primary py-1 px-3 shadow-sm" onclick="bukaModalValidasiInbox({{ $node->id }}, '{{ $node->nama_kinerja }}')">Periksa</button>
        @else
            <span class="badge {{ $node->status === 'disetujui' ? 'bg-success' : 'bg-secondary' }} small">{{ strtoupper($node->status) }}</span>
        @endif
    </div>
</div>
@if($node->children->count() > 0)
    @foreach($node->children as $child) @include('kinerja.partials.inbox-row', ['node' => $child, 'level' => $level + 1]) @endforeach
@endif