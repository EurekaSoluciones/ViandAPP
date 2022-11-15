@extends('adminlte::page')

@section('template_title')
    Personas
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-11">
                    <h1>
                        <i class="fas fa-file-excel"></i>  Asignaciones
                    </h1>
                </div><!-- /.col -->
                <div class="box-tools text-right">


                </div>


            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <form method="POST" action="{{ route('confirmarimportacion') }}"  role="form" enctype="multipart/form-data">
        @csrf
    <div class="card">

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

            <form class="form-horizontal">
                <div class="card-body">
                    <div class="form-group row">

                        <label for="fechadesde" class="col-sm-2 col-form-label">Fecha Desde</label>
                        <div class="col-sm-4">

                            <div class="input-group date" id="fechadesdedatetime" data-target-input="nearest">

                                <input type="text" name ='fechadesde', class = 'form-control datetimepicker-input'
                                 placeholder = 'Fecha Hasta' id='fechadesde' required  data-target= '#fechadesdedatetime'>
                                <div class="input-group-append" data-target="#fechadesdedatetime" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>

                            {!! $errors->first('fechadesde', '<div class="invalid-feedback">:message</div>') !!}
                        </div>

                        <label for="fechahasta" class="col-sm-2 col-form-label">Fecha Hasta</label>
                        <div class="col-sm-4">

                            <div class="input-group date" id="fechahastadatetime" data-target-input="nearest">

                                <input type="text" name ='fechahasta', class = 'form-control datetimepicker-input'
                                       placeholder = 'Fecha Hasta' id='fechahasta' required  data-target= '#fechahastadatetime'>
                                <div class="input-group-append" data-target="#fechahastadatetime" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>

                            {!! $errors->first('fechahasta', '<div class="invalid-feedback">:message</div>') !!}
                        </div>
                    </div>


                    <button type="submit" class="btn btn-info float-right"><i class="fa fa-save"></i> Importar</button>

                </div>


            </form>

            <div class="card-body">
                <div class="row">
                </div>
                <div class="table-responsive">
                    <table id="tabla" class="table table-striped table-hover dataTable">
                        <thead class="thead">
                        <tr>
                            <th class="sorting_asc">DNI</th>
                            <th class="sorting_asc">Apellido y Nombre</th>
                            <th class="text-center sorting_asc">CC</th>
                            <th class="text-center sorting_asc">Desayunos</th>
                            <th class="text-center sorting_asc">Viandas</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($asignaciones as $asignacion)
                            <tr>
                                <td class="col-md-3">{{$asignacion->dni}}</td>
                                <td>{{$asignacion->apellidoynombre}}</td>
                                <td class="text-center col-md-2">{{$asignacion->cc}}</td>
                                <td class="text-center col-md-2">{{$asignacion->desayunos}}</td>
                                <td class="text-center col-md-2">{{$asignacion->viandas}}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">

            </div>


    </div>

    </form>
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
