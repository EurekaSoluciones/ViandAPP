@extends('adminlte::page')

@section('template_title')
    Consumir
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h5>
                        <i class="fas fa-plus-circle"></i> Aumento Stock
                    </h5>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <section class="content container-fluid">
            @includeif('partials.errors')
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-default">

                        <div class="card-body">
                            <form class="form-horizontal" method="POST" action="{{ route('generaraumento') }}"  role="form" enctype="multipart/form-data">
                                @csrf


                                <div class="form-group row">
                                    <label for="articulo"  class="col-md-4 col-form-label">Articulo</label>
                                    <div class="col-md-8">
                                        <select class='js-example-basic-single' name="articulo">
                                            <option value="0">Seleccione Artículo...  </option>
                                            @foreach($articulos as  $key => $value)
                                                <option  value="{{ $key }}"> {{ $value }}  </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label for="persona"  class="col-sm-4 col-form-label">Persona</label>
                                    <div class="col-md-8">
                                        <select class='js-example-basic-single' name="persona" onchange="obtenerStockdePersona();">
                                            <option value="0">Seleccione Persona...  </option>
                                            @foreach($personas as  $key => $value)
                                                <option  value="{{ $key }}"> {{ $value }}  </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <label for="fecha"  class="col-sm-4 col-form-label">Fecha</label>
                                    <div class="input-group date col-md-4" id="fechadatetime" data-target-input="nearest">

                                        <input type="text" name ='fecha', class = 'form-control datetimepicker-input'
                                               placeholder = 'Fecha' id='fecha' required  data-target= '#fechadatetime' onchange="obtenerStockdePersona();" onblur="obtenerStockdePersona();">
                                        <div class="input-group-append" data-target="#fechadatetime" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="cantidad"  class="col-sm-4 col-form-label">Cantidad</label>
                                    <input type="text" class='form-control col-md-2 text-right'.{{($errors->has('cantidad') ? ' is-invalid' : '')}} name="cantidad"
                                           id='cantidad' placeholder='Cantidad' onKeyPress="return SoloNumeros(event)"
                                           required>
                                </div>
                                <div class="form-group row">
                                    <label for="cc"  class="col-sm-4 col-form-label">CC</label>
                                    <input type="text" class='form-control col-md-2'.{{($errors->has('cc') ? ' is-invalid' : '')}} name="cc"
                                           id='cc' placeholder='CC'
                                           required>
                                </div>

                                <div class="form-group row">
                                    <label for="observaciones"  class="col-sm-2 col-form-label">Observaciones</label>
                                    <div class="col-md-12">
                                        <textarea class='form-control'.{{($errors->has('observaciones') ? ' is-invalid' : '')}} name="observaciones"
                                                  id='observaciones' placeholder='Ingrese Observaciones' rows="3"></textarea>
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
                                    <a  class="btn btn-secondary" href="{{ route('personas.index') }}">
                                        <i class="fa fa-undo"></i> Volver
                                    </a>
                                </div>


                            </form>
                        </div>

                    </div>

                </div>
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-header ui-sortable-handle" style="cursor: move;">
                            <h3 class="card-title">
                                <i class="fas fa-thumbs-up"></i>
                                 Stock Disponible
                            </h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <ul id="ulListado" class="todo-list ui-sortable" data-widget="todo-list">
                            </ul>
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
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            $('#fechadatetime').datetimepicker({locale: 'es', format: 'DD/MM/YYYY'});

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


        function obtenerStockdePersona(){


            persona = $('select[name=persona]').val() // Here, I'm getting selected value of dropdown
            fecha = $('#fecha').val(); // Here, I'm getting selected value of dropdown
            if (persona!="" && fecha!="")
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('obtenerStockdePersona')}}",
                    type: "GET",
                    data:jQuery.param(
                        {'persona' : persona, 'fecha': fecha
                        } )// in header request I'm getting value [productName: plastic product] *
                    ,
                    success:function(data){
                        console.log(data);
                        //document.querySelector("divStock").innerHTML = JSON.stringify(data);
                        var lista=document.getElementById("ulListado");


                        function agregarElementos(){
                            var lista=document.getElementById("ulListado");
                            lista.innerHTML="";
                            data["stock"].forEach(function(data,index){
                                var linew= document.createElement("li");
                                linew.innerHTML="<span class='handle ui-sortable-handle'><i class='fas fa-ellipsis-v'></i><i class='fas fa-ellipsis-v'></i></span><span class='text'>" +
                                    (data.articulo_id=="1"?"<i class='fas fa-mug-hot'></i>":"<i class='fas fa-utensils'></i>") + " - </span><small class='badge badge-danger'>" + data.saldo +"</small>" +
                                    " - Desde: "+ moment(data.fechadesde).format('DD/MM/YYYY') + " Hasta: " +moment(data.fechahasta).format('DD/MM/YYYY');
                                // var contenido = document.createTextNode(
                                lista.appendChild(linew);
                                //linew.appendChild(contenido);

                            })
                        }
                        agregarElementos();


                    },
                    error:function(e){
                        console.log(e,'error');
                    }
                });
            }
            else
            {

            }
        }
    </script>


@endsection
