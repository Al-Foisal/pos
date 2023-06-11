@extends('backend.layouts.master')
@section('title', 'Police Station')

@section('backend')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Police Station List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Police Station</li>
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
                            <a href="{{ route('admin.p_s.create') }}" class="btn btn-outline-primary">Add
                                Police Station</a>
                            <br>
                            <br>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Country</th>
                                        <th>State</th>
                                        <th>English Name</th>
                                        <th>Bangla Name</th>
                                        <th>Status</th>
                                        <th>Created_at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($police_station as $item)
                                        <tr>
                                            <td class="d-flex justify-content-between">
                                                <a href="{{ route('admin.p_s.edit', $item) }}" class="btn btn-info btn-xs">
                                                    <i class="fas fa-edit"></i> </a>
                                                @if ($item->status === 1)
                                                    <form action="{{ route('admin.p_s.inactive', $item) }}" method="post">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return(confirm('Are you sure want to INACTIVE this item?'))"
                                                            class="btn btn-danger btn-xs"> <i
                                                                class="far fa-thumbs-down"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.p_s.active', $item) }}" method="post">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return(confirm('Are you sure want to Active this item?'))"
                                                            class="btn btn-info btn-xs"> <i class="far fa-thumbs-up"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>{{ $item->country->en_name }}</td>
                                            <td>{{ $item->state->en_name }}</td>
                                            <td>{{ $item->en_name }}</td>
                                            <td>{{ $item->bn_name }}</td>
                                            <td>{{ $item->status === 1 ? 'Active' : 'Inactive' }}</td>
                                            <td>{{ $item->created_at }}</td>
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
