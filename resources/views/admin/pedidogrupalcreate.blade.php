@extends('adminlte::page')

@section('template_title')
    Pedido Grupal
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h5>
                        <i class="fas fa-shopping-basket"></i> Nuevo Pedido Grupal
                    </h5>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">

                    <div class="card-body">
                        <form class="form-horizontal" method="POST" action="{{ route('generarpedidogrupal') }}"  role="form" enctype="multipart/form-data">
                            @csrf
                            @method('POST')

                            <div class="form-group row">
                                <label for="fecha"  class="col-sm-2 col-form-label">Fecha</label>
                                <div class="input-group date col-md-2" id="fechadatetime" data-target-input="nearest">
                                    <input type="text" name ='fecha', class = 'form-control datetimepicker-input'
                                           placeholder = 'Fecha' id='fecha' required  data-target= '#fechadatetime' value="{{\Carbon\Carbon::now()->format('d/m/Y')}}">
                                    <div class="input-group-append" data-target="#fechadatetime" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="comercio"  class="col-md-2 col-form-label">Comercio</label>
                                <div class="col-md-10">
                                <select class='js-example-basic-single w-75' name="comercio" required>
                                    <option disabled value="" selected hidden>Seleccione Comercio...</option>
                                    @foreach($comercios as  $key => $value)
                                        <option  value="{{ $key }}" {{ old('comercio')== $key? 'selected' : '' }}> {{ $value }} </option>
                                    @endforeach
                                </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="personas"  class="col-md-2 col-form-label">Personas</label>

                                <div class="select2-blue col-md-10" >
                                    <select id="js-example-basic-multiple" class="js-example-basic-multiple" name="personas[]" required
                                            multiple="multiple" data-placeholder="Seleccione personas" >
                                        @foreach($personas as  $key => $value)



                                                <option  value="{{ $key }}" > {{ $value }} </option>


                                        @endforeach
                                    </select>

                                </div>


                            </div>

                            <div class="form-group row">
                                <label for="articulo"  class="col-md-2 col-form-label">Articulo</label>
                                <div class="col-md-4">
                                    <select class='js-example-basic-single w-75' name="articulo" required>

                                        <option disabled value="" selected hidden>Seleccione Artículo...</option>
                                        @foreach($articulos as  $key => $value)
                                            <option  value="{{ $key }}" {{ old('articulo')== $key? 'selected' : '' }}> {{ $value }}  </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group row">

                                <label for="cantidad"  class="col-sm-2 col-form-label">Cantidad</label>
                                <div class="col-md-2">
                                <input type="text" class='form-control text-right'.{{($errors->has('cantidad') ? ' is-invalid' : '')}} name="cantidad" value="{{ old('cantidad') }}"
                                       id='cantidad' placeholder='Cantidad' onKeyPress="return SoloNumeros(event)"
                                       required>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="observaciones"  class="col-sm-2 col-form-label">Observaciones</label>
                                <div class="col-md-12">
                                        <textarea class='form-control'.{{($errors->has('observaciones') ? ' is-invalid' : '')}} name="observaciones"
                                                  id='observaciones' placeholder='Ingrese Observaciones' rows="3" value="{{ old('observaciones') }}" ></textarea>
                                </div>
                            </div>


                            @if ( $errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <br>
                            <br>
                            <div class="box-footer mt20 float-right">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                                <a  class="btn btn-secondary" href="{{ route('home') }}">
                                    <i class="fa fa-undo"></i> Volver
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section("js")


    <script type="text/javascript">

        $(function () {

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
            $('.select2').select2();


            $('#fechadatetime').datetimepicker({locale: 'es', format: 'DD/MM/YYYY'});

        })

        $(document).ready(function() {
            $('.js-example-basic-single').select2();
            $('.js-example-basic-multiple').select2();

            @if ( $errors->any())
            @if(!empty(Session::get('seleccionadas')))
             $('.js-example-basic-multiple').select2('val',[{!! Session::get('seleccionadas') !!}]);
             $('.js-example-basic-multiple').select2().val([{!! Session::get('seleccionadas') !!}]).trigger('change');

            @endif
            @endif




                        // var personasseleccionadas=Session::get('seleccionadas');
                        // alert (personasseleccionadas);
            {{--    var selectedValues = seleccionadas.split(',');--}}
            {{--    // selectedValues[0] = "302";--}}
            {{--    // selectedValues[1] = "359";--}}
            {{--    // selectedValues[2] = "358";--}}


{{--            @endif--}}
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
