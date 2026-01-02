@php $padding = $level * 25; @endphp
<div class="m-row">
    <div class="m-col fw-bold text-center small text-uppercase" style="background: #f8f9fc;">
        {{ str_replace('_',' ',$node->jenis_kinerja) }}
    </div>
    <div class="m-col ps-3" style="padding-left: {{ $padding + 15 }}px !important;">
        {{ $node->nama_kinerja }}
    </div>
    <div class="m-col flex-column align-items-start">
        @foreach($node->indikators as $ind) 
            <div class="small border-bottom w-100 mb-1 pb-1">• {{ $ind->indikator }}</div> 
        @endforeach
    </div>
    <div class="m-col flex-column text-center">
        @foreach($node->indikators as $ind) 
            <div class="small border-bottom w-100 mb-1 pb-1">{{ $ind->target }} {{ $ind->satuan }}</div> 
        @endforeach
    </div>
    <div class="m-col justify-content-center">
        <span class="badge {{ $node->status === 'disetujui' ? 'bg-success' : 'bg-warning' }} small">
            {{ strtoupper($node->status) }}
        </span>
    </div>
</div>

@if($node->children->count() > 0)
    @foreach($node->children as $child)
        {{-- REKURSIF PATH: kinerja.pohon.partials.matriks-row --}}
        @include('kinerja.pohon.partials.matriks-row', ['node' => $child, 'level' => $level + 1])
    @endforeach
@endif