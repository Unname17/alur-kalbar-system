@forelse($logs as $log)
<tr>
    <td class="ps-4">
        <div class="small fw-bold">{{ $log->created_at->format('d M Y') }}</div>
        <div class="small text-muted">{{ $log->created_at->format('H:i:s') }}</div>
    </td>
    <td>
        <div class="user-info-name">{{ $log->getNamaUser() }}</div>
        <div class="user-info-opd"><i class="fas fa-building me-1"></i> {{ $log->getNamaOpd() }}</div>
    </td>
    <td>
        {!! $log->getStatusLabel() !!}
        <div class="text-deskripsi mt-1 italic">{{ $log->deskripsi }}</div>
    </td>
    <td class="text-center">
        <span class="badge bg-light text-secondary border px-2" style="font-size: 0.6rem;">{{ $log->modul }}</span>
    </td>
    <td class="text-center">
        <div class="d-flex justify-content-center gap-1">
            {{-- Tombol Detail --}}
            <button class="btn btn-sm btn-outline-primary border-0 rounded-circle" 
                    onclick="showLogDetail({{ $log->id }})" title="Detail Payload">
                <i class="fas fa-eye"></i>
            </button>
            
            {{-- TOMBOL HAPUS MANUAL --}}
            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" 
                    onclick="deleteLogManual({{ $log->id }})" title="Hapus Permanen">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="5" class="py-5 text-center text-muted italic">Data tidak ditemukan.</td></tr>
@endforelse

<tr class="bg-white">
    <td colspan="5" class="p-3 border-0">
        <div class="d-flex justify-content-center ajax-pagination">
            {{ $logs->links() }}
        </div>
    </td>
</tr>