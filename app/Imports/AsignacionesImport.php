<?php

namespace App\Imports;

use App\Models\Asignacion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AsignacionesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Asignacion(['cuit' => $row['cuit'],
            'situacion' => $row['situacion'],
            'apellidoynombre'=> $row['apellido_y_nombre'],
            'cc'=> $row['cc'],
            'desayunos'=> $row['desayunos'],
            'viandas' => $row['viandas'],
            'estado'=>null
        ]);
    }
}
