<?php

namespace App\Exports;

use App\Models\Comercio;
use App\Models\Persona;
use App\Models\StockMovimiento;
use App\Models\TipoMovimiento;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelBusquedaMovimientos implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithHeadingRow, WithDrawings, WithMapping
{

    protected $personas;
    protected $comercios;
    protected $tipomovimientos;
    protected $fechaDesde;
    protected $fechaHasta;

    public function __construct(Carbon $fechaDesde, Carbon $fechaHasta,  $comercios,  $personas,  $tipomovimientos )
    {
        $this->personas = $personas;
        $this->comercios = $comercios;
        $this->tipomovimientos = $tipomovimientos;
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
    }

    public function collection()
    {
        return StockMovimiento::devolverMovimientos($this->fechaDesde, $this->fechaHasta, $this->comercios, $this->personas, $this->tipomovimientos);

    }

    public function map($movimiento): array
    {
        return [
            $movimiento->persona->cuit,
            $movimiento->persona->fullname,
            $movimiento->fecha->format('d/m/Y'),
            $movimiento->tipomovimiento->descripcion,
            $movimiento->cc,
            $movimiento->situacion,
            $movimiento->articulo->descripcion,
            ($movimiento->comercio!=null?$movimiento->comercio->nombrefantasia:""),
            $movimiento->cantidad

        ];
    }
    /**
     * @inheritDoc
     */
    public function headings(): array
    {

        return [
            [],
            ['MOVIMIENTOS'],
            [],
            [],
            [],
            ['Fecha Desde', $this->fechaDesde->format('d/m/Y') , '', 'Fecha Hasta', $this->fechaHasta->format('d/m/Y')],
            ['Comercios' ,$this->devolverComercios() ],
            ['Personas' , $this->devolverPersonas()],
            ['Tipo Movimientos' , $this->devolverTipomovimientos()],
            [],
            [
                'CUIT',
                'APELLIDO y NOMBRE',
                'FECHA',
                'TIPO MOV.',
                'CC',
                'SITUACION',
                'ARTICULO',
                'COMERCIO',
                'CANTIDAD']
        ];

    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A2:I2');
        return [
            // Style the first row as bold text.
            'A2'  => [
                'font' => ['bold' => true, 'size'=>18],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT ],
            ],
            'A1:I4' =>
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
            'B8'  => ['font' => ['bold' => true, 'size'=>12],],
            'B9'  => ['font' => ['bold' => true, 'size'=>12],],

            11   => [
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

    public function devolverPersonas()
    {
        $nombresPersonas="";
        if ($this->personas != null) {
            foreach ($this->personas as $idpersona) {
                $persona = Persona::devolverPersonaxId($idpersona);
                $nombresPersonas = $nombresPersonas . $persona->fullname . ", ";

            }
        }

        if (strlen($nombresPersonas)>1)
            $nombresPersonas=substr($nombresPersonas,0, strlen($nombresPersonas)-2);
        return $nombresPersonas;


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

    public function devolverTipomovimientos()
    {
        $nombresTipos="";
        if ($this->tipomovimientos != null) {
            foreach ($this->tipomovimientos as $idtipo) {
                $tipo = TipoMovimiento::devolverMovimiento($idtipo);
                $nombresTipos = $nombresTipos . $tipo->descripcion . ", ";

            }
        }
        if (strlen($nombresTipos)>1)
            $nombresTipos=substr($nombresTipos,0, strlen($nombresTipos)-2);
        return $nombresTipos;


    }

}
