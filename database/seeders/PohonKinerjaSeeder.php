<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kinerja\PohonKinerja;
use Illuminate\Support\Facades\DB;

class PohonKinerjaSeeder extends Seeder
{
    public function run()
    {
        DB::connection('modul_kinerja')->statement('SET FOREIGN_KEY_CHECKS=0;');
        PohonKinerja::on('modul_kinerja')->truncate();
        DB::connection('modul_kinerja')->table('indikator_kinerja')->truncate();
        DB::connection('modul_kinerja')->statement('SET FOREIGN_KEY_CHECKS=1;');

        $allOpds = DB::connection('sistem_admin')->table('perangkat_daerah')->get();

        DB::beginTransaction();
        try {
            $visi = $this->createNode(null, 'Mewujudkan Kalimantan Barat yang Sejahtera, Mandiri, dan Berdaya Saing', 'visi', null, 1);

            $misiTexts = [
                'Mewujudkan Tata Kelola Pemerintahan yang Berkualitas (Smart Province)',
                'Meningkatkan Kualitas Sumber Daya Manusia yang Unggul',
                'Mewujudkan Infrastruktur yang Adil dan Merata',
                'Meningkatkan Pertumbuhan Ekonomi Berbasis Potensi Daerah',
                'Mewujudkan Masyarakat yang Aman dan Toleran',
                'Meningkatkan Kelestarian Lingkungan Hidup',
                'Mewujudkan Tata Kelola Keuangan yang Akuntabel'
            ];

            foreach ($misiTexts as $index => $text) {
                $misi = $this->createNode($visi->id, "Misi " . ($index + 1) . ": " . $text, 'misi', null, 1);

                foreach ($allOpds as $opd) {
                    if ($index == 0 || $opd->id % 7 == $index) {
                        // Khusus OPD 5 (Diskominfo), buat 3 variasi sasaran untuk testing
                        if ($opd->id == 5) {
                            for ($i = 1; $i <= 3; $i++) {
                                $this->seedCabangLengkap($misi->id, $opd->id, $opd->nama_perangkat_daerah, 1, $index . $i);
                            }
                        } else {
                            $this->seedCabangLengkap($misi->id, $opd->id, $opd->nama_perangkat_daerah, 1, $index);
                        }
                    }
                }
            }

            DB::commit();
            echo "✅ Pohon Kinerja Berhasil Dibuat dengan variasi data.\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "❌ Gagal: " . $e->getMessage() . "\n";
        }
    }

    private function seedCabangLengkap($parentId, $opdId, $namaOpd, $userId, $suffix)
    {
        $sasaran = $this->createNode($parentId, "[$suffix] Sasaran: Meningkatnya Pelayanan pada " . $namaOpd, 'sasaran_opd', $opdId, $userId);
        
        $program = $this->createNode($sasaran->id, "[$suffix] Program Pendukung Transformasi Digital " . $namaOpd, 'program', $opdId, $userId);
        
        $kegiatan = $this->createNode($program->id, "[$suffix] Kegiatan Tata Kelola IT", 'kegiatan', $opdId, $userId);
        
        // Sub Kegiatan dengan Anggaran Random
        $randomAnggaran = rand(10, 90) * 10000000;
        $sub = $this->createNode($kegiatan->id, "[$suffix] Sub-Kegiatan Operasional Teknis", 'sub_kegiatan', $opdId, $userId, $randomAnggaran, 'Kepala Bidang');
        $this->addIndikator($sub, [["Indeks Kepuasan $suffix", rand(75, 90), "%"]]);

        // Rencana Aksi
        $rak = $this->createNode($sub->id, "[$suffix] Rencana Aksi: Monitoring Evaluasi Tahap $suffix", 'rencana_aksi', $opdId, $userId);
        $this->addIndikator($rak, [["Jumlah Laporan $suffix", rand(1, 12), "Dokumen"]]);
    }

    private function createNode($parentId, $nama, $jenis, $opdId, $userId, $anggaran = 0, $pj = null)
    {
        return PohonKinerja::create([
            'parent_id' => $parentId,
            'nama_kinerja' => $nama,
            'jenis_kinerja' => $jenis,
            'opd_id' => $opdId,
            'created_by' => $userId,
            'anggaran' => $anggaran,
            'penanggung_jawab' => $pj,
            'status' => 'disetujui'
        ]);
    }

    private function addIndikator($node, $indikators)
    {
        foreach ($indikators as $ind) {
            // PERBAIKAN: Menggunakan kolom 'indikator' sesuai skema DB Anda
            $node->indikators()->create([
                'indikator' => $ind[0], 
                'target'    => $ind[1], 
                'satuan'    => $ind[2]
            ]);
        }
    }
}