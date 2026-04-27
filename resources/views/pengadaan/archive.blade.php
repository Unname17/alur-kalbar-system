@extends('pengadaan.layout')

@section('header_title', 'Laporan & Arsip')
@section('title', 'Arsip Kontrak Selesai')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- HEADER & STATISTIK DASHBOARD --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h2 class="text-2xl font-black text-white flex items-center gap-3">
                <i class="fas fa-archive text-cyan-500"></i> Arsip Kontrak Terfinalisasi
            </h2>
            <p class="text-slate-500 text-sm mt-1">Siklus hidup data pengadaan yang telah mencapai tahap tanda tangan Doc 10.</p>
        </div>
    </div>

    {{-- CARD STATISTIK AKUMULASI --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        {{-- Total Belanja --}}
        <div class="glass-procurement p-8 rounded-[2rem] border border-emerald-500/20 bg-gradient-to-br from-slate-900 to-emerald-950/20 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl -mr-8 -mt-8 group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-2 italic">Total Realisasi Belanja</div>
                <div class="text-3xl font-mono font-bold text-white leading-tight">
                    Rp {{ number_format($totalSpending, 0, ',', '.') }}
                </div>
                <div class="text-[9px] text-slate-500 mt-4 uppercase font-bold tracking-tighter">Berdasarkan Akumulasi Nilai Kontrak Final (Doc 10)</div>
            </div>
        </div>

        {{-- Jumlah Kontrak --}}
        <div class="glass-procurement p-8 rounded-[2rem] border border-cyan-500/20 bg-slate-900/40 relative overflow-hidden group">
             <div class="absolute top-0 right-0 w-24 h-24 bg-cyan-500/5 rounded-full blur-2xl -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-cyan-400 uppercase tracking-widest mb-2 italic">Paket Terarsip</div>
                <div class="text-4xl font-black text-white">{{ $totalPackages }}</div>
                <div class="text-[9px] text-slate-500 mt-4 uppercase font-bold tracking-tighter">Kontrak yang Telah Diarsip</div>
            </div>
        </div>

        {{-- Pejabat Penandatangan Utama --}}
        <div class="glass-procurement p-8 rounded-[2rem] border border-slate-800 bg-slate-900/40">
            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 italic">Pejabat Pengesah Utama</div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-cyan-900/50 flex items-center justify-center text-cyan-400 border border-cyan-700 shadow-lg"><i class="fas fa-user-tie"></i></div>
                <div>
                    <div class="text-white font-bold text-sm leading-tight">Samuel, S.E., M.Si.</div>
                    <div class="text-[9px] text-slate-500 mt-0.5 font-bold tracking-tighter italic">NIP: 197005121996031004</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL ARSIP (Sama seperti sebelumnya) --}}
    <div class="glass-procurement rounded-[2.5rem] overflow-hidden border border-slate-800 shadow-2xl">
        <table class="w-full text-sm text-left">
            <thead class="text-[10px] text-slate-500 uppercase bg-slate-950/80 font-black tracking-widest">
                <tr>
                    <th class="px-6 py-5">Identitas Kontrak</th>
                    <th class="px-6 py-5">Penyedia & Pimpinan</th>
                    <th class="px-6 py-5">Nilai Kontrak</th>
                    <th class="px-6 py-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($packages as $pkg)
                <tr class="hover:bg-cyan-500/5 transition-all">
                    <td class="px-6 py-5">
                        <div class="font-bold text-white uppercase text-xs tracking-tighter">{{ $pkg->contract->nomor_sp }}</div>
                        <div class="text-[10px] text-slate-400 mt-1 italic">{{ $pkg->nama_paket }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-cyan-400 font-bold">{{ $pkg->contract->vendor->nama_perusahaan }}</div>
                        <div class="text-[10px] text-slate-500">{{ $pkg->contract->vendor->nama_direktur }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-emerald-400 font-mono font-bold text-base">
                            Rp {{ number_format($pkg->contract->nilai_kontrak_final, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <a href="{{ route('pengadaan.print.doc10', $pkg->id) }}" target="_blank" class="px-6 py-2.5 bg-slate-800 hover:bg-emerald-600 rounded-xl text-white text-[10px] font-black transition-all shadow-lg">
                            <i class="fas fa-print mr-2"></i> CETAK ULANG DOC 10
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-20 text-center text-slate-600 italic">Belum ada kontrak yang difinalisasi untuk diarsip.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection