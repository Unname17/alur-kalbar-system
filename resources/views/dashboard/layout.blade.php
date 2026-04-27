<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Intelligence - Alur Kalbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        /* BASE THEME */
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0B1120; color: #f8fafc; overflow-x: hidden; }
        
        /* SIDEBAR STYLING */
        .sidebar-container { background: #0F172A; border-right: 1px solid #1E293B; }
        .nav-item { transition: all 0.2s ease; border-left: 3px solid transparent; }
        .nav-item:hover { background: rgba(30, 41, 59, 0.5); color: #fff; }
        .nav-active { background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0) 100%); border-left-color: #6366F1; color: #fff; }
        .logo-box { background: linear-gradient(135deg, #4F46E5 0%, #4338CA 100%); }
        .profile-card { background: #1E293B; border: 1px solid #334155; }
    </style>
</head>
<body class="antialiased">

    <div class="flex h-screen overflow-hidden">
        {{-- SIDEBAR: z-30 (Di atas konten biasa, tapi di bawah Modal z-50/z-9999) --}}
        <aside class="w-64 sidebar-container flex flex-col z-30 shrink-0 relative">
            
            <div class="p-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg logo-box flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h1 class="text-white font-bold text-lg tracking-tight">ALUR KALBAR</h1>
            </div>
            
            <nav class="flex-1 px-4 space-y-1 mt-4">
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Main Menu</p>
                <a href="{{ route('executive.index') }}" class="nav-item flex items-center gap-3 px-4 py-3 rounded-r-lg text-sm font-medium {{ request()->routeIs('executive.index') ? 'nav-active' : 'text-slate-400' }}">
                    <i class="fas fa-chart-line w-5"></i> Dashboard
                </a>
                <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-r-lg text-sm font-medium text-slate-400">
                    <i class="fas fa-calculator w-5"></i> Perencanaan (RKA)
                </a>
                <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-r-lg text-sm font-medium text-slate-400">
                    <i class="fas fa-cart-shopping w-5"></i> Pengadaan
                </a>
                <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-r-lg text-sm font-medium text-slate-400">
                    <i class="fas fa-chart-simple w-5"></i> Laporan Kinerja
                </a>
                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">System</p>
                    <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-r-lg text-sm font-medium text-slate-400"><i class="fas fa-cog w-5"></i> Settings</a>
                </div>
            </nav>

            <div class="p-4">
                <div class="profile-card p-3 rounded-xl flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(Auth::user()->nama_lengkap ?? 'P', 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->nama_lengkap ?? 'Pimpinan' }}</p>
                        <p class="text-[10px] text-slate-400">Executive Admin</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- 
            MAIN CONTENT: 
            1. Dihapus 'z-0' agar tidak mengunci modal di layer bawah.
            2. Dihapus 'bg-[#0B1120]' agar transparan (sehingga pattern z-[-1] terlihat di atas body).
        --}}
        <main class="flex-1 overflow-y-auto relative">
            <div class="absolute inset-0 z-[-1] opacity-[0.03]" style="background-image: linear-gradient(#334155 1px, transparent 1px), linear-gradient(to right, #334155 1px, transparent 1px); background-size: 40px 40px;"></div>
            
            <div class="relative z-10 p-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>