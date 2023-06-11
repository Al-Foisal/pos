@extends('backend.layouts.master')
@section('title', 'Client Service List')

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
                                Service list
                            </div>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($service as $item)
                                        <tr>
                                            <td>
                                                {{ $item->name }}
                                            </td>
                                            <td>
                                                {{ 'Buying price: ' . $item->buying_price }},
                                                {{ 'Retail price: ' . $item->retail_price }},
                                                {{ 'Wholesale price: ' . $item->wholesale_price }}
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>
                                                <img src="{{ asset($item->image) }}" style="height:50px;width:50px;">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $service->links() }}
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
