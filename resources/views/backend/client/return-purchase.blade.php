@extends('backend.layouts.master')
@section('title', 'Client Return Purchase List')

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
                                Return Purchase list
                            </div>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Purchase Type</th>
                                        <th>Invoice No.</th>
                                        <th>Total</th>
                                        <th>Balance</th>
                                        <th>Product Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase as $item)
                                        <tr>
                                            <td>
                                                {{ $item->purchase_type }}
                                            </td>
                                            <td>
                                                {{ $item->invoice_no }}
                                            </td>
                                            <td>{{ $item->total }}</td>
                                            <td>{{ $item->balance }}</td>
                                            <td>
                                                {{ $item->return_purchase_details_count }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $purchase->links() }}
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
