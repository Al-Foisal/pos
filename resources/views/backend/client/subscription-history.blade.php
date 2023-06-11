@extends('backend.layouts.master')
@section('title', 'Client Subscription History')

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
                                Subscription history
                            </div>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Package Name</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                        <th>User Limit</th>
                                        <th>Validity From</th>
                                        <th>Validity To</th>
                                        <th>Reminder</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subscription as $item)
                                        <tr>
                                            <td>{{ $item->en_package_name }} @if($loop->first) (Present) @endif</td>
                                            <td>{{ $item->price }}</td>
                                            <td>{{ $item->duration }} days</td>
                                            <td>{{ $item->user_limit }}</td>
                                            <td>{{ $item->validity_from->format('d-m-Y') }}</td>
                                            <td>{{ $item->validity_to->format('d-m-Y') }}</td>
                                            <td>
                                                {{ $item->subscriptionReminder->duration.' days '.$item->subscriptionReminder->reminder.' times' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $subscription->links() }}
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
