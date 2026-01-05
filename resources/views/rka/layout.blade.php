<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Budgeting RKA')</title>
    
    {{-- Tailwind CSS & Plugins --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0f172a; color: #cbd5e1; }
        .glass-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .sidebar-active { background: linear-gradient(90deg, #6366f1 0%, #4338ca 100%); color: white; box-shadow: 0 4px 20px -5px rgba(99, 102, 241, 0.4); }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #0f172a; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .text-glow { text-shadow: 0 0 20px rgba(99, 102, 241, 0.5); }
    </style>
</head>
<body class="antialiased overflow-hidden h-screen flex">

    {{-- SIDEBAR --}}
    <aside class="w-72 bg-slate-900 border-r border-slate-800 flex flex-col hidden md:flex z-50">
        <div class="p-8 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-indigo-500/30">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h1 class="font-bold text-white tracking-tight">Modul RKA</h1>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">E-Budgeting System</p>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scroll">
            <div class="px-4 pb-2 pt-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Menu Utama</div>
            
            <a href="{{ route('rka.dashboard') }}" class="flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm transition-all {{ request()->routeIs('rka.dashboard') ? 'sidebar-active' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-chart-pie w-5"></i> Dashboard SPK
            </a>
            
            <a href="{{ route('rka.final') }}" class="flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm transition-all text-slate-400 hover:bg-slate-800 hover:text-white">
                <i class="fas fa-file-invoice-dollar w-5"></i> Data RKA
            </a>

            <div class="px-4 pb-2 pt-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">Laporan</div>
            
        </nav>

        <div class="p-6 border-t border-slate-800">
            <button class="w-full py-3 bg-slate-800 hover:bg-rose-900/50 hover:text-rose-400 text-slate-400 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </aside>

    {{-- CONTENT AREA --}}
    <main class="flex-1 flex flex-col relative overflow-hidden bg-[#0f172a]">
        
        {{-- TOP BAR --}}
        <header class="h-20 border-b border-slate-800/50 flex items-center justify-between px-8 bg-slate-900/50 backdrop-blur-md sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-slate-400"><i class="fas fa-bars text-xl"></i></button>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    @yield('header_title', 'Dashboard')
                </h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> System Online
                </div>
            </div>
        </header>

        {{-- SCROLLABLE CONTENT --}}
        <div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 relative">
            {{-- Decoration BG --}}
            <div class="absolute top-0 left-0 w-full h-96 bg-indigo-600/10 blur-[100px] pointer-events-none rounded-full"></div>
            
            @if(session('success'))
                <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl flex items-center gap-3 font-bold text-sm animate-pulse">
                    <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
                </div>
            @endif

            @yield('content')
            
            <div class="h-20"></div> {{-- Spacer Bottom --}}
        </div>
    </main>

</body>
</html>