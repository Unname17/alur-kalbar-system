@extends('kinerja.pohon.index')

@section('title', 'Dashboard OPD')
@section('page_title', 'Ringkasan Kinerja Instansi')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Selamat Datang, {{ Auth::user()->nama_lengkap }}</h3>
            <p class="text-slate-500 text-sm font-medium mt-1">Pantau progres perencanaan program dan kegiatan instansi Anda.</p>
        </div>
        
        @if($is_locked)
            <div class="px-6 py-3 bg-red-50 text-red-600 rounded-2xl border border-red-100 flex items-center gap-3 animate-pulse">
                <i class="fas fa-lock text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Akses Input Dikunci Bappeda</span>
            </div>
        @else
            <div class="px-6 py-3 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100 flex items-center gap-3">
                <i class="fas fa-unlock text-sm"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Akses Input Terbuka</span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-4">
                <i class="fas fa-bullseye text-xl"></i>
            </div>
            <h4 class="text-3xl font-black text-slate-800">{{ $stats['tujuan'] }}</h4>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Total Tujuan OPD</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-4">
                <i class="fas fa-layer-group text-xl"></i>
            </div>
            <h4 class="text-3xl font-black text-slate-800">{{ $stats['program'] }}</h4>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Total Program</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 mb-4">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <h4 class="text-3xl font-black text-slate-800">{{ $stats['kegiatan'] }}</h4>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Total Kegiatan</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-pink-50 rounded-2xl flex items-center justify-center text-pink-600 mb-4">
                <i class="fas fa-clipboard-list text-xl"></i>
            </div>
            <h4 class="text-3xl font-black text-slate-800">{{ $stats['sub_kegiatan'] }}</h4>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Sub-Kegiatan</p>
        </div>
    </div>

    <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="max-w-xl">
                <h4 class="text-2xl font-black mb-4 leading-tight">Mulai Menyusun Pohon Kinerja Anda Sekarang</h4>
                <p class="text-slate-400 text-sm leading-relaxed mb-6">Gunakan fitur Visualisasi Cascading untuk memetakan hubungan antara tujuan strategis daerah dengan kegiatan teknis di lapangan secara otomatis dan interaktif.</p>
                
                <a href="{{ route('kinerja.pohon') }}" class="inline-flex items-center gap-3 bg-performance-green text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl shadow-emerald-500/20 active:scale-95 no-underline">
                    Masuk ke Pohon Kinerja <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-project-diagram text-[10rem] text-white/10 -rotate-12"></i>
            </div>
        </div>
        
        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-performance-green/10 rounded-full blur-3xl"></div>
    </div>
</div>
@endsection