<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Alur Kalbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg { background: linear-gradient(135deg, #1f4287 0%, #212529 100%); }
        .copy-badge:active { transform: scale(0.95); transition: 0.1s; }
    </style>
</head>
<body class="gradient-bg flex items-center justify-center min-h-screen p-4">
<div class="w-full max-w-md">
    <div class="bg-white rounded-xl shadow-2xl p-8 md:p-10 border-t-4 border-indigo-600">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">PORTAL LOGIN</h1>
            <p class="text-gray-500 mt-2 text-sm uppercase tracking-widest">Alur Kalimantan Barat</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label for="nip" class="block text-xs font-semibold text-gray-600 uppercase mb-1">NIP Login</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required autofocus
                       placeholder="Masukkan NIP"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition duration-150">
                @error('nip')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="kata_sandi" class="block text-xs font-semibold text-gray-600 uppercase mb-1">Kata Sandi</label>
                <input type="password" id="kata_sandi" name="kata_sandi" required
                       placeholder="••••••••"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition duration-150">
            </div>
            <button type="submit" class="w-full py-3 px-4 rounded-lg shadow-lg text-white bg-indigo-600 hover:bg-indigo-700 font-bold text-lg transition duration-200 uppercase">
                Masuk Sistem
            </button>
        </form>

        <div class="mt-8 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
            <h3 class="text-xs font-bold text-indigo-800 mb-3 text-center uppercase tracking-wider">Data Akun Uji (Klik NIP untuk Copy)</h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center text-[11px] bg-white p-2 rounded border border-indigo-200">
                    <span class="font-bold text-gray-700">Admin Bappeda</span>
                    <code class="cursor-pointer text-indigo-600 font-mono hover:bg-indigo-100 px-1 rounded transition" 
                          onclick="copyToNip('199001012024011001')">199001012024011001</code>
                </div>
                <div class="flex justify-between items-center text-[11px] bg-white p-2 rounded border border-indigo-200">
                    <span class="font-bold text-gray-700">Kepala Dinas</span>
                    <code class="cursor-pointer text-indigo-600 font-mono hover:bg-indigo-100 px-1 rounded transition" 
                          onclick="copyToNip('197501012000011001')">197501012000011001</code>
                </div>
                <div class="flex justify-between items-center text-[11px] bg-white p-2 rounded border border-indigo-200">
                    <span class="font-bold text-gray-700">Kabid Aptika</span>
                    <code class="cursor-pointer text-indigo-600 font-mono hover:bg-indigo-100 px-1 rounded transition" 
                          onclick="copyToNip('198001012005011002')">198001012005011002</code>
                </div>
                <div class="flex justify-between items-center text-[11px] bg-white p-2 rounded border border-indigo-200">
                    <span class="font-bold text-gray-700">Staff Aptika</span>
                    <code class="cursor-pointer text-indigo-600 font-mono hover:bg-indigo-100 px-1 rounded transition" 
                          onclick="copyToNip('199801012024011004')">199801012024011004</code>
                </div>
            </div>
            <p class="mt-3 text-center text-[10px] text-indigo-600">Password semua akun: <b class="bg-white px-1">password</b></p>
        </div>
    </div>
</div>

<script>
    function copyToNip(nip) {
        document.getElementById('nip').value = nip;
        // Opsional: Langsung fokus ke input password setelah copy NIP
        document.getElementById('kata_sandi').focus();
    }
</script>
</body>
</html>