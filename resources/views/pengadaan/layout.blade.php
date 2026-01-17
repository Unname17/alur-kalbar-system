<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modul Pengadaan - @yield('title', 'E-Budgeting')</title>
    
    {{-- CDN Libraries (Sama seperti modul lain agar ringan) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'], // Font lebih tegas untuk data
                        mono: ['JetBrains Mono', 'monospace'], // Untuk angka/rupiah
                    },
                    colors: {
                        // Tema Khusus Pengadaan: CYAN & EMERALD
                        procurement: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            200: '#a5f3fc',
                            300: '#67e8f9',
                            400: '#22d3ee',
                            500: '#06b6d4', // Primary Color
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63',
                            950: '#083344', // Dark Background
                        },
                    }
                }
            }
        }
    </script>
    
    {{-- Font Awesome & Google Fonts --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Scrollbar Biru Laut */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #083344; }
        ::-webkit-scrollbar-thumb { background: #155e75; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #06b6d4; }

        /* Glassmorphism Spesifik Pengadaan (Bluish Tint) */
        .glass-procurement {
            background: rgba(8, 51, 68, 0.7); /* Lebih gelap/biru */
            backdrop-filter: blur(16px);
            border: 1px solid rgba(34, 211, 238, 0.1); /* Border Cyan Tipis */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }
        
        /* Gradient Background "Deep Ocean" */
        .bg-procurement-gradient {
            background: radial-gradient(circle at bottom left, #0f172a 0%, #083344 50%, #020617 100%);
        }

        /* Animasi halus */
        .animate-enter { animation: enter 0.5s ease-out forwards; opacity: 0; transform: translateY(10px); }
        @keyframes enter { to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 bg-procurement-gradient min-h-screen relative overflow-x-hidden">

    {{-- Background Ornamen (Bola-bola cahaya biru/ungu) --}}
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[20%] w-[600px] h-[600px] bg-procurement-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[5%] right-[-5%] w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative z-10 flex">
        
        {{-- SIDEBAR KHUSUS PENGADAAN (Modern Sidebar) --}}
        <aside class="w-20 lg:w-64 h-screen fixed left-0 top-0 border-r border-slate-800 bg-slate-900/60 backdrop-blur-xl flex flex-col z-50 transition-all group">
            
            {{-- Logo Area --}}
            <div class="h-24 flex items-center justify-center lg:justify-start lg:px-8 border-b border-slate-800/50">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-cyan-500/20 shrink-0">
                    P
                </div>
                <div class="hidden lg:block ml-4">
                    <h1 class="font-bold text-white text-lg leading-none">E-Purchasing</h1>
                    <span class="text-[10px] text-cyan-400 uppercase tracking-widest">Modul Pengadaan</span>
                </div>
            </div>

            {{-- Menu Items --}}
            <nav class="flex-1 py-8 px-4 space-y-2 overflow-y-auto">
                
                {{-- Portal Balik --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 p-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all mb-6">
                    <i class="fas fa-th-large text-lg w-6 text-center"></i>
                    <span class="hidden lg:block text-sm font-semibold">Portal Utama</span>
                </a>

                <div class="text-[10px] font-bold text-slate-600 uppercase tracking-widest px-3 mb-2 hidden lg:block">Menu Utama</div>

                {{-- Dashboard --}}
                <a href="{{ route('pengadaan.index') }}" class="flex items-center gap-4 p-3 rounded-xl {{ request()->routeIs('pengadaan.index') ? 'bg-procurement-500/10 text-cyan-400 border border-procurement-500/20 shadow-lg shadow-cyan-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-chart-pie text-lg w-6 text-center"></i>
                    <span class="hidden lg:block text-sm font-bold">Dashboard</span>
                </a>

                {{-- Paket Saya --}}
                <a href="#" class="flex items-center gap-4 p-3 rounded-xl {{ request()->is('pengadaan/manage*') ? 'bg-procurement-500/10 text-cyan-400 border border-procurement-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-box-open text-lg w-6 text-center"></i>
                    <span class="hidden lg:block text-sm font-bold">Paket Pengadaan</span>
                </a>

                {{-- Master Vendor --}}
                <a href="#" class="flex items-center gap-4 p-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-users-cog text-lg w-6 text-center"></i>
                    <span class="hidden lg:block text-sm font-bold">Database Vendor</span>
                </a>

                {{-- Laporan --}}
                <a href="#" class="flex items-center gap-4 p-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-file-contract text-lg w-6 text-center"></i>
                    <span class="hidden lg:block text-sm font-bold">Laporan & Arsip</span>
                </a>

            </nav>

            {{-- User Footer --}}
            <div class="p-4 border-t border-slate-800/50">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-rose-900/20 text-slate-400 hover:text-rose-400 transition-all">
                        <i class="fas fa-sign-out-alt text-lg w-6 text-center"></i>
                        <span class="hidden lg:block text-sm font-bold">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT WRAPPER --}}
        <main class="flex-1 ml-20 lg:ml-64 p-6 lg:p-10 transition-all duration-300">
            
            {{-- Top Bar (Breadcrumbs & Profile) --}}
            <header class="flex justify-between items-center mb-8 animate-enter">
                <div>
                    <h2 class="text-2xl font-black text-white tracking-tight">@yield('header_title', 'Pengadaan')</h2>
                    <div class="flex items-center gap-2 text-xs text-slate-400 mt-1">
                        <a href="{{ route('dashboard') }}" class="hover:text-cyan-400">Home</a>
                        <i class="fas fa-chevron-right text-[8px]"></i>
                        <span class="text-cyan-500 font-bold">@yield('title')</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Notif Icon --}}
                    <button class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-400 hover:text-white transition-all relative">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    </button>
                    
                    {{-- Profile Pill --}}
                    <div class="flex items-center gap-3 bg-slate-900/50 border border-slate-700 rounded-full pl-1 pr-4 py-1">
                        <div class="w-8 h-8 rounded-full bg-cyan-600 flex items-center justify-center text-white font-bold text-xs border-2 border-slate-800">
                            {{ substr(Auth::user()->nama_lengkap ?? 'U', 0, 1) }}
                        </div>
                        <div class="text-xs font-bold text-slate-300 hidden md:block">
                            {{ Auth::user()->nama_lengkap ?? 'User Pengadaan' }}
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content Slot --}}
            <div class="animate-enter" style="animation-delay: 0.1s;">
                {{-- Flash Message --}}
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-bold flex items-center gap-3 backdrop-blur-md">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                            <i class="fas fa-check"></i>
                        </div>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-bold">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-full bg-rose-500/20 flex items-center justify-center shrink-0">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <span>Terdapat kesalahan input:</span>
                        </div>
                        <ul class="list-disc ml-12 text-xs opacity-80">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Main Content --}}
                @yield('content')
            </div>

            {{-- Footer --}}
            <footer class="mt-20 border-t border-slate-800/50 pt-8 text-center">
                <p class="text-[10px] text-slate-600 uppercase tracking-widest font-bold">
                    Sistem Pengadaan E-Purchasing © {{ date('Y') }}
                </p>
            </footer>

        </main>
    </div>

</body>
</html>