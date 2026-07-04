<?php

namespace App\Exports;

use App\Models\User;
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
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        private readonly Collection $users,
        private readonly string $primaryColor = '7367F0',
        private readonly string $siteName = 'Mi Sistema',
        private readonly string $companyName = 'Mi Empresa S.A.C.',
    ) {}

    public function collection(): Collection
    {
        return $this->users->map(fn (User $u) => [
            $u->name,
            $u->email,
            $u->username ?? '—',
            $u->roles->first()?->name ?? '—',
            $u->perfil?->cargo ?? '—',
            $u->perfil?->area ?? '—',
            $u->perfil?->dni ?? '—',
            $u->perfil?->celular ?? $u->phone ?? '—',
            $u->status?->label() ?? '—',
            $u->email_verified_at ? 'Sí' : 'No',
            $u->last_login_at?->format('d/m/Y H:i') ?? 'Nunca',
            $u->created_at->format('d/m/Y'),
        ]);
    }

    public function headings(): array
    {
        return [
            'Nombre completo',
            'Email',
            'Usuario',
            'Rol',
            'Cargo',
            'Área',
            'DNI',
            'Teléfono',
            'Estado',
            'Email verificado',
            'Último acceso',
            'Registrado',
        ];
    }

    public function title(): string
    {
        return 'Usuarios';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,
            'B' => 30,
            'C' => 18,
            'D' => 14,
            'E' => 28,
            'F' => 28,
            'G' => 12,
            'H' => 16,
            'I' => 12,
            'J' => 16,
            'K' => 18,
            'L' => 14,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $color = ltrim($this->primaryColor, '#');
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'L';

        // Fila de encabezados (fila 2 porque agregaremos banner en fila 1)
        return [
            2 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$color]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            "A3:{$lastCol}{$lastRow}" => [
                'font' => ['size' => 9],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
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
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'L';
                $dataEnd = $lastRow;

                // ── Insertar 3 filas de banner arriba ─────────────────────
                $sheet->insertNewRowBefore(1, 3);

                // Merge banner completo
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->mergeCells("A3:{$lastCol}3");

                // Fila 1 — Nombre del sistema (grande)
                $sheet->setCellValue('A1', strtoupper($siteName));
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // Fila 2 — subtítulo + empresa + fecha
                $sheet->setCellValue('A2', "Reporte de Usuarios  ·  {$companyName}  ·  Generado: {$now}");
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 9, 'color' => ['argb' => 'FFFFFFFF'], 'italic' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // Fila 3 — separador (color claro)
                $sheet->getStyle('A3')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '22'.$color]],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(6);

                // ── Fila de encabezados (ahora fila 4) ───────────────────
                $headRow = 4;
                $sheet->getStyle("A{$headRow}:{$lastCol}{$headRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]],
                ]);
                $sheet->getRowDimension($headRow)->setRowHeight(22);

                // ── Filas de datos — zebra striping ───────────────────────
                $dataLastRow = $sheet->getHighestRow();
                for ($r = $headRow + 1; $r <= $dataLastRow; $r++) {
                    $bgArgb = ($r % 2 === 0) ? 'FFF8F7FF' : 'FFFFFFFF';
                    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgArgb]],
                        'font' => ['size' => 9],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE0E0E0']]],
                    ]);
                    $sheet->getRowDimension($r)->setRowHeight(18);
                }

                // Borde exterior de toda la tabla
                $sheet->getStyle("A{$headRow}:{$lastCol}{$dataLastRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.$color]]],
                ]);

                // ── Fila de totales al final ──────────────────────────────
                $totalRow = $dataLastRow + 1;
                $sheet->mergeCells("A{$totalRow}:K{$totalRow}");
                $dataCount = $dataLastRow - $headRow;
                $sheet->setCellValue("A{$totalRow}", "Total de registros: {$dataCount}");
                $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF'.$color]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '11'.$color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.$color]]],
                ]);
                $sheet->getRowDimension($totalRow)->setRowHeight(20);

                // Freeze panes en encabezados
                $sheet->freezePane('A'.($headRow + 1));

                // Auto-filter en encabezados
                $sheet->setAutoFilter("A{$headRow}:{$lastCol}{$headRow}");
            },
        ];
    }
}
