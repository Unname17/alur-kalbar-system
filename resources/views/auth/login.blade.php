<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Alur Kalbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg { background: linear-gradient(135deg, #1f4287 0%, #212529 100%); }
    </style>
</head>
<body class="gradient-bg flex items-center justify-center min-h-screen">
<div class="w-full max-w-md">
    <div class="bg-white rounded-xl shadow-2xl p-8 md:p-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800">PORTAL LOGIN</h1>
            <p class="text-gray-500 mt-2">Sistem Informasi Alur Kalimantan Barat</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP / Username</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required autofocus
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                @error('nip')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="kata_sandi" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                <input type="password" id="kata_sandi" name="kata_sandi" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
            </div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150">
                MASUK
            </button>
        </form>

        <div class="mt-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <h3 class="text-md font-semibold text-gray-800 mb-3 text-center">DATA AKUN UJI (SESUAI SEEDER)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-[11px] text-gray-600 border-collapse">
                    <thead>
                        <tr class="text-left border-b border-gray-300">
                            <th class="py-2">Peran (Role)</th>
                            <th class="py-2">NIP Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b bg-blue-50/50">
                            <td class="py-2 font-bold">Admin Utama</td>
                            <td class="py-2 font-mono">admin</td>
                        </tr>
                        <tr class="border-b bg-purple-50/50">
                            <td class="py-2 font-bold">Bappeda (Validator)</td>
                            <td class="py-2 font-mono">19850101</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2">Kepala Dinas</td>
                            <td class="py-2 font-mono">19800101</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2">Staf Perencana Aptika</td>
                            <td class="py-2 font-mono">19950101</td>
                        </tr>
                        <tr>
                            <td class="py-2">PPK (Pengadaan)</td>
                            <td class="py-2 font-mono">19900101</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 p-2 bg-yellow-50 border border-yellow-100 rounded text-[10px] text-yellow-700">
                <p><strong>Info:</strong> Password untuk semua akun di atas adalah: <code class="bg-white px-1">password</code></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>