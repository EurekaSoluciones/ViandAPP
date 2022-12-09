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
                        <i class="fas fa-users-cog"></i>  Usuarios
                    </h1>
                </div><!-- /.col -->
                <div class="box-tools text-right">

                    <a  href="{{ route('usuarios.create') }}"  class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>

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

                    <div class="card-body">
                        <div class="row">
                        </div>
                        <div class="table-responsive">
                            <table id="tabla" class="table table-striped table-hover dataTable">
                                <thead class="thead">
                                    <tr>
                                        <th>#</th>
										<th >Login</th>
										<th>Nombre y Apellido</th>
										<th>Perfil</th>
										<th>Activo?</th>
                                        <th>Fecha Baja</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($usuarios as $usuario)
                                        <tr>
                                            <td>{{ $usuario->id }}</td>
											<td class="sorting_asc">{{ $usuario->email }}</td>
											<td class="sorting_asc">{{ $usuario->name }}</td>
											<td><i class="{{$usuario->perfil->iconclass}}"></i> {{ $usuario->perfil->descripcion }}</td>
											<td>{{--<input type="checkbox" name="esactivo" disabled="" {{ ($usuario->activo) ? "checked" : "" }}>--}}</td>
                                            <td>{{--{{ ($persona->fechabaja!=null)?$persona->fechabaja->format('d-m-Y') :"" }}--}}</td>
											<td>
                                                <form action="{{ route('usuarios.reiniciarclave',$usuario->id) }}" method="get" class="form-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Reiniciar Contraseña" ><i class="fas fa-lock-open" ></i> </button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{ route('usuarios.destroy',$usuario->id) }}" method="POST" class="form-inline">
                                                    <a class="btn btn-sm btn-info" href="{{ route('usuarios.edit',$usuario->id) }}" title="Modificar"><i class="fas fa-pencil-alt"></i> </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Inactivar" ><i class="fas fa-trash" ></i> </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {!! $usuarios->links() !!}
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
                'paging'      : true,
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
