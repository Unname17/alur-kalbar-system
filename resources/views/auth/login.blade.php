<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Alur Kalbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1f4287 0%, #212529 100%);
        }
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
                <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required autofocus
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                @error('nip')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kata_sandi" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                <input type="password" id="kata_sandi" name="kata_sandi" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                @error('kata_sandi')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                MASUK
            </button>
        </form>
        <div class="mt-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <h3 class="text-md font-semibold text-gray-800 mb-3 text-center">
                <i class="fas fa-database mr-2"></i>DATA AKUN UJI (SESUAI SEEDER)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-600 border-collapse">
                    <thead>
                        <tr class="text-left border-b border-gray-300">
                            <th class="py-2">Nama Pengguna (Role)</th>
                            <th class="py-2">NIP / Username</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        <tr class="border-b border-gray-100 bg-blue-50/50">
                            <td class="py-2 font-bold text-blue-800">
                                Super Administrator
                                <span class="block text-[10px] text-blue-500 font-normal">(Role: admin_utama)</span>
                            </td>
                            <td class="py-2 font-mono text-blue-700 font-bold select-all cursor-pointer">admin</td>
                        </tr>

                        <tr class="border-b border-gray-100 bg-purple-50/50">
                            <td class="py-2 font-bold text-purple-800">
                                Verifikator Sekretariat
                                <span class="block text-[10px] text-purple-500 font-normal">(Role: sekretariat)</span>
                            </td>
                            <td class="py-2 font-mono text-purple-700 font-bold select-all cursor-pointer">198501012010012009</td>
                        </tr>

                        <tr class="border-b border-gray-100 hover:bg-white transition">
                            <td class="py-2 font-medium">
                                Kepala Dinas Kominfo
                                <span class="block text-[10px] text-green-600 font-normal">(Role: kepala_dinas)</span>
                            </td>
                            <td class="py-2 font-mono text-gray-600 select-all cursor-pointer">19800101</td>
                        </tr>

                        <tr class="border-b border-gray-100 italic">
                            <td class="py-2">
                                Admin Diskominfo Prov. Kalbar
                                <span class="block text-[10px] text-indigo-500 font-normal">(Role: opd - ID 5)</span>
                            </td>
                            <td class="py-2 font-mono text-indigo-700 select-all cursor-pointer">199001010051001</td>
                        </tr>

                        <tr class="italic">
                            <td class="py-2">
                                Admin Dinas Sosial
                                <span class="block text-[10px] text-indigo-500 font-normal">(Role: opd - ID 8)</span>
                            </td>
                            <td class="py-2 font-mono text-indigo-700 select-all cursor-pointer">199001010081001</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 p-2 bg-yellow-50 border border-yellow-100 rounded text-[10px] text-yellow-700">
                <p><strong>Info Login:</strong> Password semua akun: <code class="bg-white px-1">password</code></p>
                <p class="mt-1 italic">NIP OPD lainnya menggunakan format: 19900101 + [3 digit ID] + 1001</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>