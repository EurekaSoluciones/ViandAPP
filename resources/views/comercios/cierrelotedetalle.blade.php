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
                    <strong> Cant. Consumos: </strong>{{count($lote->movimientos)}}
                    <br>
                    <strong>Observaciones: </strong>{{$lote->observaciones}}
                </h5>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <div class="content">
        <div class="row">
        <div class="card col-md-12 ml-2 mr-2">
            <div class="card-body">

                <div class="table-responsive">
                    <table id="tabla" class="table table-striped table-hover dataTable">
                        <thead class="thead">
                        <tr>
                            <th class="sorting_asc">#</th>
                            <th class="sorting_asc">Fecha</th>
                            <th class="sorting_asc">Persona</th>
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
                                <td class="text-center">{{$movimiento->cc}}</td>
                                <td ><i class='{{$movimiento->articulo->icon}}'></i> {{$movimiento->articulo->descripcion}}</td>
                                <td class="text-center">{{$movimiento->cantidad}}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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

            $('#tabla').DataTable({
                'paging'      : false,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                'info'        : false,
                'autoWidth'   : false,
                'language': {
                    'lengthMenu': 'Mostrar _MENU_ registros por página',
                    'zeroRecords': 'No existen registros',
                    'info': 'Mostrando Página _PAGE_ de _PAGES_',
                    'infoEmpty': 'No hay registros disponibles',
                    'infoFiltered': '(filtrando desde _MAX_ registros totales)',
                    'search':'Buscar'
                },
            })
        })
    </script>


@endsection
