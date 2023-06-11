@extends('backend.layouts.master')
@section('title', 'Create Package')
@section('cssStyle')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endsection
@section('backend')
    <!-- Content Header (Package header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Package</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Create Package</li>
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
                        <form action="{{ route('admin.package.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    @foreach (config('app.languages') as $locale => $locale_name)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="{{ $locale }}_name">{{ $locale_name }} Name*</label>
                                                <input type="text" class="form-control" id="{{ $locale }}_name"
                                                    placeholder="{{ $locale_name }} Name" name="{{ $locale }}_name">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price">Price*</label>
                                            <input type="text" class="form-control" id="price"
                                                placeholder="Enter price" name="price">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="discount">Discount(optional)</label>
                                            <input type="text" class="form-control" id="discount"
                                                placeholder="Enter discount" name="discount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="discount_price">Discount Price(optional)</label>
                                            <input type="text" class="form-control" id="discount_price"
                                                placeholder="Enter discount price" name="discount_price">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="duration">Duration*</label>
                                            <input type="number" class="form-control" id="duration"
                                                placeholder="Enter duration" name="duration">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user_limit">User Limit*</label>
                                            <input type="number" class="form-control" id="user_limit"
                                                placeholder="Enter user limit" name="user_limit">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="image">Package Image*</label>
                                            <input type="file" class="form-control" id="image" name="image">
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-success mt-5">
                                    <div class="card-header">
                                        Package Features
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            @foreach ($package_feature as $item)
                                                <li class="list-group-item">
                                                    <div class="form-group clearfix">
                                                        <div class="icheck-success">
                                                            <input type="checkbox" id="{{ $item->en_name }}" name="feature[]"
                                                                value="{{ $item->id }}">
                                                            <label for="{{ $item->en_name }}">
                                                                {{ $item->en_name }} ({{ $item->bn_name }})
                                                            </label>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
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
