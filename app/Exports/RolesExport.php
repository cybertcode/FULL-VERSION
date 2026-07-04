<?php

namespace App\Exports;

use App\Models\Role;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RolesExport implements FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        private readonly Collection $roles,
        private readonly string $primaryColor = '7367F0',
        private readonly string $siteName = 'Mi Sistema',
        private readonly string $companyName = 'Mi Empresa S.A.C.',
    ) {}

    public function collection(): Collection
    {
        return $this->roles->map(fn (Role $role) => [
            $role->name,
            $role->users_count ?? $role->users->count(),
            $role->permissions->count(),
            $role->permissions->pluck('name')->join(', ') ?: '—',
            $role->created_at?->format('d/m/Y') ?? '—',
        ]);
    }

    public function headings(): array
    {
        return ['Rol', 'Usuarios asignados', 'Permisos', 'Permisos detalle', 'Creado'];
    }

    public function title(): string
    {
        return 'Roles';
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 18, 'C' => 12, 'D' => 80, 'E' => 14];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        $color = ltrim($this->primaryColor, '#');
        $siteName = $this->siteName;
        $companyName = $this->companyName;
        $now = now()->format('d/m/Y H:i');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($color, $siteName, $companyName, $now) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'E';

                $sheet->insertNewRowBefore(1, 3);

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->mergeCells("A3:{$lastCol}3");

                $sheet->setCellValue('A1', strtoupper($siteName));
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                $sheet->setCellValue('A2', "Reporte de Roles  ·  {$companyName}  ·  Generado: {$now}");
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 9, 'color' => ['argb' => 'FFFFFFFF'], 'italic' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                $sheet->getStyle('A3')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '22'.$color]],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(6);

                $headRow = 4;
                $sheet->getStyle("A{$headRow}:{$lastCol}{$headRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]],
                ]);
                $sheet->getRowDimension($headRow)->setRowHeight(22);

                $dataLastRow = $sheet->getHighestRow();
                for ($r = $headRow + 1; $r <= $dataLastRow; $r++) {
                    $bg = ($r % 2 === 0) ? 'FFF8F7FF' : 'FFFFFFFF';
                    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                        'font' => ['size' => 9],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE0E0E0']]],
                    ]);
                    $sheet->getRowDimension($r)->setRowHeight(22);
                }

                $sheet->getStyle("A{$headRow}:{$lastCol}{$dataLastRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.$color]]],
                ]);

                $totalRow = $dataLastRow + 1;
                $dataCount = $dataLastRow - $headRow;
                $sheet->mergeCells("A{$totalRow}:{$lastCol}{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", "Total de roles: {$dataCount}");
                $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF'.$color]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '11'.$color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.$color]]],
                ]);
                $sheet->getRowDimension($totalRow)->setRowHeight(20);

                $sheet->freezePane('A'.($headRow + 1));
                $sheet->setAutoFilter("A{$headRow}:{$lastCol}{$headRow}");
            },
        ];
    }
}
