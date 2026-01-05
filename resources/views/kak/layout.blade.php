<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modul KAK - @yield('title', 'E-Budgeting')</title>
    
    {{-- GANTI VITE DENGAN CDN AGAR TIDAK ERROR --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        serif: ['Merriweather', 'serif'],
                    },
                    colors: {
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        },
                    }
                }
            }
        }
    </script>
    
    {{-- Font Awesome & Google Fonts --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    {{-- Alpine.js (Untuk Interaktivitas) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Merriweather', serif; } /* Khusus Preview Dokumen */
        
        /* Scrollbar Custom */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #f59e0b; }

        /* Glassmorphism Spesifik KAK (Amber Tint) */
        .glass-kak {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(245, 158, 11, 0.1); /* Border Amber Tipis */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        
        /* Gradient Background Beda dengan RKA */
        .bg-kak-gradient {
            background: radial-gradient(circle at top right, #392410 0%, #0f172a 40%, #020617 100%);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 bg-kak-gradient min-h-screen relative overflow-x-hidden">

    {{-- Background Ornamen (Agar beda dengan RKA) --}}
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-amber-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[10%] left-[-10%] w-[400px] h-[400px] bg-rose-600/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="relative z-10 flex">
        
        {{-- SIDEBAR KHUSUS KAK (Compact) --}}
        <aside class="w-20 lg:w-24 h-screen fixed left-0 top-0 border-r border-slate-800 bg-slate-900/50 backdrop-blur-md flex flex-col items-center py-8 z-50">
            {{-- Logo --}}
            <div class="mb-10">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-amber-500/20">
                    K
                </div>
            </div>

            {{-- Menu Items --}}
            <nav class="flex-1 space-y-6 w-full px-2">
                {{-- Tombol Balik ke Portal (Menggunakan Route Dashboard Utama) --}}
                <a href="{{ route('dashboard') }}" class="group flex flex-col items-center gap-1 p-2 rounded-xl hover:bg-slate-800 transition-all text-slate-500 hover:text-white" title="Kembali ke Portal">
                    <i class="fas fa-th-large text-lg group-hover:-translate-x-1 transition-transform"></i>
                    <span class="text-[9px] font-bold">Portal</span>
                </a>

                <div class="w-8 h-[1px] bg-slate-800 mx-auto"></div>

                {{-- Link Dashboard KAK (List RKA) --}}
                <a href="{{ route('kak.index') }}" class="group flex flex-col items-center gap-1 p-3 rounded-xl {{ request()->routeIs('kak.index') ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'hover:bg-slate-800 text-slate-500 hover:text-amber-400' }} relative">
                    <i class="fas fa-list text-xl"></i>
                    <span class="text-[9px] font-bold mt-1">List</span>
                </a>

                <div class="w-8 h-[1px] bg-slate-800 mx-auto"></div>

                {{-- Tombol Logout --}}
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full group flex flex-col items-center gap-1 p-2 rounded-xl hover:bg-rose-900/20 transition-all text-slate-500 hover:text-rose-500">
                        <i class="fas fa-power-off text-lg"></i>
                        <span class="text-[9px] font-bold">Keluar</span>
                    </button>
                </form>
            </nav>

            {{-- User Avatar --}}
            <div class="mt-auto">
                <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap ?? 'User') }}&background=f59e0b&color=fff" alt="User">
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT WRAPPER --}}
        <main class="flex-1 ml-20 lg:ml-24 p-6 lg:p-10 transition-all duration-300">
            
            {{-- Header Modul --}}
            <header class="flex justify-between items-end mb-10">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Modul Perencanaan Dokumen
                        </span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                        @yield('header_title', 'Kerangka Acuan Kerja')
                    </h1>
                    <p class="text-slate-400 text-sm mt-2 max-w-2xl">
                        Sistem penyusunan narasi teknis kegiatan berbasis indikator kinerja.
                    </p>
                </div>

                {{-- Status Document --}}
                <div class="hidden md:block text-right">
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Status Dokumen</div>
                    <div class="flex items-center gap-2 justify-end text-amber-400 font-bold">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Drafting
                    </div>
                </div>
            </header>

            {{-- Content Slot --}}
            <div class="animate-fade-in-up">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-bold flex items-center gap-3">
                        <i class="fas fa-check-circle text-lg"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>

            {{-- Footer Simple --}}
            <footer class="mt-20 border-t border-slate-800/50 pt-8 text-center">
                <p class="text-[10px] text-slate-600 uppercase tracking-widest font-bold">
                    © 2026 E-Budgeting Provinsi Kalimantan Barat
                </p>
            </footer>

        </main>
    </div>

</body>
</html>