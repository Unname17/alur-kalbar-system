<div class="section-title">I. Identitas Sub Kegiatan (Perencanaan)</div>
<div class="p-3 mb-4 rounded bg-light border">
    <div class="row mb-2">
        <div class="col-md-3 text-muted small fw-bold">SUB KEGIATAN:</div>
        <div class="col-md-9 fw-bold text-primary">{{ $subKegiatan->nama_kinerja }}</div>
    </div>
    <div class="row">
        <div class="col-md-3 text-muted small fw-bold">PAGU ANGGARAN:</div>
        <div class="col-md-9 fw-bold text-success">Rp {{ number_format($subKegiatan->anggaran, 0, ',', '.') }}</div>
    </div>
</div>
<input type="hidden" name="pohon_kinerja_id" value="{{ $subKegiatan->id }}">