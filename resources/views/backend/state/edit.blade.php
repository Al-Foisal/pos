@extends('backend.layouts.master')
@section('title', 'Edit State')
@section('backend')
    <!-- Content Header (State header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit State</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Edit State</li>
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
                        <form action="{{ route('admin.state.update', $state) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="">Select Country</label>
                                    <select name="country_id" id="" class="form-control" required>
                                        <option value="">Country</option>
                                        @foreach ($countries as $item)
                                            <option value="{{ $item->id }}" @if($item->id == $state->country_id) selected @endif>{{ $item->en_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach (config('app.languages') as $locale => $locale_name)
                                    <div class="form-group">
                                        <label for="{{ $locale }}_name">{{ $locale_name }} Name*</label>
                                        <input type="text" class="form-control" id="{{ $locale }}_name"
                                            value="{{ $state->{$locale . '_name'} }}" name="{{ $locale }}_name">
                                    </div>
                                @endforeach
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
