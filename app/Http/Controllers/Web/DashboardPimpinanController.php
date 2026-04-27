<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

class DashboardPimpinanController extends Controller {

    public function exportPpt() {
        // 1. AMBIL DATA
        $summary = DB::connection('modul_dashboard')->table('executive_summaries')->get();
        $totalProgres = $this->getWeightedAverage($summary);
        $totalPagu = $summary->sum('pagu_rka');
        $totalRealisasi = $summary->sum('nilai_kontrak_final');
        $bottlenecks = $summary->where('is_bottleneck', 1);

        // 2. SETUP PPT
        $ppt = new PhpPresentation();
        
        // --- SLIDE 1: COVER ---
        $slide1 = $ppt->getActiveSlide();
        $this->applyDarkBackground($slide1);

        // Ornamen Garis Aksen
        $line = $slide1->createRichTextShape()->setHeight(10)->setWidth(900)->setOffsetX(50)->setOffsetY(180);
        $line->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF6366F1')); // Indigo Line

        // Judul
        $shape = $slide1->createRichTextShape()->setHeight(300)->setWidth(900)->setOffsetX(50)->setOffsetY(200);
        $textRun = $shape->createTextRun("EXECUTIVE INTELLIGENCE\n");
        $textRun->getFont()->setBold(true)->setSize(48)->setColor(new Color('FFFFFFFF'));
        
        $textRun = $shape->createTextRun("Laporan Periodik Kinerja & Realisasi Anggaran\n");
        $textRun->getFont()->setSize(24)->setColor(new Color('FFCBD5E1')); // Slate-300
        
        $textRun = $shape->createTextRun("📅  " . date('d F Y') . "  |  🏛️  Sistem Alur Kalbar");
        $textRun->getFont()->setItalic(true)->setSize(16)->setColor(new Color('FF94A3B8')); 

        // --- SLIDE 2: DASHBOARD UTAMA (DINAMIS) ---
        $slide2 = $ppt->createSlide();
        $this->applyDarkBackground($slide2);
        $this->addSlideTitle($slide2, "KESEHATAN ORGANISASI (ORG. HEALTH)");

        // Logika Dinamis untuk Status
        $healthStatus = $totalProgres >= 80 ? "PRIMA" : ($totalProgres >= 50 ? "CUKUP" : "KRITIS");
        $healthColor  = $totalProgres >= 80 ? 'FF10B981' : ($totalProgres >= 50 ? 'FFF59E0B' : 'FFF43F5E');

        // Kartu 1: Score & Status
        $this->createKPICard($slide2, 50, 150, "KESEHATAN ORGANISASI", number_format($totalProgres, 1) . "%", $healthColor, $healthStatus);
        
        // Kartu 2: Penyerapan (Format Singkat)
        $persenSerap = $totalPagu > 0 ? ($totalRealisasi / $totalPagu) * 100 : 0;
        $serapColor = $persenSerap >= 50 ? 'FF10B981' : 'FFF43F5E';
        $this->createKPICard($slide2, 350, 150, "PENYERAPAN ANGGARAN", $this->shortNumber($totalRealisasi), $serapColor, number_format($persenSerap,1)."% Terpakai");

        // Kartu 3: Total Pagu
        $this->createKPICard($slide2, 650, 150, "TOTAL PAGU EFEKTIF", $this->shortNumber($totalPagu), 'FF3B82F6', count($summary)." Sub-Kegiatan");

        // --- SLIDE 3: TABEL EFISIENSI (FORMAT RINGKAS) ---
        $slide3 = $ppt->createSlide();
        $this->applyDarkBackground($slide3);
        $this->addSlideTitle($slide3, "ANALISIS EFISIENSI BELANJA");

        $table = $slide3->createTableShape(4);
        $table->setWidth(900)->setOffsetX(30)->setOffsetY(120);
        
        // Header
        $row = $table->createRow();
        $row->setHeight(40)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF1E293B'));
        $headers = [['KEGIATAN STRATEGIS', 400], ['PAGU', 150], ['KONTRAK', 150], ['EFISIENSI', 150]];
        
        foreach ($headers as $h) {
            $cell = $row->nextCell();
            $cell->setWidth($h[1]);
            $cell->createTextRun($h[0])->getFont()->setBold(true)->setSize(11)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Isi Tabel (Top 7 by Pagu)
        foreach ($summary->sortByDesc('pagu_rka')->take(7) as $item) {
            $row = $table->createRow();
            $row->setHeight(35);
            
            // Nama Kegiatan (Potong jika terlalu panjang agar tidak merusak layout)
            $namaKegiatan = strlen($item->nama_sub_kegiatan) > 50 ? substr($item->nama_sub_kegiatan, 0, 47) . '...' : $item->nama_sub_kegiatan;
            
            $row->nextCell()->createTextRun("▪ " . $namaKegiatan)->getFont()->setSize(10)->setColor(new Color('FFCBD5E1'));
            $row->nextCell()->createTextRun($this->shortNumber($item->pagu_rka))->getFont()->setSize(10)->setColor(new Color('FFFFFFFF'));
            $row->nextCell()->createTextRun($this->shortNumber($item->nilai_kontrak_final))->getFont()->setSize(10)->setColor(new Color('FF10B981')); // Hijau
            
            $efisiensi = $item->pagu_rka - $item->nilai_kontrak_final;
            $row->nextCell()->createTextRun($this->shortNumber($efisiensi))->getFont()->setSize(10)->setColor(new Color('FF3B82F6')); // Biru
        }

        // --- SLIDE 4: FOCUS AREA (BOTTLENECK) ---
        if($bottlenecks->count() > 0) {
            $slide4 = $ppt->createSlide();
            $this->applyDarkBackground($slide4);
            $this->addSlideTitle($slide4, "⚠️ PERLU ATENSI PIMPINAN (MACET)", 'FFF43F5E');

            $y = 120;
            foreach($bottlenecks->take(4) as $b) {
                // Background Card Merah Gelap
                $bg = $slide4->createRichTextShape()->setHeight(80)->setWidth(900)->setOffsetX(30)->setOffsetY($y);
                $bg->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('33F43F5E')); // Merah Transparan Gelap
                
                // Teks
                $textShape = $slide4->createRichTextShape()->setHeight(80)->setWidth(880)->setOffsetX(40)->setOffsetY($y+10);
                $textShape->createTextRun("🚨 " . $b->nama_sub_kegiatan)->getFont()->setBold(true)->setSize(14)->setColor(new Color('FFFFFFFF'));
                
                $detailText = "     Dana tersedia: " . $this->shortNumber($b->pagu_rka) . " | Status Pengadaan: Belum Dimulai";
                $textShape->createParagraph()->createTextRun($detailText)->getFont()->setItalic(true)->setSize(12)->setColor(new Color('FFFFFFFF'));
                
                $y += 95;
            }
        } else {
            // Jika tidak ada bottleneck (Semua Lancar)
            $slide4 = $ppt->createSlide();
            $this->applyDarkBackground($slide4);
            $shape = $slide4->createRichTextShape()->setHeight(200)->setWidth(800)->setOffsetX(80)->setOffsetY(200);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $shape->createTextRun("✅ SEMUA KEGIATAN ON-TRACK")->getFont()->setBold(true)->setSize(36)->setColor(new Color('FF10B981'));
            $shape->createParagraph()->createTextRun("Tidak ada hambatan kritis yang terdeteksi.")->getFont()->setSize(18)->setColor(new Color('FFFFFFFF'));
        }

        // Export
        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $name = 'Laporan_Eksekutif_' . date('d_M_Y') . '.pptx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
        header('Content-Disposition: attachment;filename="'.$name.'"');
        $writer->save('php://output');
        exit;
    }

