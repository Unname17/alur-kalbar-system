<!-- @extends('kinerja.pohon.index')
@section('page_title', 'Audit Trail Aktivitas')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-primary"><i class="fas fa-history me-2"></i> Log Aktivitas Pengguna</h6>
        <form action="{{ route('kinerja.log.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari aktivitas..." value="{{ request('search') }}">
            <button class="btn btn-primary btn-sm px-3">Filter</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="bg-light text-primary">
                    <tr>
                        <th class="ps-3">Waktu</th>
                        <th>Pelaku (User)</th>
                        <th>Instansi (OPD)</th>
                        <th>Aktivitas</th>
                        <th>Modul</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="ps-3 text-muted">{{ $log->created_at->format('d/m/y H:i') }}</td>
                        <td><span class="fw-bold">{{ $log->getNamaUser() }}</span></td>
                        <td class="small">{{ $log->getNamaOpd() }}</td>
                        <td>
                            <span class="badge bg-outline-primary border text-primary px-2">{{ $log->aktivitas }}</span>
                            <div class="small text-muted mt-1">{{ Str::limit($log->deskripsi, 50) }}</div>
                        </td>
                        <td><span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $log->modul }}</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light rounded-circle" onclick="showDetail({{ $log->id }})">
                                <i class="fas fa-eye text-primary"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>

{{-- MODAL DETAIL LOG --}}
<div class="modal fade" id="modalLog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title fw-bold">Detail Payload Request</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <label class="small fw-bold">User Agent (Perangkat):</label>
                <div id="ua-text" class="alert alert-secondary py-2 small mb-3"></div>
                
                <label class="small fw-bold">Data JSON (Payload):</label>
                <pre id="payload-json" class="p-3 bg-white border rounded small overflow-auto" style="max-height: 400px;"></pre>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
function showDetail(id) {
    $.get("{{ url('kinerja/log') }}/" + id, function(data) {
        $('#ua-text').text(data.user_agent);
        $('#payload-json').text(JSON.stringify(data.payload, null, 4));
        new bootstrap.Modal(document.getElementById('modalLog')).show();
    });
}
</script>
@endpush -->