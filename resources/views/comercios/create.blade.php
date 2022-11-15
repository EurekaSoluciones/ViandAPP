@extends('adminlte::page')

@section('template_title')
    Create Comercio
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h5>
                        <i class="fas fa-store"></i> Nuevo Comercio
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
                        <form method="POST" action="{{ route('comercios.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('comercios.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
