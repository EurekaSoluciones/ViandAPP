@extends('adminlte::page')

@section('template_title')
    Importación Excel de Consumos
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-11">
                    <h1>
                        <i class="fas fa-file-excel"></i>  Importación Excel de Consumos
                    </h1>
                </div><!-- /.col -->
                <div class="box-tools text-right">


                </div>


            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <div class="card">
        <div class="card-body">

            <div class="row">


                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
            </div>

            <form action="{{ route('asignacionexcel') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group" >

                    <div class="custom-file col-md-8">
                        <i class="fas fa-paperclip"></i><input type="file" name="archivo" class="custom-file-input" id="customFile">
                        <label class="custom-file-label" for="customFile"> Seleccione Archivo a importar</label>
                    </div>
                </div>
                <button class="btn btn-primary">Previsualizar Importación</button>
                <a class="btn btn-info" href="{{ route('home') }}">Cancelar</a>
            </form>

            <div class="row">
            </div>
                <div class="row no-print mt-3">
                    <div class="col-12">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>
                </div>
        </div>
    </div>
@endsection

@section("js")

<script>
    $(function () {
        bsCustomFileInput.init();
    });
</script>
@endsection
