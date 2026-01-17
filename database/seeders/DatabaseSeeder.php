<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {


    $this->call([
            KbkiMasterSeeder::class,
            ProcurementVendorSeeder::class, // Tambahkan di sini
            ProcurementPackageSeeder::class,
            ProcurementPreparationSeeder::class, 
            ProcurementAnalysisSeeder::class, 
            ProcurementSpecSeeder::class, 
            ProcurementDoc6Seeder::class,
            
        ]);

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
