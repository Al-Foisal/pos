@extends('backend.layouts.master')
@section('title', 'Page List')

@section('backend')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Page</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Page</li>
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
                            <a href="{{ route('admin.page.create') }}" class="btn btn-outline-primary">Add Page</a>
                            <br>
                            <br>
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>English Name</th>
                                        <th>Bangla Name</th>
                                        <th>English Details</th>
                                        <th>Bangla Details</th>
                                        <th>Created_at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pages as $page)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.page.edit', $page) }}"
                                                    class="btn btn-info btn-xs"> <i class="fas fa-edit"></i> </a>
                                                @if ($page->status === 1)
                                                    <form action="{{ route('admin.page.inactive', $page) }}"
                                                        method="post">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return(confirm('Are you sure want to INACTIVE this item?'))"
                                                            class="btn btn-danger btn-xs"> <i
                                                                class="far fa-thumbs-down"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.page.active', $page) }}"
                                                        method="post">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return(confirm('Are you sure want to Active this item?'))"
                                                            class="btn btn-info btn-xs"> <i class="far fa-thumbs-up"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.page.delete', $page) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit"
                                                        onclick="return(confirm('Are you sure want to delete this item?'))"
                                                        class="btn btn-danger btn-xs"> <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>{{ $page->en_name }}</td>
                                            <td>{!! $page->en_details !!}</td>
                                            <td>{{ $page->bn_name }}</td>
                                            <td>{!! $page->bn_details !!}</td>
                                            <td>{{ $page->created_at }}</td>
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
