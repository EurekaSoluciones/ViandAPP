<?php

namespace App\Exports;

namespace App\Exports;

use App\Models\StockMovimiento;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ReportesxCCSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithHeadingRow, WithDrawings, WithTitle
{

    protected $persona;
    protected $comercio;
    protected $fechaDesde;
    protected $fechaHasta;

    public function __construct(Carbon $fechaDesde, Carbon $fechaHasta, int $comercio, int $persona )
    {
        $this->persona = $persona;
        $this->comercio = $comercio;
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
    }

    public function collection()
    {
        return StockMovimiento::devolverReportexCC( $this->fechaDesde,$this->fechaHasta,  $this->comercio , $this->persona );

    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {

        return [
            [],
            [],
            ['TOTAL CONSUMOS POR CENTRO DE COSTO'],
            [],
            [],
            ['Fecha Desde', $this->fechaDesde->format('d/m/Y') , '', 'Fecha Hasta', $this->fechaHasta->format('d/m/Y')],
            ['Comercio' , $this->comercio],
            [],
            [
                'CENTRO DE COSTO',
                'DESAYUNOS',
                'VIANDAS']
        ];

    }

    public function title(): string
    {
        return 'CC';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A3:F3');
        return [
            // Style the first row as bold text.
            'A3'  => [
                'font' => ['bold' => true, 'size'=>18],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT ],
            ],
            'A1:F4' =>
                [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'BFBFBF'  ],
                        'endColor' => [  'argb' => 'BFBFBF' ],
                    ],
                ],
            'B6'  => ['font' => ['bold' => true, 'size'=>12],],
            'E6'  => ['font' => ['bold' => true, 'size'=>12],],
            'B7'  => ['font' => ['bold' => true, 'size'=>12],],
            9   => [
                'font' => ['bold' => true, 'size'=>12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '0094CC'],
                    'endColor' => ['argb' => '0094CC'],
                ],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER ],

            ]
        ];
    }

    public function headingRow(): int
    {
        return 3;
    }

    /**
     * @inheritDoc
     */
    public function drawings()
    {
        $drawing = new Drawing();

        $drawing->setPath(public_path('/vendor/adminlte/dist/img/logoempresa.png'));
        $drawing->setHeight(62);
        $drawing->setCoordinates('A1');

        return $drawing;
    }
}
