@extends('adminlte::page')

@section('template_title')
    Historial de Cierres de Lore
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-11">
                    <h1>
                        <i class="fas fa-clipboard-check"></i>  Historial de Cierres de Lote
                    </h1>
                </div><!-- /.col -->
                <div class="box-tools text-right">


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
                    <label for="fechadesde" class="col-sm-2 col-form-label">Fecha Desde</label>
                    <div class="col-sm-3">

                        <div class="input-group date" id="fechaDesdedatetime" data-target-input="nearest">

                            <input type="text" value="{{\Carbon\Carbon::parse( $fechaDesde)->format('d/m/Y')}}" name ='fechaDesde', class = 'form-control datetimepicker-input'
                                   placeholder = 'Fecha Desde' id='fechaDesde' required  data-target= '#fechaDesdedatetime'>
                            <div class="input-group-append" data-target="#fechaDesdedatetime" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>

                        </div>

                        {!! $errors->first('fechaDesde', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                    <label for="comercio" class="col-sm-2 col-form-label">Comercios</label>
                    <div class="col-sm-4">
                        <select class='js-example-basic-single  col-md-12' name="comercio" required>

                            <option disabled value="0" hidden {{$comercio==null?"selected":""}}>Seleccione Comercio...</option>
                            @foreach($comercios as  $key => $value)

                                <option  value="{{ $key }}" {{$comercio==$key?"selected":""}}> {{ $value }}  </option>
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
                                <th>#</th>
                                <th class="text-center sorting_asc">Fecha</th>
                                <th>Comercio</th>
                                <th>Observaciones</th>
                                <th class="text-center">Cant. Operaciones</th>
                                <th class="text-center">Cant. Consumos</th>
                                <th class="text-center">Desayunos</th>
                                <th class="text-center">Viandas</th>
                                <th class="text-center">Visado</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($cierres as $lote)
                                <tr>
                                    <td>{{ $lote->id }}</td>
                                    <td>{{\Carbon\Carbon::parse( $lote->fecha)->format('d/m/Y')}}</td>
                                    <td>{{ $lote->comercio->nombrefantasia }}</td>
                                    <td>{{ $lote->observaciones }}</td>
                                    <td class="text-center">{{count($lote->movimientos)}}</td>
                                    <td class="text-center">{{$lote->movimientos->sum('cantidad')}}</td>
                                    <td class="text-center">{{$lote->desayunos->sum('cantidad')}}</td>
                                    <td class="text-center">{{$lote->viandas->sum('cantidad')}}</td>
                                    <td class="text-center"><input type="checkbox" disabled="" {{ ($lote->visado) ? "checked" : "" }}></td>
                                    <td> <a href="{{ route('detalleLote',$lote->id) }}"> <i class="fas fa-eye text-primary" title="Ver Detalle"></i></a>

                                    </td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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

            //Date and time picker
            //$('#fechadesdedatetime').datetimepicker({ locale: 'es' , format: 'DD/MM/YYYY HH:mm', icons: { time: 'far fa-clock' } });

            $('#fechaDesdedatetime').datetimepicker({ locale: 'es' , format: 'DD/MM/YYYY'});



            $('#tabla').DataTable({
                'paging'      : false,
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
                    'search':'Buscar en la lista'
                },

            });
        })



        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            // if ($('#fechaHasta').val()=="")
            //     $('#fechaHasta').val(new Date().toLocaleDateString());

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
