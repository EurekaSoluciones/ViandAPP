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
                        <i class="fas fa-comment"></i>  Notificaciones
                    </h1>
                </div><!-- /.col -->
                @if(auth()->user()->perfil->id==1 || auth()->user()->perfil->id==2 )
                <div class="box-tools text-right">

                    <a  href="{{ route('notificaciones.create') }}"  class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>

                </div>
                @endif

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
                        <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                            <div id="tabla_wrapper" class="table-responsive">
                            <div class="table-responsive">
                            <table id="tabla" class="table table-striped table-hover dataTable">
                                <thead class="thead">
                                    <tr>
										<th class="sorting_desc">Fecha</th>
                                        <th>Título</th>
										<th>Descripcion</th>
										<th>Ingresada Por</th>
                                        <th>Alcance</th>
                                        <th>Leido Por</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notificaciones as $notificacion)
                                        <tr>
                                            <td>{{\Carbon\Carbon::parse( $notificacion->fecha )->format('d/m/Y') }}</td>
                                            <td>{{ $notificacion->titulo }}</td>
											<td>{{ $notificacion->descripcion }}</td>
											<td>{{ $notificacion->usuario->name }}</td>
											<td>{{ count($notificacion->personas) }}</td>
											<td>{{count($notificacion->personasqueleyeron)}}</td>

											<td>

                                                <form action="{{ route('notificaciones.destroy',$notificacion->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-warning" href="{{ route('notificaciones.show',$notificacion->id) }}" title="Info"><i class="fas fa-info-circle"></i> </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Borrar" ><i class="fas fa-trash" ></i> </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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


        })

        $(document).ready(function() {
            $('.js-example-basic-single').select2();

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
