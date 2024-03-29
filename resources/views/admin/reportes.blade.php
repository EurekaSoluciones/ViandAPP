@extends('adminlte::page')

@section('title', 'Totales Agrupados')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-11">
                    <h1>
                        <i class="fas fa-project-diagram"></i> Totales Agrupados
                    </h1>
                </div><!-- /.col -->
                <div class="box-tools text-right">


                </div>


            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

@stop

@section('content')
    <div class="card">

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

            <form class="form-horizontal col-md-12">
                <div class="card-body">
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
                            {!! $errors->first('fechaDesde', '<div class="invalid-feedback">:message</div>') !!}
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

                            {!! $errors->first('fechaHasta', '<div class="invalid-feedback">:message</div>') !!}

                        </div>
                    </div>
                    <div class="form-group row">

                        <label for="comercio" class="col-form-label col-sm-2">Comercio</label>
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

                    <button type="submit" class="btn btn-primary float-right mr-2" onclick="marcarSalida('pantalla');"><i class="fas fa-search"></i> Buscar</button>
                    <button type="submit" class="btn btn-outline-success float-right mr-2" onclick="marcarSalida('excel');"><i class="fas fa-file-excel"></i> Exportar EXCEL</button>
                    <button type="submit" class="btn btn-outline-danger float-right mr-2" onclick="marcarSalida('pdf');"><i class="fas fa-file-pdf"></i> Exportar PDF </button>
                    <input type="hidden" name="salida" id="salida">

                </div>
            </form>
    </div>


    <div class="row">
        <section class="col-lg-6 connectedSortable ui-sortable">
            <div class="card card-primary card-outline">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">
                        <i class="fas fa-tag"></i>
                        Totales por Centro de Costo
                    </h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla" class="table table-striped table-hover dataTable">
                            <thead class="thead">
                            <tr>
                                <th>Centro Costo</th>
                                <th class="text-right">Desayunos</th>
                                <th class="text-right">Viandas</th>
                                <th class="text-right">Total</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($movimientosxCC as   $movimiento)
                                <tr>
                                    <td class="sorting_asc">{{ $movimiento->cc }}</td>
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
        </section>
        <section class="col-lg-6 connectedSortable ui-sortable">
            <div class="card card-success card-outline">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">
                        <i class="fas fa-tag"></i>
                        Totales por Situación
                    </h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla" class="table table-striped table-hover dataTable">
                            <thead class="thead">
                            <tr>
                                <th>Situación</th>
                                <th class="text-right">Desayunos</th>
                                <th class="text-right">Viandas</th>
                                <th class="text-right">Total</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($movimientosxSituacion as   $movimiento)
                                <tr>
                                    <td class="sorting_asc">{{ $movimiento->situacion }}</td>
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
        </section>
        <section class="col-lg-6 connectedSortable ui-sortable">
            <div class="card card-danger card-outline">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">
                        <i class="fas fa-tag"></i>
                        Totales por Comercio
                    </h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla" class="table table-striped table-hover dataTable">
                            <thead class="thead">
                            <tr>
                                <th>Comercio</th>
                                <th class="text-right">Desayunos</th>
                                <th class="text-right">Viandas</th>
                                <th class="text-right">Total</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($movimientosxComercio as   $movimiento)
                                <tr>
                                    <td class="sorting_asc">{{ $movimiento->comercio }}</td>
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
        </section>
        <section class="col-lg-6 connectedSortable ui-sortable">
            <div class="card card-warning card-outline">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">
                        <i class="fas fa-tag"></i>
                        Totales por Persona
                    </h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla" class="table table-striped table-hover dataTable">
                            <thead class="thead">
                            <tr>
                                <th>Persona</th>
                                <th class="text-right">Desayunos</th>
                                <th class="text-right">Viandas</th>
                                <th class="text-right">Total</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($movimientosxPersona as   $movimiento)
                                <tr>
                                    <td class="sorting_asc">{{ $movimiento->persona }}</td>
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
        </section>
    </div>
@stop


@section('footer')

        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>
        <strong>Desarrollado por <img src="vendor/adminlte/dist/img/logoEureka.jpg"></img> <a target="_blank" href="http://www.eurekasoluciones.com.ar">Eureka Soluciones Informáticas</a>.</strong> Todos los derechos reservados.

@stop


@section('js')
    <script> console.log('Hi!'); </script>


@section("js")


    <script type="text/javascript">

        $(function () {

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
            $('.select2').select2();


            $('#fechaDesdeDatetime').datetimepicker({locale: 'es', format: 'DD/MM/YYYY'});
            $('#fechaHastaDatetime').datetimepicker({locale: 'es', format: 'DD/MM/YYYY'});

        })

        $(document).ready(function() {
            $('.js-example-basic-single').select2();
            $('.js-example-basic-multiple').select2();
        });


        @if(Session::has('message'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
        toastr.success("{{ session('message') }}");
        @endif

        @if(Session::has('error'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
        toastr.error("{{ session('error') }}");
        @endif

        @if(Session::has('info'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
        toastr.info("{{ session('info') }}");
        @endif

        @if(Session::has('warning'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
        toastr.warning("{{ session('warning') }}");
        @endif


        function marcarSalida(valor)
        {
            document.getElementById("salida").value=valor;

        }
    </script>


@endsection

@stop


