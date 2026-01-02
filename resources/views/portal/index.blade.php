<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Perencanaan - Prov. Kalbar</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --kalbar-green: #064e3b;
            --kalbar-accent: #f59e0b;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.5);
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f3f4f6 url('https://images.unsplash.com/photo-1518182170546-0766ce6fec56?q=80&w=2000&auto=format&fit=crop') center/cover fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.9), rgba(16, 185, 129, 0.8));
            z-index: -1;
        }
        .user-greeting { color: white; padding-top: 3rem; padding-bottom: 2rem; }
        .ethnic-line { height: 4px; background: repeating-linear-gradient(45deg, #f59e0b, #f59e0b 10px, #dc2626 10px, #dc2626 20px, #000 20px, #000 30px); width: 100px; margin: 0 auto 1.5rem auto; border-radius: 2px; }
        
        /* Glassmorphism Card Style */
        .app-card { 
            background: var(--glass-bg); 
            backdrop-filter: blur(12px); 
            -webkit-backdrop-filter: blur(12px); 
            border: 1px solid var(--glass-border); 
            border-radius: 24px; 
            padding: 2.5rem 2rem; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
            position: relative; 
            overflow: hidden; 
            height: 100%; 
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .app-card:hover { 
            transform: translateY(-10px) scale(1.02); 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); 
            background: rgba(255, 255, 255, 1); 
            border-color: var(--kalbar-accent); 
        }

        .icon-wrapper { 
            width: 80px; height: 80px; border-radius: 20px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 2.5rem; margin-bottom: 1.5rem; 
            background: #f0fdf4; color: var(--kalbar-green); 
            transition: 0.3s; 
        }
        .app-card:hover .icon-wrapper { 
            background: var(--kalbar-green); 
            color: var(--kalbar-accent); 
            transform: rotate(5deg); 
        }

        .btn-launch { 
            background-color: var(--kalbar-green); color: white; 
            border: none; padding: 12px 20px; border-radius: 50px; 
            font-weight: 700; width: 100%; transition: 0.3s; 
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-launch:hover { background-color: #047857; letter-spacing: 1.5px; color: var(--kalbar-accent); }
        
        .status-badge { 
            position: absolute; top: 15px; right: 15px; 
            padding: 5px 12px; border-radius: 20px; 
            font-size: 0.7rem; font-weight: 800; 
        }
        .badge-online { background: #d1fae5; color: #065f46; }
        .badge-locked { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
    </style>
</head>
<body>

    <div class="overlay"></div>

    {{-- Header dengan Animasi fadeInDown --}}
    <div class="container user-greeting text-center animate__animated animate__fadeInDown">
        <h5 class="text-uppercase tracking-wider mb-1 opacity-75" style="letter-spacing: 3px;">Pemerintah Provinsi Kalimantan Barat</h5>
        <h1 class="display-5 fw-bold mb-3">Sistem Informasi Perencanaan Daerah</h1>
        <div class="ethnic-line"></div>
        
        <div class="d-inline-flex align-items-center bg-white bg-opacity-10 backdrop-blur rounded-pill px-4 py-2 mt-2 border border-white border-opacity-25">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=f59e0b&color=fff&bold=true" class="rounded-circle me-3 shadow-sm" width="40">
            <div class="text-start">
                <span class="d-block small opacity-75 text-light">Unit: {{ Auth::user()->perangkatDaerah->singkatan ?? 'Provinsi' }}</span>
                <span class="fw-bold text-white">{{ Auth::user()->nama_lengkap }}</span>
            </div>
            <div class="vr mx-3 bg-white opacity-50"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-link text-white text-decoration-none fw-bold hover:text-accent">
                    LOGOUT <i class="bi bi-box-arrow-right ms-1"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row justify-content-center g-4">
            
            @php $delay = 0; @endphp

            {{-- Looping Card dengan Animasi fadeInUp berurutan --}}
            @foreach($apps as $app)
            @php $delay += 150; @endphp
            
            <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}ms;">
                <div class="app-card">
                    {{-- Status Badge Dinamis --}}
                    @if(isset($app['is_locked']) && $app['is_locked'])
                        <span class="status-badge badge-locked uppercase"><i class="bi bi-lock-fill"></i> Terkunci</span>
                    @else
                        <span class="status-badge badge-online uppercase">Online</span>
                    @endif
                    
                    <div class="icon-wrapper shadow-sm">
                        <i class="bi {{ $app['icon'] }}"></i>
                    </div>
                    
                    <h4 class="fw-bold mb-2 text-dark">{{ $app['title'] }}</h4>
                    <p class="text-muted small flex-grow-1 px-2">{{ $app['desc'] }}</p>
                    
                    <div class="w-100 mt-4">
                        <a href="{{ $app['url'] }}" class="btn btn-launch text-decoration-none d-flex align-items-center justify-content-center">
                            Buka Aplikasi
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
        
        {{-- Footer dengan Animasi fadeIn --}}
        <div class="text-center text-white text-opacity-50 mt-5 small animate__animated animate__fadeIn" style="animation-delay: 1.2s;">
            &copy; 2025 Dinas Komunikasi dan Informatika Provinsi Kalimantan Barat.<br>
            Jl. Ahmad Yani, Pontianak.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>