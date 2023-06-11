@extends('backend.layouts.master')
@section('title', 'Client Balance Transfer List')

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
                                Balance Transfer details
                            </div>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Transfer Person</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Sender Account</th>
                                        <th>Receiver Account</th>
                                        <th>Note</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($balance_transfer as $item)
                                        <tr>
                                            <td>{{ $item->transfer_person }}</td>
                                            <td>{{ $item->transfer_date }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ $item->sender->name }}</td>
                                            <td>{{ $item->reciver->name }}</td>
                                            <td>{{ $item->note }}</td>
                                            <td>
                                                <img src="{{ asset($item->image) }}" style="height:50px;width:50px;">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $balance_transfer->links() }}
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
