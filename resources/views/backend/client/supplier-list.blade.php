@extends('backend.layouts.master')
@section('title', 'Client Supplier List')

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
                                Supplier details
                            </div>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Group</th>
                                        <th>Amount</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supplier as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->phone }}</td>
                                            <td>{{ $item->email }} days</td>
                                            <td>{{ $item->group }}</td>
                                            <td>
                                                @if ($item->previous_due > 0)
                                                    {{ 'Due: ' . $item->previous_due . ' from ' . $item->previous_due_date }}
                                                @elseif($item->previous_advance > 0)
                                                    {{ 'Due: ' . $item->previous_advance . ' from ' . $item->previous_advance_date }}
                                                @else
                                                    0.00
                                                @endif
                                            </td>
                                            <td>
                                                <img src="{{ asset($item->image) }}" style="height:50px;width:50px;">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $supplier->links() }}
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
