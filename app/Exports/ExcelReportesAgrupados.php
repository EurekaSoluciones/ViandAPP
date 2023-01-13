<?php

namespace App\Exports;

use App\Models\StockMovimiento;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelReportesAgrupados implements FromArray, WithMultipleSheets
{

    protected $persona;
    protected $comercio;
    protected $fechaDesde;
    protected $fechaHasta;

    protected $sheets;

    public function __construct(Carbon $fechaDesde, Carbon $fechaHasta, int $comercio, int $persona )
    {
        $this->persona = $persona;
        $this->comercio = $comercio;
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
        $this->sheets = ['CC'];
    }

    public function array(): array
    {
        return $this->sheets;
    }

    public function sheets(): array
    {
        $sheets = [
            new \App\Exports\ReportesxCCSheet( $this->fechaDesde,$this->fechaHasta,  $this->comercio , $this->persona),
            new \App\Exports\ReportesxSituacionSheet( $this->fechaDesde,$this->fechaHasta,  $this->comercio , $this->persona),
            new \App\Exports\ReportesxComercioSheet( $this->fechaDesde,$this->fechaHasta,  $this->comercio , $this->persona),
            new \App\Exports\ReportesxPersonaSheet( $this->fechaDesde,$this->fechaHasta,  $this->comercio , $this->persona),
        ];

        return $sheets;
    }

}
