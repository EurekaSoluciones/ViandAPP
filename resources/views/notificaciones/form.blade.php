<div class="box box-info padding-1">
    <div class="box-body  container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="titulo"  class="col-sm-2 col-form-label">Título</label>
                        <input type="text" class='form-control'.{{($errors->has('titulo') ? ' is-invalid' : '')}} name="titulo"
                               id='titulo' placeholder='Título' value="{{ old('titulo') ? old('titulo') : $notificacion->titulo }}" required maxlength="100">
                    {!! $errors->first('titulo', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="descripcion"  class="col-sm-2 col-form-label">Descripción</label>
                    <textarea rows="4" class='form-control'.{{($errors->has('descripcion') ? ' is-invalid' : '')}} name="descripcion"
                              id='descripcion' placeholder='Texto de la notificación' value="{{ old('descripcion') ? old('descripcion') : $notificacion->descripcion }}" required maxlength="100"></textarea>
                    {!! $errors->first('descripcion', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rdpersonas" checked="" id="rdpersonastodas" onchange="habilitarSeleccionPersonas();" value="todos" >
                        <label class="form-check-label">Para TODAS las personas activas</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rdpersonas" id="rdpersonasseleccion" onchange="habilitarSeleccionPersonas();" value="seleccion" >
                        <label class="form-check-label">Seleccionar Personas</label>
                    </div>

                </div>
            </div>
        </div>
        <div class="row" id="divSeleccionPersonas" style="visibility: hidden">
            <label for="personas"  class="col-md-2 col-form-label">Personas</label>

            <div class="select2-blue col-md-12" >
                <select id="js-example-basic-multiple" class="js-example-basic-multiple w-100" name="personas[]"
                        multiple="multiple" data-placeholder="Seleccione personas" >
                    @foreach($personas as  $key => $value)
                        <option  value="{{ $key }}" > {{ $value }} </option>
                    @endforeach
                </select>

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

        <div class="row no-print">
            <div class="col-12">
                <div id="diverror" class="alert alert-danger" type="text" name="diverror" style="display:none">

                </div>
            </div>
        </div>

    </div>

</div>
<br>
<br>
<div class="box-footer mt20 float-right">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a  class="btn btn-secondary" href="{{ route('notificaciones.index') }}">
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

            $('.js-example-basic-multiple').select2();

        });


        function habilitarSeleccionPersonas()
        {
            if (document.getElementById("rdpersonasseleccion").checked)
                document.getElementById("divSeleccionPersonas").style.visibility='visible';
            else
                document.getElementById("divSeleccionPersonas").style.visibility='hidden';

        }

        function validate() {


            var input = document.getElementById('diverror');
            input.style.display='none';
            input.innerHTML="";
            valido=true;

            /*Si esta seleccionado que no es para todas las personas, al menos tiene que haber una seleccionada*/

            if (document.getElementById("rdpersonasseleccion").checked)
            {

                if (document.getElementById("js-example-basic-multiple").value =="")
                {
                    valido=false;
                    input.innerHTML+="Debe ingresar al menos una persona\r\n";
                    input.style.display='block';
                }
            }


            return( valido);
        }


    </script>


@endsection
