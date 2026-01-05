@extends('rka.layout')

@section('title', 'Step 3: Data Pelengkap')
@section('header_title', 'Step 3: Layanan & Tim Anggaran')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Stepper Sederhana --}}
    <div class="flex items-center gap-4 mb-8 bg-slate-900/50 p-4 rounded-3xl border border-slate-800">
        <div class="flex items-center gap-2 opacity-50"><span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">1</span> <span class="text-xs text-white">Identitas</span></div>
        <i class="fas fa-chevron-right text-slate-700 text-[10px]"></i>
        <div class="flex items-center gap-2 opacity-50"><span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">2</span> <span class="text-xs text-white">Rincian</span></div>
        <i class="fas fa-chevron-right text-slate-700 text-[10px]"></i>
        <div class="flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-[10px]">3</span> <span class="text-xs text-white font-bold">Pelengkap</span></div>
    </div>

    <form action="{{ route('rka.store_step3', $rka->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Bagian Layanan & SPM --}}
            <div class="glass-card p-8 rounded-[2.5rem] bg-slate-900 border border-slate-800">
                <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-concierge-bell text-indigo-400"></i> Layanan & SPM
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Jenis Layanan</label>
                        <input type="text" name="jenis_layanan" value="{{ $rka->jenis_layanan }}" placeholder="Boleh dikosongkan..." class="w-full bg-slate-800 border border-slate-700 rounded-2xl p-4 text-sm text-white focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">SPM (Standar Pelayanan Minimal)</label>
                        <textarea name="spm" rows="3" placeholder="Boleh dikosongkan..." class="w-full bg-slate-800 border border-slate-700 rounded-2xl p-4 text-sm text-white focus:border-indigo-500 outline-none">{{ $rka->spm }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Bagian Tim Anggaran --}}
            <div class="glass-card p-8 rounded-[2.5rem] bg-slate-900 border border-slate-800">
                <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-users text-indigo-400"></i> Tim Anggaran (TAPD)
                </h3>
                <p class="text-[10px] text-slate-500 mb-4 italic">*Jika kosong, hasil print akan tertulis "Data Kosong"</p>
                
                <div id="wrapper-tim" class="space-y-3">
                    @php $tim = json_decode($rka->tim_anggaran) ?? []; @endphp
                    @forelse($tim as $item)
                    <div class="flex gap-2 item-tim">
                        <input type="text" name="tim_nama[]" value="{{ $item->nama }}" placeholder="Nama Anggota" class="flex-1 bg-slate-800 border border-slate-700 rounded-xl p-3 text-xs text-white">
                        <input type="text" name="tim_nip[]" value="{{ $item->nip }}" placeholder="NIP" class="flex-1 bg-slate-800 border border-slate-700 rounded-xl p-3 text-xs text-white">
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 px-2"><i class="fas fa-times"></i></button>
                    </div>
                    @empty
                    <div class="text-center py-4 border-2 border-dashed border-slate-800 rounded-2xl">
                        <span class="text-xs text-slate-600">Belum ada data tim anggaran</span>
                    </div>
                    @endforelse
                </div>

                <button type="button" onclick="addTim()" class="w-full mt-4 py-3 border border-indigo-500/30 text-indigo-400 rounded-xl text-xs font-bold hover:bg-indigo-500/10 transition-all">
                    + Tambah Anggota Tim
                </button>
            </div>
        </div>

        <div class="flex justify-between items-center bg-slate-900 p-6 rounded-[2rem] border border-slate-800">
            <a href="{{ route('rka.manage', $rka->id) }}" class="text-slate-500 text-xs font-bold hover:text-white">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Rincian Belanja
            </a>
            <button type="submit" class="px-10 py-4 bg-emerald-600 text-white rounded-2xl font-bold shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                Simpan & Selesai <i class="fas fa-check-circle ml-2"></i>
            </button>
        </div>
    </form>
</div>

<script>
function addTim() {
    const wrapper = document.getElementById('wrapper-tim');
    const div = document.createElement('div');
    div.className = 'flex gap-2 item-tim';
    div.innerHTML = `
        <input type="text" name="tim_nama[]" placeholder="Nama Anggota" class="flex-1 bg-slate-800 border border-slate-700 rounded-xl p-3 text-xs text-white">
        <input type="text" name="tim_nip[]" placeholder="NIP" class="flex-1 bg-slate-800 border border-slate-700 rounded-xl p-3 text-xs text-white">
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 px-2"><i class="fas fa-times"></i></button>
    `;
    wrapper.appendChild(div);
}
</script>
@endsection