<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kinerja\Vision;
use App\Models\Kinerja\Mission;
use App\Models\Kinerja\Goal;
use App\Models\Kinerja\Program;
use App\Models\Kinerja\Activity;
use App\Models\Kinerja\SubActivity;
use App\Models\Kinerja\PerformanceIndicator;
use App\Models\Kinerja\AccessSetting;

class PohonKinerjaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. VISI TUNGGAL (Provinsi)
        $vision = Vision::on('modul_kinerja')->create([
            'tahun_awal'  => 2025,
            'tahun_akhir' => 2029,
            'visi_text'   => 'Terwujudnya Masyarakat Kalimantan Barat yang Sejahtera, Mandiri, dan Berdaya Saing',
            'is_active'   => true,
        ]);

        // 2. EMPAT MISI PROVINSI
        $misi1 = Mission::on('modul_kinerja')->create([
            'vision_id' => $vision->id, 'nomor_misi' => 1,
            'misi_text' => 'Mewujudkan Tata Kelola Pemerintahan yang Bersih, Terbuka, dan Berbasis Teknologi Informasi'
        ]);

        Mission::on('modul_kinerja')->create([
            'vision_id' => $vision->id, 'nomor_misi' => 2,
            'misi_text' => 'Meningkatkan Kualitas Sumber Daya Manusia yang Berbudaya dan Berdaya Saing'
        ]);

        Mission::on('modul_kinerja')->create([
            'vision_id' => $vision->id, 'nomor_misi' => 3,
            'misi_text' => 'Mewujudkan Infrastruktur Dasar yang Adil dan Merata'
        ]);

        Mission::on('modul_kinerja')->create([
            'vision_id' => $vision->id, 'nomor_misi' => 4,
            'misi_text' => 'Meningkatkan Pertumbuhan Ekonomi Berbasis Potensi Unggulan Daerah'
        ]);

        // 3. TUJUAN (Contoh Cascading di Misi 1 - DISKOMINFO)
        $goal = Goal::on('modul_kinerja')->create([
            'mission_id'  => $misi1->id,
            'pd_id'       => 1, 
            'nama_tujuan' => 'Meningkatkan Kualitas Layanan Digital dan Keterbukaan Informasi Publik',
        ]);

        // ... (Lanjutkan Program, Kegiatan, Sub-Kegiatan seperti sebelumnya)
        $program = Program::on('modul_kinerja')->create([
            'goal_id' => $goal->id, 'nama_program' => 'Program Aplikasi Informatika'
        ]);

        $activity = Activity::on('modul_kinerja')->create([
            'program_id' => $program->id, 'nama_kegiatan' => 'Pengembangan Ekosistem Digital'
        ]);

        $sub = SubActivity::on('modul_kinerja')->create([
            'activity_id' => $activity->id, 'bidang_id' => 1, 'kode_sub' => '5.02.01',
            'nama_sub' => 'Pemeliharaan Aplikasi Alur-Kalbar', 'created_by_nip' => '19900101', 'status' => 'approved'
        ]);

        PerformanceIndicator::on('modul_kinerja')->create([
            'sub_activity_id' => $sub->id, 'nama_indikator' => 'Persentase Integrasi Modul',
            'satuan' => 'Persen', 'klasifikasi' => 'IKK', 'baseline_2024' => 0, 'target_2025' => 100
        ]);

        AccessSetting::on('modul_kinerja')->updateOrCreate(['pd_id' => 1], ['is_locked' => false, 'updated_by_nip' => '19850101']);
    }
}