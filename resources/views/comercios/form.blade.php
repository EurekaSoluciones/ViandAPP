<div class="box box-info padding-1">
    <div class="box-body  container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="razonsocial"  class="col-sm-2 col-form-label">Razón Social</label>
                        <input type="text" class='form-control'.{{($errors->has('razonsocial') ? ' is-invalid' : '')}} name="razonsocial"
                               id='razonsocial' placeholder='Razón Social' value="{{ old('razonsocial') ? old('razonsocial') : $comercio->razonsocial }}" required>
                    {!! $errors->first('razonsocial', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombrefantasia"  class="col-sm-3 col-form-label">Nombre Fantasía</label>
                    <input type="text" class='form-control'.{{($errors->has('nombrefantasia') ? ' is-invalid' : '')}} name="nombrefantasia"
                           id='nombrefantasia' placeholder='Nombre Fantasía' value="{{ old('nombrefantasia') ? old('nombrefantasia') : $comercio->nombrefantasia }}" required>
                    {!! $errors->first('nombrefantasia', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label for="cuit"  class="col-sm-2 col-form-label">CUIT</label>
                    <input type="text" class='form-control'.{{($errors->has('cuit') ? ' is-invalid' : '')}} name="cuit"
                           id='cuit' placeholder='CUIT' value="{{old('cuit') ? old('cuit') :$comercio->cuit}}" onKeyPress="return SoloNumeros(event)"
                           onblur="CuitValido();" required>
                    <label class="label label-danger text-sm"  name="cuitInvalido" id="cuitInvalido" hidden="true">Cuit Invalido</label>
                    {!! $errors->first('cuit', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label for="domicilio"  class="col-sm-2 col-form-label">Domicilio</label>
                    <input type="text" class='form-control'.{{($errors->has('domicilio') ? ' is-invalid' : '')}} name="domicilio"
                           id='domicilio' placeholder='Domicilio' value="{{old('dni') ? old('dni') :$comercio->domicilio}}" >
                    {!! $errors->first('domicilio', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">
                <div class="form-group">
                    <label for="observaciones"  class="col-sm-2 col-form-label">Observaciones</label>
                    <input type="text" class='form-control'.{{($errors->has('observaciones') ? ' is-invalid' : '')}} name="observaciones"
                           id='observaciones' placeholder='Observaciones' value="{{old('observaciones') ? old('observaciones') :$comercio->observaciones}}" >
                    {!! $errors->first('observaciones', '<div class="invalid-feedback">:message</div>') !!}
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
    <a  class="btn btn-secondary" href="{{ route('comercios.index') }}">
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

        function CuitValido()
        {
            // cuit=document.getElementById('cuit').value;

            var vec = new Array(10);
            var cuit = document.getElementById('cuit').value ;
            esCuit=false;
            cuit_rearmado="";
            errors = '';
            for (i=0; i < cuit.length; i++)
            {
                caracter=cuit.charAt( i);
                if ( caracter.charCodeAt(0) >= 48 && caracter.charCodeAt(0) <= 57 )
                {
                    cuit_rearmado +=caracter;
                }
            }
            cuit=cuit_rearmado;
            if ( cuit.length != 11) {  // si no estan todos los digitos
                esCuit=false;
                errors = 'Cuit < 11 ';
                //alert( "CUIT Menor a 11 Caracteres" );
            } else {
                x=i=dv=0;
                // Multiplico los dígitos.
                vec[0] = cuit.charAt(  0) * 5;
                vec[1] = cuit.charAt(  1) * 4;
                vec[2] = cuit.charAt(  2) * 3;
                vec[3] = cuit.charAt(  3) * 2;
                vec[4] = cuit.charAt(  4) * 7;
                vec[5] = cuit.charAt(  5) * 6;
                vec[6] = cuit.charAt(  6) * 5;
                vec[7] = cuit.charAt(  7) * 4;
                vec[8] = cuit.charAt(  8) * 3;
                vec[9] = cuit.charAt(  9) * 2;

                // Suma cada uno de los resultado.
                for( i = 0;i<=9; i++)
                {
                    x += vec[i];
                }
                dv = (11 - (x % 11)) % 11;
                if ( dv == cuit.charAt( 10) )
                {
                    esCuit=true;
                }
            }
            if ( !esCuit )
            {
                //alert( "CUIT Invalido" );
                document.getElementById('cuitInvalido').hidden=false;
                document.getElementById('cuit').focus();
                //errors = 'Cuit Invalido ';
            }
            else
            {
                document.getElementById('cuitInvalido').hidden=true;

            }
            //document.MM_returnValue1 = (errors == '');


        }

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
