@extends('backend.layouts.master')
@section('title', 'Edit Slider')
@section('backend')
    <!-- Content Header (Package header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Slider</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Edit Slider</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-primary">
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('admin.slider.update', $data->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="card-body">
                                
                                <div class="row">
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="image">Slider Image*</label>
                                            <input type="file" class="form-control" id="image" name="image">
                                        </div>
                                        <img src="{{ asset($data->image) }}" style="height:50px;width:50px;">
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('jsScript')
    {{-- for discount price --}}
    <script type="text/javascript">
        $(function() {
            $("#price, #discount").on("keydown keyup", sum);

            function sum() {
                var price = Number($("#price").val());
                var discount = Number($("#discount").val());
                var discount_price = (price * discount) / 100;
                if (discount > 0) {
                    $("#discount_price").val(price - discount_price);
                } else {
                    $("#discount_price").val(null);
                }
            }
        });
    </script>
@endsection
