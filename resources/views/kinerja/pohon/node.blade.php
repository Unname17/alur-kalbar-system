<li class="tree-item">
    <div class="card p-3 mb-2 shadow-sm bg-{{ $node->jenis_kinerja }}">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <span class="badge badge-secondary text-uppercase mb-1" style="font-size: 0.7rem;">
                    {{ str_replace('_', ' ', $node->jenis_kinerja) }}
                </span>
                <h6 class="font-weight-bold m-0 text-dark">{{ $node->nama_kinerja }}</h6>
                
                {{-- Tampilkan Detail Indikator jika ada (Contoh Program) --}}
                @if($node->jenis_kinerja == 'program' && $node->detailProgram)
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-target"></i> Indikator: {{ $node->detailProgram->indikator_program }} 
                        ({{ $node->detailProgram->target_program }} {{ $node->detailProgram->satuan_target }})
                    </small>
                @endif
                
                {{-- Tampilkan Anggaran jika Sub Kegiatan --}}
                @if($node->jenis_kinerja == 'sub_kegiatan' && $node->detailSubKegiatan)
                    <small class="text-success d-block mt-1 font-weight-bold">
                        Rp {{ number_format($node->detailSubKegiatan->anggaran, 0, ',', '.') }}
                    </small>
                @endif
            </div>

            {{-- Tombol Aksi (Edit/Hapus) - Opsional --}}
            <div class="dropdown no-arrow">
                <button class="btn btn-link btn-sm text-gray-400" type="button">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- RECURSIVE: Cek apakah punya anak --}}
    @if($node->children->count() > 0)
        <ul class="tree-child">
            @foreach($node->children as $child)
                {{-- Panggil file ini lagi (diri sendiri) --}}
                @include('kinerja.pohon.node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>