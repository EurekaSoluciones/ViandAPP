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
                        <i class="fas fa-store"></i>  Comercios
                    </h1>
                </div><!-- /.col -->
                @if(auth()->user()->perfil->id==1 || auth()->user()->perfil->id==2 )
                <div class="box-tools text-right">

                    <a  href="{{ route('comercios.create') }}"  class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>

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



                        <!-- /.card-header -->
                        <!-- form start -->
                        <form class="form-horizontal">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="razonsocial" class="col-sm-2 col-form-label">Razón Social</label>
                                    <div class="col-sm-4">
                                        <input type="text" class='form-control' name="razonsocial" id='razonsocial' placeholder='Razón Social' value={{$razonsocial}}>

                                    </div>
                                    <label for="nombrefantasia" class="col-sm-2 col-form-label">Nombre Fantasía</label>
                                    <div class="col-sm-4">
                                        <input type="text" class='form-control' name="nombrefantasia"  id='nombrefantasia' placeholder='Nombre' value={{$nombrefantasia}}>
                                    </div>
                                </div>

                                <div class="form-group row">

                                    <label for="cuit" class="col-sm-2 col-form-label">CUIT</label>
                                    <div class="col-sm-4">
                                        <input type="text" class='form-control' name="cuit" id='cuit' placeholder='CUIT' value={{$cuit}}>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="offset-2 col-sm-2">
                                        <div class="form-check">
                                            <input type="checkbox" class='form-check-input' name= id='ckActivos'  checked>

                                            <label class="form-check-label" for="ckActivos">Solo Activos</label>
                                        </div>
                                    </div>

                                </div>

                                <button type="submit" class="btn btn-info float-right">Buscar</button>

                            </div>


                        </form>

                    <div class="card-body">
                        <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                            <div id="tabla_wrapper" class="table-responsive">
                                <table id="tabla" class="table table-striped table-hover dataTable">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>

										<th class="sorting_asc">Razón Social</th>
										<th>Nombre Fantasía</th>
										<th>Cuit</th>
										<th>Activo?</th>
                                        <th>Fecha Baja</th>
                                        <th></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($comercios as $comercio)
                                        <tr>
                                            <td>{{ $comercio->id }}</td>
											<td>{{ $comercio->razonsocial}}</td>
											<td>{{ $comercio->nombrefantasia }}</td>
											<td>{{ $comercio->cuit }}</td>
											<td><input type="checkbox" name="esexterno" disabled="" {{ ($comercio->activo) ? "checked" : "" }}></td>
                                            <td>{{ ($comercio->fechabaja!=null)?$persona->fechabaja->format('d-m-Y') :"" }}</td>
											<td>
                                                <a class="btn btn-sm btn-primary" href="{{ route('comercios.show',$comercio->id) }}" title="Ver"><i class="fas fa-eye"></i> </a>
                                                @if(auth()->user()->perfil->id==1 || auth()->user()->perfil->id==2 )
                                                    <a class="btn btn-sm btn-info" href="{{ route('comercios.edit',$comercio->id) }}" title="Modificar"><i class="fas fa-pencil-alt"></i> </a>
                                                    @if ($comercio->activo)
                                                        <form action="{{ route('comercios.destroy',$comercio->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Borrar"><i class="fas fa-trash" ></i> </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('comercios.reactivate',$comercio->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-sm" title="Reactivar"><i class="fas fa-recycle" ></i> </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </td>
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
