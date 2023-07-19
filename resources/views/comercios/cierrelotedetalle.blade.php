@extends('adminlte::page')

@section('template_title')
    Cierre de Lote
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                        <i class="fas fa-clipboard-check"></i>  Cierre de Lote # {{$lote->id}}
                        <br>

                    </h1>
                </div><!-- /.col -->
                <div class="box-tools text-right">


                </div>


            </div><!-- /.row -->
            <div class="callout callout-danger">
                <h5>
                    <strong> Comercio: </strong>{{$lote->comercio->nombrefantasia}}
                    <br>
                    <strong> Fecha: </strong>{{\Carbon\Carbon::parse( $lote->fecha )->format('d/m/Y')}}
                    <br>
                    <strong> Cant. Operaciones: </strong>{{count($lote->movimientos)}}
                    <br>
                    <strong> Cant. Consumos: </strong>{{$lote->movimientos->sum('cantidad')}}
                    <br>
                    <strong><i class='fas fa-mug-hot'></i> Desayunos: </strong> Desayunos: {{$lote->desayunos->sum('cantidad')}}
                    <br>
                    <strong> <i class='fas fa-utensils'></i> Viandas: </strong> Viandas: {{$lote->viandas->sum('cantidad')}}
                    <br>
                    <strong>Observaciones: </strong>{{$lote->observaciones}}
                    <br>
                    <strong>Visado: </strong>{{$lote->visado?"SI":"NO"}}
                </h5>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <div class="content">
        <div class="row">
        <div class="card col-md-12 ml-2 mr-2">
            <div class="card-body">
                <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                    <div id="tabla_wrapper" class="table-responsive">
                        <table id="tabla" class="table table-striped table-hover dataTable">
                        <thead class="thead">
                        <tr>
                            <th class="sorting_asc">#</th>
                            <th class="sorting_asc">Fecha</th>
                            <th class="sorting_asc">Persona</th>
                            <th class="text-center sorting_asc">Situación</th>
                            <th class="text-center sorting_asc">CC</th>
                            <th class="sorting_asc">Articulo</th>
                            <th class="text-center sorting_asc">Cantidad</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($lote->movimientos as $movimiento)
                            <tr>
                                <td >{{$movimiento->id}}</td>
                                <td >{{\Carbon\Carbon::parse( $movimiento->fecha )->format('d/m/Y')}}</td>
                                <td>{{$movimiento->persona->fullname}}</td>
                                <td class="text-center">{{$movimiento->situacion}}</td>
                                <td class="text-center">{{$movimiento->cc}}</td>
                                <td ><i class='{{$movimiento->articulo->icon}}'></i> {{$movimiento->articulo->descripcion}}</td>
                                <td class="text-center">{{$movimiento->cantidad}}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-12 col-md-6">
                            <div class="dt-buttons btn-group flex-wrap">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

@endsection

@section("js")
    <script type="text/javascript">

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


        $(function () {

            //Date and time picker
            //$('#fechadesdedatetime').datetimepicker({ locale: 'es' , format: 'DD/MM/YYYY HH:mm', icons: { time: 'far fa-clock' } });

            $('#fechadesdedatetime').datetimepicker({locale: 'es', format: 'DD/MM/YYYY'});
            $('#fechahastadatetime').datetimepicker({locale: 'es', format: 'DD/MM/YYYY'});


        })

        $(document).ready(function() {
            $('.js-example-basic-single').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('.table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'lengthMenu': [ [25, 50, 100,-1], [25, 50, 100, 'Todos'] ],
                'fixedHeader': true,
                'autoWidth': false,

                'language': {
                    'search':         'Buscar:',
                    'lengthMenu': 'Mostrar _MENU_ registros por página',
                    'zeroRecords': 'No existen registros',
                    'info': 'Mostrando Página _PAGE_ de _PAGES_',
                    'infoEmpty': 'No hay registros disponibles',
                    'infoFiltered': '(filtrando desde _MAX_ registros totales)',
                    'paginate': {
                        'first':      'Primero',
                        'last':       'Ultimo',
                        'next':       'Siguiente',
                        'previous':   'Anterior'
                    },
                },

                'buttons': [
                    "copy", "csv", "excel", "pdf",

                ]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>


@endsection
