<?php
@extends('adminlte::page')

@section('template_title')
    Cierre de Lore
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-11">
                    <h1>
                        <i class="fas fa-store"></i>  Cierre de Lote
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
        <form class="form-horizontal" method="POST" action="{{ route('consumosPendientes') }}"  role="form" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="card-body">
                <div class="form-group row">
                    <label for="fechahasta" class="col-sm-2 col-form-label">Fecha Hasta</label>
                    <div class="col-sm-4">

                        <div class="input-group date" id="fechahastadatetime" data-target-input="nearest">

                            <input type="text" name ='fechahasta', class = 'form-control datetimepicker-input'
                                   placeholder = 'Fecha Hasta' id='fechadesde' required  data-target= '#fechahastadatetime'>
                            <div class="input-group-append" data-target="#fechahastadatetime" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>

                        </div>

                        {!! $errors->first('fechahasta', '<div class="invalid-feedback">:message</div>') !!}
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
                        <th class="sorting_asc">Fecha</th>
                        <th>Persona</th>
                        <th>Artículo</th>
                        <th>Cantidad</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($movimientos as $movimiento)
                        <tr>
                            <td>{{ $movimiento->id }}</td>
                            <td>{{ $movimiento->fecha}}</td>
                            <td>{{ $movimiento->persona->fullname }}</td>
                            <td><i class="{{$movimiento->articulo->icon}}"></i> {{$movimiento->articulo->descripcion}}</td>
                            <td>{{$movimiento->cantidad}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row ">
                <button type="submit" class="btn btn-info float-right" action="{{ route('cerrarLote') }}">Cerrar Lote</button>

            </div>
        </div>
        <div class="card-footer">
            {!! $movimientos->links() !!}
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


            $('#tabla').DataTable({
                'paging'      : false,
                'lengthChange': false,
                'searching'   : false,
                'ordering'    : true,
                'info'        : false,
                'autoWidth'   : false,
                'language': {
                    'lengthMenu': 'Mostrar _MENU_ registros por página',
                    'zeroRecords': 'No existen registros',
                    'info': 'Mostrando Página _PAGE_ de _PAGES_',
                    'infoEmpty': 'No hay registros disponibles',
                    'infoFiltered': '(filtrando desde _MAX_ registros totales)',
                },
            })


        })

        $(document).ready(function() {
            $('.js-example-basic-single').select2();
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
