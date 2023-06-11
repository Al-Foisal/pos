@extends('backend.layouts.master')
@section('title', 'Create State')
@section('backend')
    <!-- Content Header (State header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create State</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Create State</li>
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
                        <form action="{{ route('admin.state.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="">Select Country</label>
                                    <select name="country_id" id="" class="form-control" required>
                                        <option value="">Country</option>
                                        @foreach ($countries as $item)
                                            <option value="{{ $item->id }}">{{ $item->en_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @foreach (config('app.languages') as $locale => $locale_name)
                                    <div class="form-group">
                                        <label for="{{ $locale }}_name">{{ $locale_name }} Name*</label>
                                        <input type="text" class="form-control" id="{{ $locale }}_name"
                                            placeholder="{{ $locale_name }} Name" name="{{ $locale }}_name">
                                    </div>
                                @endforeach
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
