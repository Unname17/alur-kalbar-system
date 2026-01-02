<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoDeleteLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-delete-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil durasi dari database (default 3 bulan jika tidak diatur)
        $months = DB::connection('modul_kinerja')->table('pengaturan_sistem')
                    ->where('kunci', 'auto_delete_log_months')->value('nilai') ?? 3;

        $dateLimit = now()->subMonths($months);

        // Hapus log yang lebih lama dari durasi tersebut
        $deleted = DB::connection('modul_kinerja')->table('log_aktivitas')
                    ->where('created_at', '<', $dateLimit)
                    ->delete();

        $this->info("Berhasil menghapus $deleted data log yang sudah kadaluwarsa.");
    }
}
