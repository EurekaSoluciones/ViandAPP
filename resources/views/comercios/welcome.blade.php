@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')


    <div class="col-md-12">
        <div class="callout callout-danger">
            <h1>Bienvenido !</h1>
            <h5>
                <strong> {{$comercio->razonsocial .' - '. $comercio->nombrefantasia}}</strong>
                <br>
            </h5>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{$desayunos}}</h3>

                    <p>Desayunos entregados (en el mes)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-mug-hot"></i>
                </div>
                <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>

        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{$viandas}}</h3>

                    <p>Viandas entregadas (en el mes)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{count($consumosPendientes)}}</h3>

                    <p>Consumos Pendientes de Liquidar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <a href="{{ route('consumosPendientes') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
{{--        <div class="col-12 col-sm-6 col-md-4">--}}
{{--            <div class="info-box mb-3">--}}
{{--                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-clipboard-list"></i></span>--}}

{{--                <div class="info-box-content">--}}
{{--                   <span class="info-box-text">Consumos Pendientes de Liquidar</span>--}}
{{--                   <span class="info-box-number">{{count($consumosPendientes)}}</span>--}}
{{--                </div>--}}
{{--                <!-- /.info-box-content -->--}}
{{--            </div>--}}
{{--            <!-- /.info-box -->--}}
{{--        </div>--}}

    </div>
    <div class="row">
        <section class="col-lg-6 connectedSortable ui-sortable">
            <div class="card">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-check mr-1"></i>
                        Ultimos Cierres de Lote
                    </h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <ul class="todo-list ui-sortable" data-widget="todo-list">
                        @foreach($ultimosLotes as $cierreLote)
                            <li>
                                <!-- drag handle -->
                                <span class="handle ui-sortable-handle">
                                  <i class="fas fa-ellipsis-v"></i>
                                  <i class="fas fa-ellipsis-v"></i>
                                </span>
                                <!-- checkbox -->
                                <div class="icheck-primary d-inline ml-2">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <!-- todo text -->
                                <span class="text">Fecha: <strong> {{\Carbon\Carbon::parse( $cierreLote->fecha )->format('d/m/Y')}}</strong></span>
                                <!-- Emphasis label -->
                                <small class="badge badge-danger"><i class="far fa-clock"></i>{{\Carbon\Carbon::create( $cierreLote->fecha)->diffForHumans()}}</small>
                                <!-- General tools such as edit or delete-->
                                <span class="handle ui-sortable-handle">
                                    <span class="text">    # Consumos: {{count($cierreLote->movimientos)}}</span>
                                </span>
                                <div class="tools">
                                    <a href="{{ route('detalleLote',$cierreLote->id) }}"> <i class="fas fa-info-circle text-danger"></i></a>
                                </div>
                            </li>

                        @endforeach
                    </ul>
                </div>
                <!-- /.card-body -->

            </div>
        </section>
        <section class="col-lg-6 connectedSortable ui-sortable">
            <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fas fa-shopping-basket mr-1"></i>
                    Ultimos Pedidos Grupales
                </h3>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <ul class="todo-list ui-sortable" data-widget="todo-list">
                    @foreach($pedidosGrupales as $pedido)
                        <li class="{{$pedido->fechacumplido!=null?"done":""}}">
                            <!-- drag handle -->

                        {{--                                 <div class="icheck-primary d-inline ml-2">--}}
                        {{--                                  <input type="checkbox" value="" name="todo1" id="todoCheck1" readonly="true">--}}
                        {{--                                  <label for="todoCheck1"></label>--}}
                        {{--                                </div>--}}


                        <!-- checkbox -->
                            <div class="icheck-primary d-inline ml-2">
                                <i class="{{$pedido->estadoclass}}"></i>
                            </div>
                            <!-- todo text -->
                            <span class="text">Fecha: <strong> {{\Carbon\Carbon::parse( $pedido->fecha )->format('d/m/Y')}}</strong></span>
                            <span class="text">Comercio: <strong> {{$pedido->comercio->nombrefantasia}}</strong></span>
                            <!-- Emphasis label -->
                            <small class="badge badge-danger"><i class="far fa-clock"></i> {{\Carbon\Carbon::create( $pedido->fecha)->diffForHumans()}}</small>
                            <!-- General tools such as edit or delete-->
                            <span class="handle ui-sortable-handle">
                                    <span class="text"><i class='{{$pedido->items[0]->articulo->icon}}'></i> {{$pedido->cantidaddesayunos>0? "Desayunos":"Viandas"}}: {{$pedido->cantidaddesayunos + $pedido->cantidadviandas}}</span>
                                </span>
                            <div class="tools">
                                <a href="{{ route('detallePedido',$pedido->id) }}"> <i class="fas fa-boxes text-danger"></i></a>
                            </div>
                        </li>

                    @endforeach
                </ul>
            </div>
            </div>
        </section>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section("js")


    <script type="text/javascript">



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
@stop
