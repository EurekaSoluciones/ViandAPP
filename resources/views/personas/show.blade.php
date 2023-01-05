@extends('adminlte::page')

@section('template_title')
    Modificar Persona
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h5>
                        <i class="fas fa-user"></i> Persona
                    </h5>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="callout callout-danger">
                    <h5>
                        <strong> {{$persona->apellido.' '.$persona->nombre }}</strong>
                        <br>
                    </h5>
                    <div class="form-inline">
                    <div class="col-md-6">
                        <h6>
                            <strong> DNI: </strong>
                            {{$persona->dni }}
                            <br>
                            <strong> CUIT: </strong>

                            {{$persona->cuit}}


                        </h6>
                    </div>
                    <div class="col-md-6">
                        <h6>

                            <strong> Situación: </strong>
                            {{$persona->situacion}}
                            <br>
                            <strong> CC: </strong>

                            {{$persona->cc}}
                        </h6>
                    </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="row">
            <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Stock Disponible</h3>
                </div>

                <div class="card-body">
                    <div id="tabla_wrapper" class="table-responsive">

                        <table id="tabla" class="table table-striped table-hover dataTable" role="grid" aria-describedby="tabla_info">
                            <thead>
                            <tr role="row">
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Articulo</th>
                                <th>CC</th>
                                <th>Situacion</th>
                                <th>Asignado</th>
                                <th>Saldo</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($persona->stock as  $stock)
                                <tr>
                                    <td >{{ $stock->fechadesde->formatLocalized('%d/%m/%Y')}}</td>

                                    <td>{{ $stock->fechahasta->formatLocalized('%d/%m/%Y')}}</td>

                                    <td>{{ $stock->articulo->descripcion }}</td>
                                    <td class="text-center">{{ $stock->cc }}</td>
                                    <td class="text-center">{{ $stock->situacion }}</td>
                                    <td class="text-center">{{ $stock->stock }}</td>
                                    <td class="text-center">{{ $stock->saldo }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>

            </div>
        </div>

            <div class="col-md-6">
                <div class="card card-purple">
                    <div class="card-header">
                        <h3 class="card-title">Ultimos Movimientos (2 meses)</h3>
                    </div>

                    <div class="card-body">
                        <div id="tabla_wrapper" class="table-responsive">

                            <table id="tabla" class="table table-striped table-hover dataTable" role="grid" aria-describedby="tabla_info">
                                <thead>
                                <tr role="row">
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Articulo</th>
                                    <th>Comercio</th>
                                    <th>Cantidad</th>
                                    <th>Obs</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($persona->ultimosmovimientos as  $movimiento)
                                    <tr>
                                        <td >{{ $movimiento->fecha->formatLocalized('%d/%m/%Y')}}</td>
                                        <td>{{ $movimiento->tipomovimiento->descripcion}}
                                            @if ($movimiento->tipomovimiento->operacion=="INC")
                                                <i class="fas fa-plus-circle text-green"></i>
                                            @else
                                                <i class="fas fa-minus-circle text-red" ></i>
                                            @endif
                                        </td>

                                        <td>{{ $movimiento->articulo->descripcion }}</td>
                                        <td >@if ($movimiento->comercio!=null){{ $movimiento->comercio->nombrefantasia }} @endif</td>
                                        <td class="text-center">{{ $movimiento->cantidad }}</td>
                                        <td class="text-center">
                                            @if ($movimiento->observaciones!="")
                                            <i class="fas fa-comment" title={{$movimiento->observaciones}}></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>
@endsection


@section("js")
    <script type="text/javascript">
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })


            $('#tabla').DataTable({
                'paging'      : false,
                'lengthChange': false,
                'searching'   : false,
                'ordering'    : true,
                'info'        : false,
                'autoWidth'   : false,
                'language': {
                    'lengthMenu': 'Mostrar _MENU_ registros por página',
                    'zeroRecords': 'No existen registros',
                    'info': 'Mostrando Página _PAGE_ de _PAGES_',
                    'infoEmpty': 'No hay registros disponibles',
                    'infoFiltered': '(filtrando desde _MAX_ registros totales)',
                },
            })


        })

        $(document).ready(function() {
            $('.js-example-basic-single').select2();
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

    </script>


@endsection
