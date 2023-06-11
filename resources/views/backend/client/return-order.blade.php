@extends('backend.layouts.master')
@section('title', 'Client Return Order List')

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
                                Return Order list
                            </div>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order Type</th>
                                        <th>Invoice No.</th>
                                        <th>Total</th>
                                        <th>Balance</th>
                                        <th>Product Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $item)
                                        <tr>
                                            <td>
                                                {{ $item->order_type }}
                                            </td>
                                            <td>
                                                {{ $item->invoice_no }}
                                            </td>
                                            <td>{{ $item->total }}</td>
                                            <td>{{ $item->balance }}</td>
                                            <td>
                                                {{ $item->order_details_count }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $orders->links() }}
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
