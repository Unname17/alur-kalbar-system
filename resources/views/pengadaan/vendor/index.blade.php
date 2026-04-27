@extends('pengadaan.layout')

@section('header_title', 'Database Vendor')
@section('title', 'Daftar Penyedia')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-black text-white">Manajemen Penyedia Terdaftar</h2>
            <p class="text-slate-500 text-xs mt-1 uppercase tracking-widest font-bold">Total: {{ $vendors->count() }} Vendor Terverifikasi</p>
        </div>
        <a href="{{ route('pengadaan.vendor.create') }}" class="px-6 py-3 bg-cyan-600 hover:bg-cyan-500 text-white rounded-2xl font-bold shadow-lg transition-all flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Tambah Vendor Baru
        </a>
    </div>

    {{-- SEKSI PENCARIAN BARU --}}
    <div class="mb-8 p-6 bg-slate-950/40 rounded-[2rem] border border-slate-800 shadow-inner">
        <form action="{{ route('pengadaan.vendor.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Cari berdasarkan Nama Perusahaan, Direktur, atau NPWP..." 
                       class="w-full bg-slate-900 border border-slate-700 rounded-2xl py-4 pl-12 pr-4 text-white text-sm focus:border-cyan-500 outline-none transition-all shadow-2xl">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-8 py-4 bg-slate-800 hover:bg-slate-700 text-cyan-400 border border-cyan-500/20 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all">
                    Cari Data
                </button>
                @if($search)
                    <a href="{{ route('pengadaan.vendor.index') }}" class="px-6 py-4 bg-rose-900/20 hover:bg-rose-900/40 text-rose-500 rounded-2xl flex items-center justify-center transition-all shadow-lg" title="Reset Pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="glass-procurement rounded-[2rem] overflow-hidden border border-slate-800">
        <table class="w-full text-sm text-left">
            <thead class="text-[10px] text-slate-500 uppercase bg-slate-950/50 font-black tracking-widest">
                <tr>
                    <th class="px-6 py-5">Nama Perusahaan</th>
                    <th class="px-6 py-5">PIC / Direktur</th>
                    <th class="px-6 py-5">Kontak & NPWP</th>
                    <th class="px-6 py-5">Alamat</th>
                    <th class="px-6 py-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @foreach($vendors as $vendor)
                <tr class="hover:bg-cyan-500/5 transition-all">
                    <td class="px-6 py-5">
                        <div class="font-bold text-white">{{ $vendor->nama_perusahaan }}</div>
                        <div class="text-[10px] text-cyan-400 font-bold uppercase">{{ $vendor->bentuk_usaha ?? 'PT' }}</div>
                    </td>
                    <td class="px-6 py-5">
                        {{-- GANTI nama_pic MENJADI nama_direktur --}}
                        <div class="text-slate-300 font-bold">{{ $vendor->nama_direktur ?? '-' }}</div>
                        {{-- GANTI jabatan_pic MENJADI jabatan_direktur --}}
                        <div class="text-[10px] text-slate-500">{{ $vendor->jabatan_direktur ?? 'Direktur' }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-white text-xs font-mono">{{ $vendor->npwp ?? '-' }}</div>
                        <div class="text-[10px] text-slate-500 mt-1">{{ $vendor->email ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <p class="text-slate-400 text-xs line-clamp-2 max-w-xs">{{ $vendor->alamat ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('pengadaan.vendor.edit', $vendor->id) }}" class="p-2 bg-slate-800 hover:bg-cyan-600 rounded-lg text-white transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('pengadaan.vendor.destroy', $vendor->id) }}" method="POST" onsubmit="return confirm('Hapus vendor ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-slate-800 hover:bg-rose-600 rounded-lg text-white transition-all">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($vendors->isEmpty())
            <div class="p-20 text-center text-slate-600 italic border-t border-slate-800">
                <i class="fas fa-search-minus text-4xl mb-4 block opacity-20"></i>
                Data vendor dengan kata kunci "{{ $search }}" tidak ditemukan.
            </div>
        @endif
        
    </div>
    <p class="text-slate-500 text-xs mt-1 uppercase tracking-widest font-bold">Total: {{ $vendors->total() }} Vendor Terverifikasi</p>
</div>
@endsection