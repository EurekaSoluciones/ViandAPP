@extends('adminlte::page')

@section('template_title')
    Crear Notificación
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h5>
                        <i class="fas fa-comment"></i> Nueva Notificación
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
                        <form method="POST" action="{{ route('notificaciones.store') }}"  role="form" enctype="multipart/form-data" onsubmit = "return(validate());">
                            @csrf

                            @include('notificaciones.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
