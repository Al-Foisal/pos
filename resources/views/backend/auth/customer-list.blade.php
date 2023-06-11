@extends('backend.layouts.master')
@section('title', 'Customer List')

@section('backend')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customer</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Customer List</li>
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
                                        <th>Name</th>
                                        <th>Business Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Image</th>
                                        <th>Reference and Package</th>
                                        <th>Custom Subscription</th>
                                        <th>Created_at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $key => $customer)
                                        <tr>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->business_name }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->phone ?? '' }}</td>
                                            <td>{{ $customer->address ?? '' }}</td>
                                            <td><img src="{{ asset($customer->image ?? '') }}" height="50" width="50"
                                                    alt=""></td>
                                            <td>
                                                Refer code: {{ $customer->reference_code ?? 'Not set' }} <br>
                                                @php
                                                    $latest_package = DB::table('subscription_histories')
                                                        ->where('user_id', $customer->id)
                                                        ->orderBy('id', 'desc')
                                                        ->first();
                                                @endphp
                                                Present package: {{ $latest_package->en_package_name ?? '' }} <br>
                                                Validity: {{ $latest_package->validity_to ?? '' }}
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.auth.updateCustomSubscription') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $customer->id }}">
                                                    <input type="number" name="date" placeholder="Enter days">
                                                    <button class="btn btn-primary btn-sm">Submit</button>
                                                </form>
                                            </td>
                                            <td>{{ $customer->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $customers->links() }}
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
