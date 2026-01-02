@extends('kinerja.pohon.index')

@section('page_title', 'Log Aktivitas Sistem')

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6">
    
    <div class="flex justify-between items-center px-4">
        <div>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Audit Trail / Log</h3>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Pantau seluruh aktivitas user di modul ini</p>
        </div>
        <div class="flex gap-2">
            <span class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold">
                Total Record: {{ $logs->total() }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-8">
            <table class="w-full">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">
                        <th class="pb-4 pl-4">Waktu & IP</th>
                        <th class="pb-4">Aktor (User)</th>
                        <th class="pb-4">Aktivitas</th>
                        <th class="pb-4">Detail Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($logs as $log)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 pl-4 align-top w-48">
                            <span class="text-xs font-black text-slate-700 block">
                                {{ $log->created_at->format('d M Y') }}
                            </span>
                            <span class="text-[10px] font-bold text-indigo-500 block mb-1">
                                {{ $log->created_at->format('H:i:s') }}
                            </span>
                            <span class="text-[9px] text-slate-400 font-mono bg-slate-100 px-1 rounded">
                                {{ $log->ip_address }}
                            </span>
                        </td>
                        <td class="py-4 align-top w-64">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                    {{ substr($log->user_nama, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">{{ $log->user_nama }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $log->perangkatDaerah->nama_pd ?? 'Admin Pusat' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 align-top w-40">
                            @php
                                $colors = [
                                    'CREATE' => 'bg-emerald-100 text-emerald-600',
                                    'UPDATE' => 'bg-amber-100 text-amber-600',
                                    'DELETE' => 'bg-rose-100 text-rose-600',
                                    'ACCESS' => 'bg-slate-100 text-slate-600',
                                    'CONFIG' => 'bg-purple-100 text-purple-600',
                                ];
                                $badge = $colors[$log->aksi] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase {{ $badge }} mb-1 inline-block">
                                {{ $log->aksi }}
                            </span>
                            <p class="text-[10px] font-bold text-slate-500">{{ $log->modul }}</p>
                        </td>
                        <td class="py-4 align-top">
                            <p class="text-xs font-medium text-slate-600 leading-relaxed">
                                {{ $log->deskripsi }}
                            </p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection