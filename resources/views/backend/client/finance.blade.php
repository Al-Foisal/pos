@extends('backend.layouts.master')
@section('title', 'Client Finance List')

@section('backend')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @include('backend.layouts.partials._client-navbar')
            <div class="row">
                <div class="col-12">
                    <div class="">
                        <section class="content">
                            <div class="container-fluid">
                                <h4>Income and Expenses</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $total_income??0 }}</h3>

                                                <p>Total Income</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-money-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $total_expense??0 }}</h3>

                                                <p>Total Expenses</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-money-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $daily_income??0 }}</h3>

                                                <p>Daily Income</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-money-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $daily_expense??0 }}</h3>

                                                <p>Daily Expenses</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-money-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="content">
                            <div class="container-fluid">
                                <h4>Order Details</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                @php
                                                    $p_count = 0;
                                                    foreach ($total_order as $total_product) {
                                                        $p_count += $total_product->orderDetails->count();
                                                    }
                                                @endphp
                                                <h3>{{ $total_order->count() . '/' . $p_count }}</h3>

                                                <p>Total Order / Product</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-cart-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                @php
                                                    $pr_count = 0;
                                                    foreach ($total_order_return as $total_return) {
                                                        $pr_count += $total_return->orderDetails->count();
                                                    }
                                                @endphp
                                                <h3>{{ $total_order_return->count() . '/' . $pr_count }}</h3>

                                                <p>Total Order Return / Product</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-cart-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                @php
                                                    $t_count = 0;
                                                    foreach ($today_order as $tp) {
                                                        $t_count += $tp->count();
                                                    }
                                                @endphp
                                                <h3>{{ $today_order->count() . '/' . $t_count }}</h3>

                                                <p>Daily Order / Product</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-cart-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                @php
                                                    $t_count = 0;
                                                    foreach ($today_order_return as $tp) {
                                                        $t_count += $tp->count();
                                                    }
                                                @endphp
                                                <h3>{{ $today_order_return->count() . '/' . $t_count }}</h3>

                                                <p>Daily Order Return / Product</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-cart-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="content">
                            <div class="container-fluid">
                                <h4>Purchase Details</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                @php
                                                    $pp_count = 0;
                                                    foreach ($total_purchase as $tp) {
                                                        $pp_count += $tp->count();
                                                    }
                                                @endphp
                                                <h3>{{ $total_purchase->count() . '/' . $pp_count }}</h3>

                                                <p>Total Purchase / Product</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                @php
                                                    $ppr_count = 0;
                                                    foreach ($total_purchase_return as $tp) {
                                                        $ppr_count += $tp->count();
                                                    }
                                                @endphp
                                                <h3>{{ $total_purchase_return->count() . '/' . $ppr_count }}</h3>

                                                <p>Total Purchase Return / Product</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                @php
                                                    $t_count = 0;
                                                    foreach ($today_purchase as $tp) {
                                                        $t_count += $tp->count();
                                                    }
                                                @endphp
                                                <h3>{{ $today_purchase->count() . '/' . $t_count }}</h3>

                                                <p>Daily Purchase / Product</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                @php
                                                    $t_count = 0;
                                                    foreach ($today_purchase_return as $tp) {
                                                        $t_count += $tp->count();
                                                    }
                                                @endphp
                                                <h3>{{ $today_purchase_return->count() . '/' . $t_count }}</h3>

                                                <p>Daily Purchase Return / Product</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="content">
                            <div class="container-fluid">
                                <h4>People and Products</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $total_user }} out of {{ $package->user_limit??1 }}</h3>

                                                <p>Total User</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $total_customer }}</h3>

                                                <p>Total Customer</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $total_supplier }}</h3>

                                                <p>Daily Supplier</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- small card -->
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $product }}</h3>

                                                <p>Products</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-warehouse"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <hr>
    <section class="content">
        <div class="container-fluid">
            <h4>Subscription History</h4>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Package Name</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                        <th>User Limit</th>
                                        <th>Validity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($package_history as $item)
                                        <tr>
                                            <td>{{ $item->en_package_name }}</td>
                                            <td>{{ $item->price }}</td>
                                            <td>{{ $item->duration.' days' }}</td>
                                            <td>{{ $item->user_limit }}</td>
                                            <td>{{ $item->validity_to->format("Y-m-d") }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $package_history->links() }}
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
