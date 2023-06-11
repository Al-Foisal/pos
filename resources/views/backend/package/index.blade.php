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
                            <a href="{{ route('admin.package.create') }}" class="btn btn-outline-primary">Add Package</a>
                            <br>
                            <br>
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Package Name</th>
                                        <th>Package Feature</th>
                                        <th>Package Amount</th>
                                        <th>Validity</th>
                                        <th>Image</th>
                                        <th>Statue</th>
                                        <th>Updated_at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packages as $item)
                                        <tr>
                                            <td class="d-flex justify-content-around">
                                                <a href="{{ route('admin.package.edit', $item) }}"
                                                    class="btn btn-info btn-xs mr-2"> <i class="fas fa-edit"></i></a>
                                                @if ($item->status === 1)
                                                    <form action="{{ route('admin.package.inactive', $item) }}"
                                                        method="post">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return(confirm('Are you sure want to INACTIVE this item?'))"
                                                            class="btn btn-danger btn-xs mr-2"> <i
                                                                class="far fa-thumbs-down"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.package.active', $item) }}"
                                                        method="post">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return(confirm('Are you sure want to Active this item?'))"
                                                            class="btn btn-info btn-xs mr-2"> <i
                                                                class="far fa-thumbs-up"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <li>{{ $item->en_name }}</li>
                                                <li>{{ $item->bn_name }}</li>
                                            </td>
                                            <td>
                                                @foreach ($item->packageFeatures as $feature)
                                                    <li>{{ $feature->en_name }}</li>
                                                @endforeach
                                            </td>
                                            <td>
                                                <li>Price: {{ $item->price }}</li>

                                                <li>Discount: {{ $item->discount ?? 0 }}</li>

                                                <li>Price after discount: {{ $item->discount_price ?? 0 }}</li>
                                            </td>
                                            <td>
                                                <li>Duration: {{ $item->duration }} Days</li>
                                                <li>User Limit: {{ $item->user_limit }}</li>
                                            </td>
                                            <td>
                                                <img src="{{ asset($item->image) }}" style="height:50px;width:50px;">
                                            </td>
                                            <td>{{ $item->status == 1 ? 'Active' : 'Inactive' }}</td>
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
