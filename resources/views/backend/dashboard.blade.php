@extends('backend.layouts.master')
@section('title', 'dashboard')

@section('backend')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>


    <section class="content">
        <div class="container-fluid">
            <h4>Clients</h4>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-user-friends"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $total_client }}</span>
                            <span class="info-box-number">Total Clients</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-user-friends"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $active_client }}</span>
                            <span class="info-box-number">Active Clients</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-danger">
                            <i class="fas fa-user-friends"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $inactive_client }}</span>
                            <span class="info-box-number">Inactive Clients</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-danger">
                            <i class="fas fa-user-friends"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $expired_client }}</span>
                            <span class="info-box-number">Expired Clients</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <h4>Peoples and Products</h4>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $total_customer }}</span>
                            <span class="info-box-number">Total Customer</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $total_supplier }}</span>
                            <span class="info-box-number">Total Supplier</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $total_products }}</span>
                            <span class="info-box-number">Total Products</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-danger">
                            <i class="fas fa-cart-arrow-down"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $inactive_products }}</span>
                            <span class="info-box-number">Inactive Products</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <h4>Order and Purchase Details</h4>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $todays_order }}</span>
                            <span class="info-box-number">Todays Order</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-danger">
                            <i class="fas fa-cart-arrow-down"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $todays_return_order }}</span>
                            <span class="info-box-number">Todays Return Order</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $todays_purchase }}</span>
                            <span class="info-box-number">Todays Purchase</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-danger">
                            <i class="fas fa-cart-arrow-down"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $todays_return_purchase }}</span>
                            <span class="info-box-number">Todays Return Purchase</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr>

    <section class="content">
        <div class="container-fluid">
            <h4>Country wise users</h4>
            <div class="row">
                @foreach ($countries as $country)
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box shadow-lg">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-globe-africa"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ $country->users->count() }}</span>
                                <span class="info-box-number">{{ $country->en_name }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <hr>

    <section class="content">
        <div class="container-fluid">
            <h4>Package wise subscription</h4>
            <div class="row">
                @foreach ($packages as $package)
                    <div class="col-sm-6 col-12">
                        <div class="info-box shadow-lg">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-globe-africa"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ $package->subscriptions->count() }}</span>
                                <span class="info-box-number">{{ $package->en_name }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
