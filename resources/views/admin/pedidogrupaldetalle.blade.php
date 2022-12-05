@extends('adminlte::page')

@section('template_title')
    Pedido Grupal
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                        <i class="fas fa-people-carry"></i>  Pedido Grupal # {{$pedido->id}}
                        <br>

                    </h1>
                </div><!-- /.col -->
                <div class="box-tools text-right">


                </div>


            </div><!-- /.row -->
            <div class="row">
            <div class="col-md-7">
                <div class="callout callout-danger">
                    <h6>
                        <strong> Comercio: </strong>{{$pedido->comercio->nombrefantasia}}
                        <br>
                        <strong> Fecha: </strong>{{\Carbon\Carbon::parse( $pedido->fecha )->format('d/m/Y')}}
                        <br>
                        <strong> Cant. Personas: </strong>{{count($pedido->items)}}
                        <br>
                        <strong> <i class='{{$pedido->items[0]->articulo->icon}}'></i> Cant. {{ $pedido->items[0]->articulo->descripcion }}s: </strong>{{$pedido->items->sum('cantidad')}}
                        <br>
                        <strong> Ingresado Por:  </strong>{{ $pedido->usuario->name}}
                        <br>
                        <strong>Observaciones: </strong>{{$pedido->observaciones}}
                    </h6>
                </div>
            </div>
            <div class="col-md-5">
                <div class="callout callout-success">
                    <h6>
                        <strong> Estado: </strong>
                        @if ($pedido->fechacumplido != null)
                            <span class="right badge badge-success">Cumplido</span>
                            <br>
                            <strong> Fecha Cumplido: </strong> {{\Carbon\Carbon::parse( $pedido->fechacumplido )->format('d/m/Y')}}
                            <br>
                            <strong> Cumplido Por:  </strong>{{ $pedido->usuariocumple->name}}
                            <br>
                        @else
                            <span class="right badge badge-danger">Pendiente</span>
                        @endif

                        <br>


                    </h6>
                </div>
            </div>
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
                            <th class="sorting_asc">Persona</th>
                            <th class="sorting_asc">Situación</th>
                            <th class="text-center sorting_asc">CC</th>
                            <th class="sorting_asc">Articulo</th>
                            <th class="text-center sorting_asc">Cantidad</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($pedido->items as $item)
                            <tr>
                                <td>{{$item->persona->fullname}}</td>
                                <td class="text-center">{{$item->persona->situacion}}</td>
                                <td class="text-center">{{$item->persona->cc}}</td>
                                <td ><i class='{{$item->articulo->icon}}'></i> {{$item->articulo->descripcion}}</td>
                                <td class="text-center">{{$item->cantidad}}</td>

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
