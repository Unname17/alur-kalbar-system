@extends('kinerja.pohon.index') {{-- Sesuaikan dengan jalur file master Anda --}}

@section('page_title', $pageTitle)

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-bottom border-light">
        <h6 class="m-0 fw-bold text-danger text-uppercase tracking-widest" style="font-size: 0.75rem;">
            <i class="fas fa-exclamation-triangle me-2"></i> Data Perlu Perbaikan
        </h6>
    </div>
    
    <div class="list-group list-group-flush" style="min-height: 400px;">
        @forelse($dataRevisi as $item)
            <div class="list-group-item p-4 border-bottom border-light revisi-item">
                <div class="row">
                    <div class="col-md-9">
                        <span class="badge bg-danger-subtle text-danger mb-2 text-uppercase" style="font-size: 0.6rem; font-weight: 800;">
                            {{ str_replace('_', ' ', $item->jenis_kinerja) }} - DITOLAK
                        </span>
                        <h6 class="fw-bold text-dark mb-1">{{ $item->nama_kinerja }}</h6>
                        <small class="text-muted">Tanggal Input: {{ $item->created_at->format('d M Y') }}</small>
                        
                        {{-- Menampilkan Catatan Penolakan agar staf tahu apa yang harus diperbaiki --}}
                        <div class="mt-3 p-3 bg-danger-subtle border-start border-danger border-4 rounded-end">
                            <small class="fw-bold text-danger d-block mb-1">CATATAN REVISI:</small>
                            <p class="small text-danger mb-0 font-italic">"{{ $item->catatan_penolakan ?? 'Nomenklatur atau indikator belum sesuai, silakan diperbaiki.' }}"</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 text-md-end mt-3 mt-md-0 d-flex align-items-center justify-content-md-end">
                        {{-- Memanggil fungsi editDetail yang sudah ada di controller Anda --}}
                        <button onclick="bukaModalEdit('{{ $item->id }}')" class="btn btn-danger btn-sm px-4 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-edit me-2"></i> Perbaiki Data
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-5 text-center text-muted">
                <i class="fas fa-check-circle fa-3x mb-3 opacity-20"></i>
                <h6 class="fw-bold">Tidak ada data yang ditolak.</h6>
                <p class="small">Semua ajukan baru Anda sudah disetujui atau masih dalam antrean.</p>
            </div>
        @endforelse
    </div>

    <div class="card-footer bg-white border-0 py-3">
        {{ $dataRevisi->links() }}
    </div>
</div>

<style>
    .revisi-item { transition: 0.2s; }
    .revisi-item:hover { background-color: #fff5f5; }
</style>
@endsection