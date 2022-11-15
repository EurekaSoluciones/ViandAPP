@extends('adminlte::page')

@section('template_title')
    Personas
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-11">
                    <h1>
                        <i class="fas fa-shopping-bag"></i>  Comercios
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

            <form action="{{ route('asignacionexcel') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group col-md-6" >

                    <div class="custom-file">
                        <i class="fas fa-paperclip"></i><input type="file" name="file" class="custom-file-input" id="customFile">
                        <label class="custom-file-label" for="customFile"></label>
                    </div>
                </div>
                <button class="btn btn-primary">Import data</button>
            </form>
    </div>
@endsection

@section("js")

<script>
    $(function () {
        bsCustomFileInput.init();
    });
</script>
@endsection
