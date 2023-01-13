<style>
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 300;
        src: local('Roboto Light'), local('Roboto-Light'), url(https://fonts.gstatic.com/s/roboto/v20/KFOlCnqEu92Fr1MmSU5vAw.ttf) format('truetype');
    }
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 400;
        src: local('Roboto'), local('Roboto-Regular'), url(https://fonts.gstatic.com/s/roboto/v20/KFOmCnqEu92Fr1Me5Q.ttf) format('truetype');
    }
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 700;
        src: local('Roboto Bold'), local('Roboto-Bold'), url(https://fonts.gstatic.com/s/roboto/v20/KFOlCnqEu92Fr1MmWUlvAw.ttf) format('truetype');
    }

    /* Añado la declaración de font-family, para usar la fuente de Google Fonts en este PDF */

    body {
        font-family: 'Roboto', serif;
        color: #303030;
    }

    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .card-header {
        border: 1px solid black;
        background-color: #c7ced5;
    }
    /* todo el otro CSS necesario para el PDF */
    /* ... */
</style>


                    <div class="form-group row">
                        <label for="fechadesde" class="col-form-label col-sm-2">Fecha Desde</label>
                        <div class="col-sm-4">
                            <div class="input-group date" id="fechaDesdeDatetime" data-target-input="nearest">

                                <input type="text" value="{{\Carbon\Carbon::parse( $fechaDesde)->format('d/m/Y')}}" name ='fechaDesde', class = 'form-control datetimepicker-input'
                                       placeholder = 'Fecha Desde' id='fechaDesde' required  data-target= '#fechaDesdeDatetime'>
                                <div class="input-group-append" data-target="#fechaDesdeDatetime" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>
                        </div>


                        <label for="fechahasta" class="col-form-label  col-sm-2">Fecha Hasta</label>

                        <div class="col-sm-4">
                            <div class="input-group date" id="fechaHastaDatetime" data-target-input="nearest">

                                <input type="text" value="{{\Carbon\Carbon::parse( $fechaHasta)->format('d/m/Y')}}" name ='fechaHasta', class = 'form-control datetimepicker-input'
                                       placeholder = 'Fecha Hasta' id='fechaHasta' required  data-target= '#fechaDesdeDatetime'>
                                <div class="input-group-append" data-target="#fechaHastaDatetime" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>

                       </div>
                    </div>
                    <div class="form-group row">

                        <label for="comercio" class="col-form-label col-sm-2">Comercio: </label>
                        <div class="col-sm-4">

                            <select class='js-example-basic-single w-100' name="comercio" required>

                                <option  value="0" hidden {{$comercio==0?"selected":""}}>(Todos)</option>
                                @foreach($comercios as  $key => $value)

                                    <option  value="{{ $key }}" {{$comercio==$key?"selected":""}}> {{ $value }}  </option>
                                @endforeach
                            </select>
                        </div>


                        <label for="persona" class="col-form-label col-sm-2">Persona</label>
                        <div class="col-sm-4">
                            <select class='js-example-basic-single w-100' name="persona" required>

                                <option  value="0" hidden {{$persona==0?"selected":""}}>(Todas)</option>
                                @foreach($personas as  $key => $value)

                                    <option  value="{{ $key }}" {{$persona==$key?"selected":""}}> {{ $value }}  </option>
                                @endforeach
                            </select>
                        </div>
                    </div>





            <div class="card card-primary card-outline">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">
                        <i class="fas fa-tag"></i>
                        Detalle de Consumos
                    </h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla" class="table table-striped table-hover dataTable">
                            <thead class="thead">
                            <tr>
                                <th>DNI</th>
                                <th>Apellido y Nombre</th>
                                <th class="text-center">CC</th>
                                <th class="text-center">Situacion</th>
                                <th class="text-right">Desayunos</th>
                                <th class="text-right">Viandas</th>
                                <th class="text-right">Total</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($movimientos as   $movimiento)
                                <tr>
                                    <td class="sorting_asc">{{ $movimiento->dni }}</td>
                                    <td class="sorting_asc">{{ $movimiento->persona }}</td>
                                    <td class="sorting_asc  text-center">{{ $movimiento->cc }}</td>
                                    <td class="sorting_asc  text-center">{{ $movimiento->situacion }}</td>
                                    <td class="sorting_asc  text-right">{{ $movimiento->desayunos}}</td>
                                    <td class="sorting_asc  text-right">{{ $movimiento->viandas }}</td>
                                    <td class="sorting_asc  text-right">{{ $movimiento->viandas + $movimiento->desayunos }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->

            </div>




