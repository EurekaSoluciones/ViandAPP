@extends('adminlte::page')
@section('template_title')
    Modificar Usuario
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-11">
                    <h1>
                        <i class="fas fa-user-lock"></i> Cambiar Contraseña
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


        <form class="form-horizontal" method="post" action="{{route('usuarios.guardarclave') }}"
              enctype='multipart/form-data' onSubmit="return checkPassword(this)">
            @csrf
            @method('POST')
            <div class="card-body">

                <div class="form-group row">

                    <label class="col-md-3">Nombre</label>

                    <label class="col-md-6"> {{$usuario->name}} </label>

                </div>
                <div class="form-group row">

                    <label class="col-md-3">Login</label>


                    <label class="col-md-6"> {{$usuario->email}} </label>


                </div>
                <div class="form-group row">
                    <div class="col-md-3"><label>Perfil</label>
                    </div>
                    <div class="col-md-4">
                        <label> {{$usuario->perfil->descripcion}} </label>
                    </div>

                    <br>

                </div>

                <div class="form-group row">
                    <div class="col-md-3"><label>Nueva Clave</label>
                    </div>
                    <div class="col-md-3">
                        <input type="password" class='form-control'
                               .{{($errors->has('new_password') ? ' is-invalid' : '')}} name="new_password"
                               id='new_password' placeholder='Nueva Clave'
                               value="{{ old('new_password') ? old('new_password') : "" }}" required>
                        {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}

                    </div>

                    <br>

                </div>
                <div class="form-group row">
                    <div class="col-md-3"><label>Confirmar Nueva Clave</label>
                    </div>
                    <div class="col-md-3">

                        <input type="password" class='form-control'
                               .{{($errors->has('new_confirm_password') ? ' is-invalid' : '')}} name="new_confirm_password"
                               id='new_confirm_password' placeholder='Nueva Clave'
                               value="{{ old('new_confirm_password') ? old('new_confirm_password') : "" }}" required>
                        {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}

                    </div>

                    <br>

                </div>
                <div class="form-group row">
                    @if($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="form-group has-error">
                                <div class="col-md-12">
                                    <span class="help-block">{{ $error }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="form-group row">

                </div>
                <div class="box-footer mt20 float-right">
                    <button type="submit" class="btn btn-primary pull-right ">{{ __('Guardar') }}</button>
                    <a class="btn btn-secondary pull-right" href="{{ route('home') }}">
                        <i class="fa fa-undo"></i> Volver
                    </a>
                </div>
            </div>


        </form>


    </div>


@endsection


@section("js")
    <script type="text/javascript">
        // Function to check Whether both passwords
        // is same or not.
        function checkPassword(form) {
            password1 = form.new_password.value;
            password2 = form.new_confirm_password.value;

            // If password not entered
            if (password1 == '')
            {
                // alert("Please enter Password");
                toastr.options =
                    {
                        "closeButton" : true,
                        "progressBar" : true
                    }
            toastr.error("Ingrese Nueva Contraseña");
            }
            // If confirm password not entered
            else if (password2 == '')
            {
                // alert("Please enter confirm password");
                toastr.options =
                    {
                        "closeButton" : true,
                        "progressBar" : true
                    }
            toastr.error("Ingrese la confirmación de la Contraseña");
            }
            // If Not same return False.
            else if (password1 != password2) {
                // alert("\n")

                {
                    // alert("Please enter Password");
                    toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                    toastr.error("Las contraseñas no coinciden: Intente Nuevamente...");
                }

                return false;
            }


        }
    </script>
@endsection
