@extends('backend.layouts.master')
@section('title', 'Package List')

@section('backend')
    <!-- Content Header (Package header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Package</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Package</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <a href="{{ route('admin.feature.create') }}" class="btn btn-outline-primary">Add Feature</a>
                            <br>
                            <br>
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Details</th>
                                        <th>Updated_at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr>
                                            <td class="d-flex justify-content-around">
                                                <a href="{{ route('admin.feature.edit', $item) }}"
                                                    class="btn btn-info btn-xs mr-2"> <i class="fas fa-edit"></i></a>
                                                <form action="{{ route('admin.feature.destroy', $item) }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-danger btn-sm"><i
                                                            class="fas fa-trash"></i></button>

                                                </form>
                                            </td>

                                            <td>
                                                <img src="{{ asset($item->image) }}" style="height:50px;width:50px;">
                                            </td>
                                            <td>
                                                {{ $item->name }}
                                            </td>
                                            <td>{!! $item->details !!}</td>
                                            <td>{{ $item->updated_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('jsLink')
@endsection
@section('jsScript')
@endsection