    // --- HELPER BARU: FORMAT ANGKA PENDEK (Jt/M) ---
    private function shortNumber($n) {
        if ($n >= 1000000000) return number_format($n / 1000000000, 2) . ' M';
        if ($n >= 1000000) return number_format($n / 1000000, 1) . ' Jt';
        return number_format($n, 0, ',', '.');
    }

    private function applyDarkBackground($slide) {
        $shape = $slide->createRichTextShape()->setHeight(2000)->setWidth(2000)->setOffsetX(-100)->setOffsetY(-100);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF0F172A'));
    }

    private function addSlideTitle($slide, $text, $color = 'FFFFFFFF') {
        $shape = $slide->createRichTextShape()->setHeight(50)->setWidth(900)->setOffsetX(30)->setOffsetY(30);
        $shape->createTextRun($text)->getFont()->setBold(true)->setSize(24)->setColor(new Color($color));
        
        // Garis bawah judul
        $line = $slide->createRichTextShape()->setHeight(3)->setWidth(900)->setOffsetX(30)->setOffsetY(75);
        $line->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('33FFFFFF'));
    }

    private function createKPICard($slide, $x, $y, $label, $value, $accentColor, $subtext = '') {
        // Kotak Utama
        $card = $slide->createRichTextShape()->setHeight(220)->setWidth(280)->setOffsetX($x)->setOffsetY($y);
        $card->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF1E293B'));
        $card->getBorder()->setLineWidth(2)->setColor(new Color('33FFFFFF')); // Border tipis

        // Label
        $p = $card->getActiveParagraph();
        $p->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $card->createTextRun("\n" . $label . "\n")->getFont()->setSize(10)->setBold(true)->setColor(new Color('FF94A3B8'));
        
        // Nilai Besar
        $p2 = $card->createParagraph();
        $p2->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $card->createTextRun($value)->getFont()->setSize(28)->setBold(true)->setColor(new Color($accentColor));

        // Subtext (Status Dinamis)
        if($subtext) {
            $p3 = $card->createParagraph();
            $p3->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $card->createTextRun("\n" . $subtext)->getFont()->setSize(12)->setItalic(true)->setColor(new Color('FFFFFFFF'));
        }
    }

    // --- FUNGSI SYNC & INDEX (JANGAN DIHAPUS) ---
    public function fullSync() {
        set_time_limit(0); 
        $subActivities = DB::connection('modul_kinerja')->table('sub_activities as sa')
            ->join('activities as a', 'sa.activity_id', '=', 'a.id')
            ->join('programs as p', 'a.program_id', '=', 'p.id')
            ->join('sasaran_strategis as ss', 'p.sasaran_id', '=', 'ss.id')
            ->join('goals as g', 'ss.goal_id', '=', 'g.id')
            ->join('missions as m', 'g.mission_id', '=', 'm.id')
            ->join('visions as v', 'm.vision_id', '=', 'v.id')
            ->select('sa.*', 'a.nama_kegiatan', 'p.nama_program', 'm.misi_text', 'v.visi_text')->get();

        foreach ($subActivities as $sub) {
            $rka = DB::connection('modul_anggaran')->table('rka_mains')->where('sub_activity_id', $sub->id)->first();
            $kak = DB::connection('modul_kak')->table('kak_mains')->where('sub_activity_id', $sub->id)->exists();
            $status_pengadaan = 'belum'; $realisasi_sub_kegiatan = 0; $nama_vendor = null;

            if ($rka) {
                $rkaDetailIds = DB::connection('modul_anggaran')->table('rka_details')->where('rka_main_id', $rka->id)->pluck('id');
                $itemsSubKegiatan = DB::connection('modul_pengadaan')->table('procurement_items')->whereIn('rka_detail_id', $rkaDetailIds)->get();
                if ($itemsSubKegiatan->count() > 0) {
                    foreach ($itemsSubKegiatan as $item) {
                        $contract = DB::connection('modul_pengadaan')->table('procurement_contracts')->where('package_id', $item->package_id)->first();
                        if ($contract) {
                            $totalHpsPaket = DB::connection('modul_pengadaan')->table('procurement_items')->where('package_id', $item->package_id)->sum('total_hps');
                            $ratio = $totalHpsPaket > 0 ? ($contract->nilai_kontrak_final / $totalHpsPaket) : 0;
                            $realisasi_sub_kegiatan += ($item->total_hps * $ratio);
                            $vendor = DB::connection('modul_pengadaan')->table('procurement_vendors')->where('id', $contract->vendor_id)->first();
                            $nama_vendor = $vendor->nama_perusahaan ?? null;
                        }
                    }
                    $status_pengadaan = ($itemsSubKegiatan->pluck('rka_detail_id')->unique()->count() < $rkaDetailIds->count()) ? 'sebagian' : 'lengkap';
                }
            }
            $is_bottleneck = ($rka && ($rka->total_anggaran ?? 0) > 0 && $status_pengadaan == 'belum') ? 1 : 0;
            DB::connection('modul_dashboard')->table('executive_summaries')->updateOrInsert(['sub_activity_id' => $sub->id], [
                'nama_sub_kegiatan' => $sub->nama_sub, 'klasifikasi' => $sub->klasifikasi, 'status_kinerja' => $sub->status, 'pagu_rka' => $rka->total_anggaran ?? 0, 'has_kak' => $kak ? 1 : 0, 'status_pengadaan' => $status_pengadaan, 'nilai_kontrak_final' => $realisasi_sub_kegiatan, 'nama_vendor' => $nama_vendor, 'misi_text' => $sub->misi_text, 'visi_text' => $sub->visi_text, 'is_bottleneck' => $is_bottleneck, 'updated_at' => now()
            ]);
        }
        return redirect()->route('executive.index')->with('success', 'Intelligence data synchronized successfully!');
    }

