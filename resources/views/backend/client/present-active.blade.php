@extends('backend.layouts.master')
@section('title', 'Client List')

@section('backend')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Present active clients</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Client List</li>
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
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Image</th>
                                        <th>Client Name</th>
                                        <th>Client Business Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Refer Codeo</th>
                                        <th>Validity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($show_data as $item)
                                        <tr>
                                            <td>
                                                <!-- Example single danger button -->
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-danger dropdown-toggle"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('admin.client.details', $item->id) }}">Details</a>
                                                        @if ($item->status == 0)
                                                            <form action="{{ route('admin.client.active', $item->id) }}"
                                                                method="post">
                                                                @csrf
                                                                <button class="dropdown-item" type="submit">Active
                                                                    </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('admin.client.inactive',$item->id) }}" method="post">
                                                                @csrf
                                                                <button class="dropdown-item" type="submit">Inactive
                                                                    </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="{{ asset($item->image) }}" height="50" width="50"
                                                    alt=""></td>
                                            <td>{{ $item->name ?? 'Not Set Yet' }}</td>
                                            <td>{{ $item->business_name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->phone ?? 'Not Set Yet' }}</td>
                                            <td>{{ $item->reference_code }} </td>
                                            <td>{{ $item->validity }}</td>
                                            <td>{{ $item->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $show_data->links() }}
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
