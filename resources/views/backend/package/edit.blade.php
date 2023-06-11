@extends('backend.layouts.master')
@section('title', 'Edit Package')
@section('backend')
    <!-- Content Header (Package header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Package</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Edit Package</li>
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
                        <form action="{{ route('admin.package.update', $package) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="card-body">
                                <div class="row">
                                    @foreach (config('app.languages') as $locale => $locale_name)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="{{ $locale }}_name">{{ $locale_name }} Name*</label>
                                                <input type="text" class="form-control" id="{{ $locale }}_name"
                                                    value="{{ $package->{$locale . '_name'} }}"
                                                    name="{{ $locale }}_name">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price">Price*</label>
                                            <input type="number" class="form-control" id="price"
                                                placeholder="Enter price" value="{{ $package->price }}" name="price">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="discount">Discount(optional)</label>
                                            <input type="number" class="form-control" id="discount"
                                                placeholder="Enter discount" name="discount"
                                                value="{{ $package->discount }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="discount_price">Discount Price(optional)</label>
                                            <input type="number" class="form-control" id="discount_price"
                                                placeholder="Enter discount price" name="discount_price"
                                                value="{{ $package->discount_price }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="duration">Duration*</label>
                                            <input type="number" class="form-control" id="duration"
                                                placeholder="Enter duration" name="duration"
                                                value="{{ $package->duration }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user_limit">User Limit*</label>
                                            <input type="number" class="form-control" id="user_limit"
                                                placeholder="Enter user limit" name="user_limit"
                                                value="{{ $package->user_limit }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="image">Package Image*</label>
                                            <input type="file" class="form-control" id="image" name="image">
                                        </div>
                                        <img src="{{ asset($package->image) }}" style="height:50px;width:50px;">
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
                                                            <input type="checkbox" id="{{ $item->en_name }}"
                                                                name="feature[]" value="{{ $item->id }}"
                                                                {{ in_array($item->id, old('feature', $package->packageFeatures->pluck('id')->toArray())) ? 'checked' : '' }}>
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