public function index() {
        $summary = DB::connection('modul_dashboard')->table('executive_summaries')->get();
        
        // Data untuk Grafik Organisasi (Comparison Chart)
        $totalItems = $summary->count();
        $completedItems = $summary->where('status_pengadaan', 'lengkap')->count();
        $partialItems = $summary->where('status_pengadaan', 'sebagian')->count();
        $pendingItems = $totalItems - ($completedItems + $partialItems);

        // Data Keuangan
        $totalPagu = $summary->sum('pagu_rka');
        $totalRealisasi = $summary->sum('nilai_kontrak_final');
        
        // Hitung persentase serapan untuk grafik Donut Keuangan
        $persenSerapan = $totalPagu > 0 ? ($totalRealisasi / $totalPagu) * 100 : 0;
        $persenSisa = 100 - $persenSerapan;

        $progressKegiatan = $summary->take(10);
        
        // Ambil instruksi internal (Sistem Validasi)
        $directives = DB::connection('modul_dashboard')
            ->table('executive_directives as ed')
            ->join('executive_summaries as es', 'ed.sub_activity_id', '=', 'es.sub_activity_id')
            ->select('ed.*', 'es.nama_sub_kegiatan')
            ->orderBy('ed.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalItems', 'completedItems', 'partialItems', 'pendingItems', // Data Grafik Kegiatan
            'totalPagu', 'totalRealisasi', 'persenSerapan', 'persenSisa',   // Data Grafik Keuangan
            'progressKegiatan', 'directives'
        ));
    }

    public function getItemDetails($id) {
        try {
            $summary = DB::connection('modul_dashboard')->table('executive_summaries')->where('sub_activity_id', $id)->first();
            if (!$summary) return response()->json(['error' => 'Data not found'], 404);
            $rka = DB::connection('modul_anggaran')->table('rka_mains')->where('sub_activity_id', $id)->first();
            $contracted = []; $pending = [];
            if ($rka) {
                $allRkaDetails = DB::connection('modul_anggaran')->table('rka_details')->where('rka_main_id', $rka->id)->get();
                foreach ($allRkaDetails as $detail) {
                    $procItem = DB::connection('modul_pengadaan')->table('procurement_items')->where('rka_detail_id', $detail->id)->first();
                    if ($procItem) {
                        $contract = DB::connection('modul_pengadaan')->table('procurement_contracts')->where('package_id', $procItem->package_id)->first();
                        $ratio = ($contract && $contract->nilai_kontrak_final > 0) ? ($contract->nilai_kontrak_final / DB::connection('modul_pengadaan')->table('procurement_items')->where('package_id', $procItem->package_id)->sum('total_hps')) : 1;
                        $contracted[] = ['nama' => $detail->uraian_belanja, 'spek' => $detail->spesifikasi ?? '-', 'volume' => $detail->koefisien . ' ' . $detail->satuan, 'harga_rka' => number_format($detail->harga_satuan, 0, ',', '.'), 'total_realisasi' => number_format($detail->sub_total * $ratio, 0, ',', '.')];
                    } else {
                        $pending[] = ['nama' => $detail->uraian_belanja, 'spek' => $detail->spesifikasi ?? '-', 'volume' => $detail->koefisien . ' ' . $detail->satuan, 'total_rencana' => number_format($detail->sub_total, 0, ',', '.')];
                    }
                }
            }
            return response()->json(['context' => ['klasifikasi' => $summary->klasifikasi, 'misi' => $summary->misi_text ?? 'Misi belum terpetakan', 'visi' => $summary->visi_text ?? 'Visi belum terpetakan'], 'contracted' => $contracted, 'pending' => $pending]);
        } catch (\Exception $e) { return response()->json(['error' => $e->getMessage()], 500); }
    }

// REVISI: Simpan Catatan ke Sistem (Bukan WA)
    public function storeNote(Request $request) {
        $request->validate([
            'sub_activity_id' => 'required', 
            'instruction' => 'required'
        ]);

        // Simpan ke database (Ini adalah bentuk Validasi/Revisi dari Pimpinan)
        DB::connection('modul_dashboard')->table('executive_directives')->insert([
            'sub_activity_id' => $request->sub_activity_id,
            'instruction' => $request->instruction,
            'status' => 'pending_revision', // Status baru: Menunggu perbaikan modul terkait
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Instruksi revisi berhasil dikirim ke sistem modul terkait.'
        ]);
    }

    private function getWeightedAverage($data) {
        if ($data->count() === 0) return 0;
        return $data->sum(fn($item) => $this->calculateItemWeight($item)) / $data->count();
    }

    private function calculateItemWeight($item) {
        $weight = 0;
        if ($item->status_kinerja == 'approved') $weight += 25;
        if ($item->pagu_rka > 0) $weight += 25;
        if ($item->has_kak) $weight += 25;
        $weight += ($item->status_pengadaan == 'lengkap') ? 25 : (($item->status_pengadaan == 'sebagian') ? 12.5 : 0);
        return $weight;
    }
}