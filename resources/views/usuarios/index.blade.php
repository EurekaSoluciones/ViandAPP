@extends('adminlte::page')

@section('template_title')
    Usuarios
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

                        <form class="form-horizontal">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="apellido" class="col-sm-2 col-form-label">Nombre y Apellido</label>
                                    <div class="col-sm-4">
                                        <input type="text" class='form-control' name="nombre" id='nombre' placeholder='Apellido' value={{$nombre}}>

                                    </div>
                                    <label for="nombre" class="col-sm-2 col-form-label">Login</label>
                                    <div class="col-sm-4">
                                        <input type="text" class='form-control' name="login"  id='login' placeholder='login' value={{$login}}>
                                    </div>
                                </div>

                                <div class="form-group row">

                                    <label for="dni" class="col-sm-2 col-form-label">Perfil</label>
                                    <div class="col-sm-4">
                                    <select class='js-example-basic-single  col-md-12' name="perfil" required>

                                        <option disabled value="" hidden selected>Seleccione Perfil...</option>
                                        @foreach($perfiles as  $key => $value)

                                            <option  value="{{ $key }}"> {{ $value }}  </option>
                                        @endforeach
                                    </select>
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
{{--                                        <th>#</th>--}}
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
{{--                                            <td>{{ $usuario->id }}</td>--}}
											<td class="sorting_asc">{{ $usuario->email }}</td>
											<td class="sorting_asc">{{ $usuario->name }}</td>
											<td><i class="{{$usuario->perfil->iconclass}}"></i> {{ $usuario->perfil->descripcion }}</td>
											<td><input type="checkbox" name="esactivo" disabled="" {{ ($usuario->activo) ? "checked" : "" }}></td>
                                            <td>{{ ($usuario->fechabaja!=null)?$usuario->fechabaja->format('d-m-Y') :"" }}</td>
											<td>
                                                @if($usuario->fechabaja==null)
                                                    <form action="{{ route('usuarios.reiniciarclave',$usuario->id) }}" method="get" class="form-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning" title="Reiniciar Contrase??a" ><i class="fas fa-lock-open" ></i> </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                @if($usuario->fechabaja!=null)
                                                    <form action="{{ route('usuarios.reactivar',$usuario->id) }}" method="post" class="form-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning" title="Reactivar" ><i class="fas fa-recycle" ></i> </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('usuarios.destroy',$usuario->id) }}" method="POST" class="form-inline">
                                                        <a class="btn btn-sm btn-info" href="{{ route('usuarios.edit',$usuario->id) }}" title="Modificar"><i class="fas fa-pencil-alt"></i> </a>
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Inactivar" ><i class="fas fa-trash" ></i> </button>
                                                    </form>
                                                @endif
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



        })

        $(document).ready(function() {
            $('.js-example-basic-single').select2();


            $('#tabla').DataTable({
                'paging'      : false,
                'lengthChange': false,
                'searching'   : false,
                'ordering'    : true,
                'info'        : false,
                'autoWidth'   : false,
                'language': {
                    'lengthMenu': 'Mostrar _MENU_ registros por p??gina',
                    'zeroRecords': 'No existen registros',
                    'info': 'Mostrando P??gina _PAGE_ de _PAGES_',
                    'infoEmpty': 'No hay registros disponibles',
                    'infoFiltered': '(filtrando desde _MAX_ registros totales)',
                    'search':'Buscar',

                },
                order: [[1, 'asc']],

            })



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
