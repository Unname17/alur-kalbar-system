<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $viewTitle }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .form-select option { padding: 10px; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-4">
    <div class="row">
        
        <div class="col-md-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-key me-2"></i> Beri Akses Input</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('kinerja.akses.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Target Perangkat Daerah</label>
                            <select name="opd_id" class="form-select" required>
                                <option value="">-- Pilih Dinas / Badan --</option>
                                @foreach($listOpd as $opd)
                                    <option value="{{ $opd->id }}">
                                        {{ $opd->nama_perangkat_daerah }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-xs">Pilih OPD yang akan diizinkan menginput.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Induk Kinerja (Parent)</label>
                            <select name="parent_id_allowed" class="form-select" required>
                                <option value="">-- Pilih Node Induk --</option>
                                @foreach($allNodes as $node)
                                    <option value="{{ $node->id }}">
                                        {{ $node->nama_kinerja }} 
                                        ({{ ucfirst(str_replace('_', ' ', $node->jenis_kinerja)) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-xs">OPD tersebut hanya bisa menambah data DI BAWAH node ini.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Jenis Kinerja Diizinkan</label>
                            <select name="jenis_kinerja_allowed" class="form-select">
                                <option value="sasaran">Sasaran</option>
                                <option value="program">Program</option>
                                <option value="kegiatan">Kegiatan</option>
                                <option value="sub_kegiatan">Sub Kegiatan</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Simpan Aturan
                        </button>
                    </form>
                    
                    <a href="{{ route('kinerja.pohon') }}" class="btn btn-outline-secondary w-100 mt-3">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Pohon
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">Daftar Akses Aktif</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="30%">Target OPD</th>
                                <th width="40%">Parent Dibuka</th>
                                <th>Jenis Izin</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rules as $rule)
                                @php
                                    // Cari Nama OPD dari listOpd berdasarkan ID (Manual Lookup di Blade)
                                    $namaOpd = 'OPD ID: ' . $rule->opd_id; // Default
                                    $opdData = $listOpd->firstWhere('id', $rule->opd_id);
                                    if($opdData) {
                                        $namaOpd = $opdData->nama_perangkat_daerah;
                                    }
                                @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                        {{ $namaOpd }}
                                    </div>
                                    <span class="badge bg-light text-secondary border">Role: {{ $rule->role_target }}</span>
                                </td>
                                <td>
                                    @if($rule->parentNode)
                                        <div class="text-primary fw-bold">{{ $rule->parentNode->nama_kinerja }}</div>
                                        <small class="text-muted">
                                            Jenis: {{ ucfirst($rule->parentNode->jenis_kinerja) }}
                                        </small>
                                    @else
                                        <span class="text-danger italic">Node Induk Terhapus</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst(str_replace('_', ' ', $rule->jenis_kinerja_allowed)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('kinerja.akses.delete', $rule->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mencabut akses ini?');">
                                        @csrf 
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus Akses">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-lock fa-3x mb-3 text-secondary opacity-25"></i><br>
                                    Belum ada aturan akses yang dibuat.<br>
                                    Semua OPD saat ini <strong>terkunci</strong> (tidak bisa input).
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>