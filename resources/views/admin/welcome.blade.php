@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')


<div class="col-md-12">
    <div class="callout callout-danger">
        <h1>Bienvenido !</h1>
        <h5>
            <strong> {{auth()->user()->name}}</strong>
            <br>
        </h5>
    </div>
</div>
@stop

@section('content')
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{$desayunos}}</h3>

                    <p>Desayunos consumidos (en el mes)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-mug-hot"></i>
                </div>
                <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>


            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{$viandas}}</h3>

                    <p>Viandas consumidas (en el mes)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{$comercios}}</h3>

                    <p>Comercios</p>
                </div>
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
                <a href="{{ route('comercios.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{$empleados}}</h3>

                        <p>Empleados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <a href="{{ route('personas.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
                </div>

                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
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
                                <span class="text">Comercio: <strong> {{$cierreLote->comercio->nombrefantasia}}</strong></span>
                                <span class="text">Fecha: <strong> {{\Carbon\Carbon::parse( $cierreLote->fecha )->format('d/m/Y')}}</strong></span>
                                <!-- Emphasis label -->
                                <small class="{{$cierreLote->timeclass}}"><i class="far fa-clock"></i> {{\Carbon\Carbon::create( $cierreLote->fecha)->diffForHumans()}}</small>
                                <!-- General tools such as edit or delete-->
                                <span class="handle ui-sortable-handle">
                                    <span class="text">    # Operaciones: {{count($cierreLote->movimientos)}}</span>
                                </span>
                                <span class="handle ui-sortable-handle">
                                    <span class="text">    # Consumos: {{$cierreLote->movimientos->sum('cantidad')}}</span>
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
                        <i class="fas fa-people-carry mr-1"></i>
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
                                    <i class="fas fa-people-carry"></i>
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
                <!-- /.card-body -->

            </div>
        </section>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
