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
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-mug-hot"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Desayunos (en el mes)</span>
                    <span class="info-box-number">{{$desayunos}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-utensils"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Viandas (en el mes)</span>
                    <span class="info-box-number">{{$viandas}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-clipboard-list"></i></span>

                <div class="info-box-content">
                   <span class="info-box-text">Consumos Pendientes de Liquidar</span>
                   <span class="info-box-number">{{count($consumosPendientes)}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

    </div>
    <div class="row">
        <section class="col-lg-7 connectedSortable ui-sortable">
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
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
