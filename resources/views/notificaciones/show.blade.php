@extends('adminlte::page')

@section('template_title')
    Detalle Notificación
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h5>
                        <i class="fas fa-comment"></i> Notificación
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
                        <strong> {{$notificacion->titulo }}</strong>
                        <br>
                    </h5>
                    <div class="form-inline">
                        <div class="col-md-3">
                            <h6>
                                <strong> Fecha: </strong>
                                {{\Carbon\Carbon::parse($notificacion->fecha)->format('d/m/Y')}}
                                <br>
                                <strong> Ingresada Por: </strong>
                                {{$notificacion->usuario->name}}
                                <br>
                            </h6>
                        </div>
                        <div class="col-md-9">
                            <h6>

                                <strong> Descripción: </strong>
                                {{$notificacion->descripcion}}
                                <br>

                            </h6>
                        </div>
                    </div>
                </div>
            </div>


        </div>


            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Personas alcanzadas</h3>
                </div>
                <div class="card-body">
                    <div id="tabla_wrapper" class="table-responsive">

                        <table id="tabla" class="table table-striped table-hover dataTable" role="grid" aria-describedby="tabla_info">
                            <thead>
                            <tr role="row">
                                <th >Persona</th>
                                <th class="text-center">Leída</th>
                                <th class="text-center">Fecha Lectura</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($notificacion->personas as  $persona)
                                <tr>
                                    <td  >{{$persona->persona->fullname}}</td>
                                    <td class="text-center"><input type="checkbox" disabled="" {{ ($persona->leido) ? "checked" : "" }}></td>
                                    <td  class="text-center">{{$persona->fechalectura ==null?"":\Carbon\Carbon::parse($persona->fechalectura)->format('d/m/Y')}}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>

            </div>
            <!-- /.card -->


    </section>
@endsection


@section("js")
    <script type="text/javascript">
        $(function () {

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



