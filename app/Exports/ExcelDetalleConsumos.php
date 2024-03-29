<?php

namespace App\Exports;

use App\Models\Comercio;
use App\Models\StockMovimiento;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelDetalleConsumos implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithHeadingRow, WithDrawings
{

    protected $persona;
    protected $comercios;
    protected $fechaDesde;
    protected $fechaHasta;

    public function __construct(Carbon $fechaDesde, Carbon $fechaHasta, $comercios, int $persona )
    {
        $this->persona = $persona;
        $this->comercios = $comercios;
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
    }

    public function collection()
    {
        return StockMovimiento::devolverDetalleConsumoxPersona( $this->fechaDesde,$this->fechaHasta,  $this->comercios , $this->persona );

    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {

        return [
            [],
            ['DETALLE DE CONSUMOS'],
            [],
            [],
            [],
            ['Fecha Desde', $this->fechaDesde->format('d/m/Y') , '', 'Fecha Hasta', $this->fechaHasta->format('d/m/Y')],
            ['Comercio' , $this->devolverComercios()],
            [],
            [
            'CUIT',
            'APELLIDO y NOMBRE',
            'CC',
            'SITUACION',
            'COMERCIO',
            'DESAYUNOS',
            'VIANDAS']
        ];

    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A2:G2');
        return [
            // Style the first row as bold text.
            'A2'  => [
                'font' => ['bold' => true, 'size'=>18],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT ],
            ],
            'A1:G4' =>
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

    public function devolverComercios()
    {
        $nombresComercios="";
        if ($this->comercios != null)
        {
            foreach ($this->comercios as $idcomercio)
            {
                $comercio=Comercio::devolverComercioxId($idcomercio);
                $nombresComercios=$nombresComercios.$comercio->nombrefantasia.", ";

            }
        }
        if (strlen($nombresComercios)>1)
            $nombresComercios=substr($nombresComercios,0, strlen($nombresComercios)-2);
        return $nombresComercios;


    }

}
