@extends('adminlte::page')

@section('template_title')
    Notificaciones
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-11">
                    <h1>
                        <i class="fas fa-comment"></i>  Mis Notificaciones
                    </h1>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

                <div class="card">

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                        <!-- /.card-header -->
                        <!-- form start -->
                        <form class="form-horizontal">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3 invoice-col">
                                        <b>Fecha Desde</b><br>

                                        <div class="input-group date" id="fechadesdedatetime" data-target-input="nearest">

                                            <input type="text" name="fechadesde" value="{{\Carbon\Carbon::parse( $fechadesde)->format('d/m/Y')}}" class ="form-control datetimepicker-input" placeholder ="Fecha Desde" id="fechahasta" data-target="#fechadesdedatetime">
                                            <div class="input-group-append" data-target="#fechadesdedatetime" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-3 invoice-col">
                                        <b>Fecha Hasta</b><br>

                                        <div class="input-group date" id="fechahastadatetime" data-target-input="nearest">
                                            <input type="text" name="fechahasta" value="{{\Carbon\Carbon::parse( $fechahasta)->format('d/m/Y')}}" class ="form-control datetimepicker-input" placeholder ="Fecha Hasta" id="fechahasta" data-target="#fechahastadatetime">
                                            <div class="input-group-append" data-target="#fechahastadatetime" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info float-right">Buscar</button>

                            </div>


                        </form>

                    <div class="card-body">
                        <div class="row">
                        </div>
                        <div class="table-responsive">
                            <table id="tabla" class="table table-striped table-hover dataTable">
                                <thead class="thead">
                                    <tr>
										<th class="sorting_desc">Fecha</th>
                                        <th>Título</th>
										<th>Descripcion</th>
										<th>Ingresada Por</th>
                                        <th class="text-center">Leida?</th>
                                        <th class="text-center">Fecha Lectura</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notificaciones as $notificacion)
                                        <tr>
                                            <td>{{\Carbon\Carbon::parse( $notificacion->notificacion->fecha )->format('d/m/Y') }}</td>
                                            <td>{{ $notificacion->notificacion->titulo }}</td>
											<td>{{ $notificacion->notificacion->descripcion }}</td>
											<td>{{ $notificacion->notificacion->usuario->name }}</td>
											<td class="text-center"><input type="checkbox" disabled="" {{ ($notificacion->leido) ? "checked" : "" }}></td>
											<td class="text-center">{{$notificacion->fechalectura ==null?"":\Carbon\Carbon::parse($notificacion->fechalectura)->format('d/m/Y')}}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {!! $notificaciones->links() !!}
                    </div>

                </div>


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
            $('#fechadesdedatetime').datetimepicker({ locale: 'es' , format: 'DD/MM/YYYY'});
            $('#fechahastadatetime').datetimepicker({ locale: 'es' , format: 'DD/MM/YYYY'});


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
