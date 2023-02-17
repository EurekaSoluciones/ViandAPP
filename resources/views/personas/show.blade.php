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

        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link  navbar-primary active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="false">Stock Disponible</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link navbar-purple " id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="true">Ultimos Movimientos (2 meses)</a>
                    </li>

                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    <div class="tab-pane fade active show" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                        <div class="card-header">
                            <h3 class="card-title">Stock Disponible</h3>
                        </div>
                        <div class="card-body">
                            <div id="tabla_wrapper" class="table-responsive">

                                <table id="tabla" class="table table-striped table-hover dataTable" role="grid" aria-describedby="tabla_info">
                                    <thead>
                                    <tr role="row">
                                        <th  class="text-center">Desde</th>
                                        <th  class="text-center">Hasta</th>
                                        <th>Articulo</th>
                                        <th  class="text-center">CC</th>
                                        <th  class="text-center">Situacion</th>
                                        <th class="text-center">Asignado</th>
                                        <th class="text-center">Saldo</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($persona->stock as  $stock)
                                        <tr>
                                            <td  class="text-center">{{\Carbon\Carbon::parse($stock->fechadesde)->format('d/m/Y') }}</td>

                                            <td class="text-center">{{ \Carbon\Carbon::parse($stock->fechahasta)->format('d/m/Y')}}</td>

                                            <td><i class='{{$stock->articulo->icon}}'></i> {{ $stock->articulo->descripcion }}</td>
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
                    <div class="tab-pane fade " id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
                        <div class="card-header">
                            <h3 class="card-title">Últimos movimientos (2 meses)</h3>
                        </div>
                        <div class="card-body">
                            <div id="tabla_wrapper" class="table-responsive">

                                <table id="tabla" class="table table-striped table-hover dataTable" role="grid" aria-describedby="tabla_info">
                                    <thead>
                                    <tr role="row">
                                        <th  class="text-center">Fecha</th>
                                        <th>Tipo</th>
                                        <th>Articulo</th>
                                        <th>Comercio</th>
                                        <th  class="text-center">Cantidad</th>
                                        <th>Realizado por</th>
                                        <th>Obs</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($persona->ultimosmovimientos as  $movimiento)
                                        <tr>
                                            <td  class="text-center">{{\Carbon\Carbon::parse(  $movimiento->fecha)->format('d/m/Y')}}</td>
                                            <td>{{ $movimiento->tipomovimiento->descripcion}}
                                                @if ($movimiento->tipomovimiento->operacion=="INC")
                                                    <i class="fas fa-plus-circle text-green"></i>
                                                @else
                                                    <i class="fas fa-minus-circle text-red" ></i>
                                                @endif
                                            </td>

                                            <td><i class='{{$movimiento->articulo->icon}}'></i> {{$movimiento->articulo->descripcion }}</td>
                                            <td >@if ($movimiento->comercio!=null){{ $movimiento->comercio->nombrefantasia }} @endif</td>
                                            <td class="text-center">{{ $movimiento->cantidad }}</td>
                                            <td>{{ $movimiento->usuario->name }}</td>
                                            <td>

                                                {{$movimiento->observaciones}}

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
            <!-- /.card -->
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
