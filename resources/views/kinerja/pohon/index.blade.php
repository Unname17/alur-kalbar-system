<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-performance | @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    {{-- Library Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'midnight': '#0f172a',
                        'performance-green': '#10b981',
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .sidebar-active { background: #10b981; color: white !important; font-weight: 800; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2); }
    </style>
    @stack('css')
</head>
<body class="h-full font-sans text-slate-700 overflow-hidden">

<div class="flex h-screen overflow-hidden">
    {{-- SIDEBAR --}}
    <aside class="w-72 bg-midnight flex-shrink-0 flex flex-col z-20 shadow-xl border-r border-white/5">
        <div class="p-6 flex items-center gap-4 border-b border-white/5">
            <div class="w-11 h-11 bg-performance-green rounded-xl flex items-center justify-center shadow-lg shadow-emerald-900/20">
                <i class="fas fa-sitemap text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-white font-black text-lg leading-none tracking-tight">E-performance</h1>
                <p class="text-slate-500 text-[9px] mt-1.5 font-bold uppercase tracking-[2px]">Sistem Perencanaan</p>
            </div>
        </div>
        
        <nav class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-8">
            {{-- GRUP 1: NAVIGASI UTAMA --}}
            <div>
                <p class="px-4 text-[10px] font-black text-slate-600 uppercase tracking-[2px] mb-4">Navigasi Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3.5 text-slate-400 hover:text-white hover:bg-white/5 rounded-2xl transition-all group">
                        <i class="fas fa-home w-5 text-center group-hover:scale-110"></i>
                        <span class="text-sm font-bold">Portal Depan</span>
                    </a>
                </div>
            </div>

            {{-- GRUP 2: MODUL KINERJA --}}
            <div>
                <p class="px-4 text-[10px] font-black text-slate-600 uppercase tracking-[2px] mb-4">Modul Kinerja</p>
                <div class="space-y-1">
                    {{-- 1. MENU DASHBOARD --}}
                    <a href="{{ route('kinerja.index') }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('kinerja.index') ? 'sidebar-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-th-large w-5 text-center group-hover:scale-110"></i>
                        <span class="text-sm font-bold">Dashboard</span>
                    </a>

                    {{-- Logic Peran User --}}
                    @php
                        $userRole = strtolower(is_object(Auth::user()->role) ? Auth::user()->role->name : Auth::user()->role);
                    @endphp

                    {{-- 2. MENU WIZARD CASCADING (INPUT) --}}
                    @if($userRole !== 'bappeda')
                    <a href="{{ route('kinerja.wizard.index') }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('kinerja.wizard.*') ? 'sidebar-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-plus-circle w-5 text-center group-hover:scale-110"></i>
                        <span class="text-sm font-bold">Wizard Cascading</span>
                    </a>
                    @endif

                    {{-- 3. MENU INBOX APPROVAL (VERIFIKASI) - KHUSUS ATASAN --}}
                    @if(in_array($userRole, ['kabid', 'kadis', 'bappeda']))
                    <a href="{{ route('kinerja.inbox.index') }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('kinerja.inbox.*') ? 'sidebar-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="relative">
                            <i class="fas fa-inbox w-5 text-center group-hover:scale-110"></i>
                            <div id="notif-count" class="hidden absolute -top-2 -right-2 w-4 h-4 bg-rose-500 text-[8px] flex items-center justify-center text-white rounded-full border-2 border-midnight font-black animate-bounce">0</div>
                        </div>
                        <span class="text-sm font-bold">Inbox Approval</span>
                    </a>
                    @endif

                    {{-- 4. MENU POHON KINERJA --}}
                    <a href="{{ route('kinerja.pohon') }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('kinerja.pohon') ? 'sidebar-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-project-diagram w-5 text-center group-hover:scale-110"></i>
                        <span class="text-sm font-bold">Pohon Kinerja</span>
                    </a>
                    
                    {{-- 5. MENU MONITORING --}}
                    <a href="{{ route('kinerja.monitoring') }}" 
                    class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('kinerja.monitoring') ? 'sidebar-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-tasks w-5 text-center group-hover:scale-110"></i>
                        <span class="text-sm font-bold">Monitoring Progress</span>
                    </a>

                    {{-- 6. MENU ADMINISTRASI - KHUSUS BAPPEDA --}}
                    @if($userRole === 'bappeda')
                    <div class="pt-4 mt-4 border-t border-white/5">
                        <p class="px-4 text-[10px] font-black text-slate-600 uppercase tracking-[2px] mb-4">Administrasi</p>
                        
                        {{-- Manajemen Akses --}}
                        <a href="{{ route('kinerja.admin.access.index') }}" 
                           class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('kinerja.admin.access.*') ? 'sidebar-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-user-lock w-5 text-center group-hover:scale-110"></i>
                            <span class="text-sm font-bold">Manajemen Akses</span>
                        </a>

                        {{-- [BARU] Log Aktivitas --}}
                        <a href="{{ route('kinerja.log.index') }}" 
                           class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('kinerja.log.*') ? 'sidebar-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-history w-5 text-center group-hover:scale-110"></i>
                            <span class="text-sm font-bold">Log Aktivitas</span>
                        </a>
                    </div>
                    @endif

                </div>
            </div>
        </nav>

        {{-- USER PROFILE BOTTOM --}}
        <div class="p-4 border-t border-white/5 bg-slate-800/20">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3.5 bg-slate-800 hover:bg-red-600 text-slate-400 hover:text-white rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest border-0 cursor-pointer">
                    <i class="fas fa-power-off"></i> LOGOUT
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col min-w-0 bg-slate-100">
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-10 z-10 shadow-sm">
            <h2 class="text-slate-800 font-black text-xl tracking-tight">@yield('page_title')</h2>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-slate-900 font-extrabold text-sm leading-none">{{ Auth::user()->nama_lengkap }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 uppercase mt-1 border border-slate-200">
                        {{ strtoupper($userRole) }} | {{ Auth::user()->perangkatDaerah->singkatan ?? 'DISKOMINFO' }}
                    </span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-900 flex items-center justify-center text-performance-green font-black text-sm">
                    {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 2)) }}
                </div>
            </div>
        </header>
        <main class="flex-1 overflow-y-auto p-10 custom-scrollbar">
            @yield('content')
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Fungsi Update Badge (Notification)
    function updateInboxBadge() {
        // Cek apakah elemen notif-count ada di halaman (hanya muncul untuk role atasan)
        if ($('#notif-count').length) {
            $.get("{{ route('kinerja.inbox.count') }}", function(data) {
                const badge = $('#notif-count');
                if (data.count > 0) {
                    badge.text(data.count).removeClass('hidden');
                    // Tambahkan animasi sedikit agar menarik perhatian
                    badge.addClass('animate-bounce');
                    setTimeout(() => badge.removeClass('animate-bounce'), 3000);
                } else {
                    badge.addClass('hidden');
                }
            }).fail(function() {
                console.log("Gagal mengambil data notifikasi.");
            });
        }
    }

    $(document).ready(function() {
        // Jalankan saat pertama kali halaman dibuka
        updateInboxBadge();

        // (Opsional) Jalankan otomatis setiap 2 menit untuk mengecek kiriman dari staff lain
        setInterval(updateInboxBadge, 120000); 
    });
</script>
@stack('js')
</body>
</html>