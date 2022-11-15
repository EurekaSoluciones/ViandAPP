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
                        <i class="fas fa-user"></i>  Personas
                    </h1>
                </div><!-- /.col -->
                <div class="box-tools text-right">

                    <a  href="{{ route('personas.create') }}"  class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>

                </div>


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
                                    <label for="apellido" class="col-sm-2 col-form-label">Apellido</label>
                                    <div class="col-sm-4">
                                        <input type="text" class='form-control' name="apellido" id='apellido' placeholder='Apellido' value={{$apellido}}>

                                    </div>
                                    <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                                    <div class="col-sm-4">
                                        <input type="text" class='form-control' name="nombre"  id='nombre' placeholder='Nombre' value={{$nombre}}>
                                    </div>
                                </div>

                                <div class="form-group row">

                                    <label for="dni" class="col-sm-2 col-form-label">DNI</label>

                                    <div class="col-sm-4">
                                        <input type="text" class='form-control' name="dni" id='dni' placeholder='DNI' value={{$dni}}>
                                    </div>

                                    <label for="cuit" class="col-sm-2 col-form-label">CUIT</label>
                                    <div class="col-sm-4">
                                        <input type="text" class='form-control' name="cuit" id='cuit' placeholder='CUIT' value={{$cuit}}>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="offset-2 col-sm-2">
                                        <div class="form-check">
                                            <input type="checkbox" class='form-check-input' name= id='ckActivos'  checked="">

                                            <label class="form-check-label" for="ckActivos">Solo Activos</label>
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
                                        <th>No</th>

										<th class="sorting_asc">Apellido</th>
										<th>Nombre</th>
										<th>Dni</th>
										<th>Cuit</th>
										<th>Activo?</th>
                                        <th>Fecha Baja</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($personas as $persona)
                                        <tr>
                                            <td>{{ $persona->id }}</td>
											<td>{{ $persona->apellido }}</td>
											<td>{{ $persona->nombre }}</td>
											<td>{{ $persona->dni }}</td>
											<td>{{ $persona->cuit }}</td>
											<td><input type="checkbox" name="esexterno" disabled="" {{ ($persona->activo) ? "checked" : "" }}></td>
                                            <td>{{ ($persona->fechabaja!=null)?$persona->fechabaja->format('d-m-Y') :"" }}</td>
											<td>

                                                <form action="{{ route('personas.destroy',$persona->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-warning" href="{{ route('personas.show',$persona->id) }}" title="Info"><i class="fas fa-info-circle"></i> </a>
                                                    <a class="btn btn-sm btn-info" href="{{ route('personas.edit',$persona->id) }}" title="Modificar"><i class="fas fa-pencil-alt"></i> </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Borrar"><i class="fas fa-trash" ></i> </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {!! $personas->links() !!}
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
