<div class="box box-info padding-1">
    <div class="box-body  container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email"  class="col-sm-3 col-form-label">Login</label>
                        <input type="text" class='form-control'.{{($errors->has('login') ? ' is-invalid' : '')}} name="login"
                               id='login' placeholder='Login' value="{{ old('login') ? old('login') : $usuario->email }}"
                               required {{$ACTION!="Alta"?"readonly":""}}>
                    {!! $errors->first('login', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombre"  class="col-sm-3 col-form-label">Nombre y Apellido</label>
                    <input type="text" class='form-control'.{{($errors->has('nombre') ? ' is-invalid' : '')}} name="nombre"
                           id='nombre' placeholder='Nombre y Apellido' value="{{ old('nombre') ? old('nombre') : $usuario->name }}" required>
                    {!! $errors->first('nombre', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="perfil"  class="col-sm-3 col-form-label">Perfil</label>
                    <div class="col-md-4">
                        <select class='js-example-basic-single  col-md-12' name="perfil" required >

                            <option disabled value="" hidden>Seleccione Perfil...</option>
                            @foreach($perfiles as  $key => $value)
                                @if($usuario->perfil_id == $key)
                                    <option  value="{{ $key }}" selected>{{ $value }}    </option>
                                @else
                                    <option  value="{{ $key }}"> {{ $value }} </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>

</div>
<br>
<br>
<div class="box-footer mt20 float-right">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a  class="btn btn-secondary" href="{{ route('usuarios.index') }}">
        <i class="fa fa-undo"></i> Volver
    </a>
</div>


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
        });


        function SoloNumeros(evt){
            if(window.event){//asignamos el valor de la tecla a keynum
                keynum = evt.keyCode; //IE
            }
            else{
                keynum = evt.which; //FF
            }
            //comprobamos si se encuentra en el rango numérico y que teclas no recibirá.
            if((keynum > 47 && keynum < 58) || keynum == 8 || keynum == 13 || keynum == 6 ){
                return true;
            }
            else{
                return false;
            }
        }



    </script>


@endsection
