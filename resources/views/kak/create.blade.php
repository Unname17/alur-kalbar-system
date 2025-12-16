@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <form action="{{ route('kak.store') }}" method="POST">
        @csrf
        <input type="hidden" name="pohon_kinerja_id" value="{{ $subKegiatan->id }}">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informasi Dasar (Berdasarkan Pohon Kinerja)</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Sub Kegiatan</label>
                    <p class="form-control-plaintext bg-light px-2">{{ $subKegiatan->nama_kinerja }}</p>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Judul KAK (What)</label>
                        <input type="text" name="judul_kak" class="form-control" placeholder="Contoh: Pengembangan Sistem E-Arsip..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama/Kode Proyek</label>
                        <input type="text" name="kode_proyek" class="form-control" placeholder="E-ARSIP-2024">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Landasan, Maksud, Tujuan & Manfaat</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Latar Belakang (Why)</label>
                    <textarea name="latar_belakang" class="form-control" rows="3" placeholder="Jelaskan alasan dan justifikasi..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Dasar Hukum</label>
                    <textarea name="dasar_hukum" class="form-control" rows="2" placeholder="Sebutkan peraturan yang relevan..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Maksud & Tujuan (Why)</label>
                    <textarea name="maksud_tujuan" class="form-control" rows="2" placeholder="Jelaskan maksud utama kegiatan..."></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg shadow">Simpan Dokumen KAK</button>
    </form>
</div>
@endsection