@extends('backend.layouts.master')
@section('title', 'Client Income Type List')

@section('backend')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @include('backend.layouts.partials._client-navbar')
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="alert alert-light">
                                Income Type list
                            </div>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($income_type as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $income_type->links() }}
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
