@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')


<div class="col-md-12">
    <div class="callout callout-danger">
        <h1>Bienvenido !</h1>
        <h5>
            <strong> {{$persona->nombre .' '. $persona->apellido}}</strong>
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

                    <p>Desayunos consumidos (en el mes)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-mug-hot"></i>
                </div>
                <a href="{{ route('personas.show',$persona->id) }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>

{{--            <div class="info-box">--}}
{{--                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-mug-hot"></i></span>--}}

{{--                <div class="info-box-content">--}}
{{--                    <span class="info-box-text">Desayunos consumidos (en el mes)</span>--}}
{{--                    <span class="info-box-number">{{$desayunos}}</span>--}}
{{--                </div>--}}
{{--                <!-- /.info-box-content -->--}}
{{--            </div>--}}
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-4">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{$viandas}}</h3>

                <p>Viandas consumidas (en el mes)</p>
            </div>
            <div class="icon">
                <i class="fas fa-utensils"></i>
            </div>
            <a href="{{ route('personas.show',$persona->id) }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>

        </div>
{{--        <div class="col-12 col-sm-6 col-md-4">--}}
{{--            <div class="info-box mb-3">--}}
{{--                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-utensils"></i></span>--}}

{{--                <div class="info-box-content">--}}
{{--                    <span class="info-box-text">Viandas consumidas (en el mes)</span>--}}
{{--                    <span class="info-box-number">{{$viandas}}</span>--}}
{{--                </div>--}}
{{--                <!-- /.info-box-content -->--}}
{{--            </div>--}}
{{--            <!-- /.info-box -->--}}
{{--        </div>--}}
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-4">

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
{{--            --}}
{{--            <div class="info-box mb-3">--}}
{{--                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-store"></i></span>--}}


{{--                <div class="info-box-content">--}}
{{--                    <span class="info-box-text">Comercios</span>--}}
{{--                    <span class="info-box-number">{{$comercios}}</span>--}}
{{--                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>--}}
{{--                </div>--}}
{{--                <!-- /.info-box-content -->--}}
{{--            </div>--}}
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
{{--        <div class="col-12 col-sm-6 col-md-3">--}}
{{--            <div class="info-box mb-3">--}}
{{--                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>--}}

{{--                <div class="info-box-content">--}}
{{--                    <span class="info-box-text">Empleados</span>--}}
{{--                    <span class="info-box-number">{{$empleados}}</span>--}}
{{--                </div>--}}
{{--                <!-- /.info-box-content -->--}}
{{--            </div>--}}
{{--            <!-- /.info-box -->--}}
{{--        </div>--}}
    </div>
    <div class="row">
        <section class="col-lg-7 connectedSortable ui-sortable">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="ion ion-clipboard mr-1"></i>
                    Stock Actual
                </h3>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <ul class="todo-list ui-sortable" data-widget="todo-list">
                    @foreach($persona->stockActual as $stock)

                    <li>
                        <div class="info-box mb-3 bg-warning">
                            <span class="info-box-icon"> <i class='{{$stock->articulo->icon}}'></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{\Carbon\Carbon::parse( $stock->fechadesde )->format('d/m/Y')}} - {{\Carbon\Carbon::parse( $stock->fechahasta )->format('d/m/Y')}}</span>
                                <span class="info-box-number">{{$stock->saldo}}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>


                    </li>
                    @endforeach
                </ul>
            </div>
            <!-- /.card-body -->

        </div>
        </section>
        <section class="col-lg-5">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fas fa-qrcode "></i>
                    Mi QR
                </h3>

            </div>
            <!-- /.card-header -->
            <div class="card-body text-center">
                {!!QrCode::size(300)
                     ->backgroundColor(254,254,218)
                     ->generate("{{$persona->qr}}");!!}
            </div>

        </div>
        </section>
        <!-- /.col -->
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
