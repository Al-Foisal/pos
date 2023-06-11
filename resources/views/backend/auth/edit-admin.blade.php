@extends('backend.layouts.master')
@section('title', 'Update Admin')

@section('backend')
    <div class="">
        <div class="register-logo">
            <a href="{{ route('admin.dashboard') }}" class="h1">
                Pos Inventory
            </a>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">Edit supportive member</p>

                <form action="{{ route('admin.auth.updateAdmin', $admin) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ $admin->name }}" name="name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ $admin->phone }}" name="phone">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="email" class="form-control" value="{{ $admin->email }}" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="input-group mb-3">
                                <input type="file" class="form-control" name="image">
                            </div>
                            @if ($admin->image)
                                <img src="{{ asset($admin->image) }}" height="100" width="100" alt="User logo">
                            @endif
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <textarea type="text" rows="2" class="form-control" name="address">{{ $admin->address }}</textarea>
                    </div>
                    
                    <hr>
                    <div class="row">
                        <div class="col-8">
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Update</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <br>
    <br>
@endsection
