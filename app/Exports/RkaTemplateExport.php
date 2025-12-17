<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RkaTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    /**
     * Data Dummy untuk Contoh (Mulai muncul di Baris ke-8)
     */
    public function array(): array
    {
        return [
            ['SBU', 'Nasi Kotak Rapat (VIP)', 45000, 'Kotak', 50, 'Menu lengkap + Buah (Contoh Jasa/Konsumsi)'],
            ['SSH', 'Kertas HVS A4 80gr', 55000, 'Rim', 10, 'Sinar Dunia / Setara (Contoh Barang Fisik)'],
        ];
    }

    /**
     * Header Database (Akan didorong ke Baris 7 oleh Event di bawah)
     */
    public function headings(): array
    {
        return [
            'Kategori (Wajib)', 
            'Nama Barang / Jasa (Wajib)', 
            'Harga Satuan (Wajib)', 
            'Satuan (Wajib)', 
            'Volume (Wajib)', 
            'Keterangan'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, 'B' => 45, 'C' => 20, 'D' => 15, 'E' => 15, 'F' => 40,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling header tabel (Baris 7 nanti) akan diatur di Events
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // 1. GESER KE BAWAH 7 BARIS (6 Instruksi + 1 Spasi Kosong)
                // Ini yang membuat Header utama turun ke Baris 8
                $sheet->insertNewRowBefore(1, 7);

                // 2. TULIS JUDUL & KAMUS (Baris 1-6)
                $sheet->setCellValue('A1', 'PANDUAN PENGISIAN RKA (BACA DULU)');
                $sheet->mergeCells('A1:F1'); 
                
                $sheet->setCellValue('A2', 'JENIS (KATEGORI)');
                $sheet->setCellValue('B2', 'PENJELASAN');
                $sheet->setCellValue('C2', 'CONTOH ITEM');
                
                $sheet->setCellValue('A3', 'SSH (Barang)');
                $sheet->setCellValue('B3', 'Barang Fisik, Aset, Persediaan, ATK');
                $sheet->setCellValue('C3', 'Laptop, Kertas, Meja, Tinta');
                
                $sheet->setCellValue('A4', 'SBU (Jasa/Lain)');
                $sheet->setCellValue('B4', 'Jasa, Honor, Sewa, Makanan, Perjas');
                $sheet->setCellValue('C4', 'Honor Narasumber, Sewa Tenda');

                $sheet->setCellValue('A5', 'ATURAN HARGA');
                $sheet->setCellValue('B5', 'Wajib Angka Murni (Tanpa "Rp" / Titik).');
                $sheet->setCellValue('C5', 'Benar: 50000 | Salah: 50.000');

                $sheet->setCellValue('A6', 'ATURAN VOLUME');
                $sheet->setCellValue('B6', 'Wajib Angka. Desimal pakai TITIK (.)');
                $sheet->setCellValue('C6', 'Benar: 1.5 atau 10 | Salah: 1,5');

                // Merge Kolom Penjelasan
                foreach(range(2, 6) as $row) {
                    $sheet->mergeCells("C{$row}:F{$row}");
                }

                // 3. STYLING PANEL INSTRUKSI
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                $sheet->getStyle('A2:F6')->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => 'center'],
                ]);

                // Warna Header Tabel Panduan
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']]
                ]);

                // Warna Warning Harga & Volume
                $sheet->getStyle('A5:F5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FEF3C7');
                $sheet->getStyle('A6:F6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DBEAFE');
                $sheet->getStyle('B5')->getFont()->getColor()->setARGB('DC2626');
                $sheet->getStyle('B6')->getFont()->getColor()->setARGB('1E40AF');

                // --- BARIS 7 SENGAJA DIBIARKAN KOSONG (SPASI) ---

                // 4. HEADER TABEL INPUT (Sekarang di Baris 8)
                $sheet->getStyle('A8:F8')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']], // Indigo
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
                $sheet->getRowDimension(8)->setRowHeight(30);

                // 5. DROPDOWN (Mulai Baris 9)
                $validation = $sheet->getCell('A9')->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"SSH,SBU"');

                // Terapkan sampai baris 100
                for ($i = 9; $i <= 100; $i++) {
                    $sheet->getCell("A{$i}")->setDataValidation(clone $validation);
                }
            },
        ];
    }
}