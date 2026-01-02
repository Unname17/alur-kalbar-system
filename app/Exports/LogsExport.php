<?php

namespace App\Exports;

use App\Models\Kinerja\LogAktivitas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LogsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $logs;

    public function __construct($logs = null)
    {
        $this->logs = $logs;
    }

    public function collection()
    {
        // Jangan gunakan with() karena model Anda menggunakan DB manual
        return $this->logs ?: LogAktivitas::orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'WAKTU',
            'PELAKU & INSTANSI',
            'ORGANISASI (OPD)',
            'AKTIVITAS',
            'DESKRIPSI',
            'IP ADDRESS',
            'MODUL'
        ];
    }

    public function map($log): array
    {
        return [
            $log->created_at->format('d/m/Y H:i:s'),
            $log->getNamaUser(), // Memanggil fungsi manual dari model Anda
            $log->getNamaOpd(),  // Memanggil fungsi manual dari model Anda
            strtoupper($log->aktivitas), // Sesuai kolom 'aktivitas' di model
            $log->deskripsi,
            $log->ip_address,
            strtoupper($log->modul ?? 'UMUM')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0F172A']
                ],
            ],
        ];
    }
}