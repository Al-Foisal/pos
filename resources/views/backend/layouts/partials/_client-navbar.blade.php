@php
    $account = App\Models\User::where('id', request()->id)
        ->with('country')
        ->first();
@endphp

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $account->business_name }} @if ($account->country_id)
                        ({{ $account->country->en_name }})
                    @endif
                </h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<nav class="main-header navbar navbar-expand-md navbar-light navbar-white"
    style="top: 130px;margin-left: 0;position:inherit;margin-bottom:3rem;">
    <div class="container-fluide">

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('admin.client.details', $account->id) }}" class="nav-link">User</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.client.subscriptionHistory', $account->id) }}"
                        class="nav-link">Subscription</a>
                </li>
                <li class="nav-item dropdown">
                    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" class="nav-link dropdown-toggle">People</a>
                    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                        <li class="nav-item">
                            <a href="{{ route('admin.client.customerList', $account->id) }}"
                                class="nav-link">Customer</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.supplierList', $account->id) }}"
                                class="nav-link">Supplier</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" class="nav-link dropdown-toggle">Item</a>
                    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                        <li class="nav-item">
                            <a href="{{ route('admin.client.productList', $account->id) }}" class="nav-link">Product</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.serviceList', $account->id) }}" class="nav-link">Service</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" class="nav-link dropdown-toggle">Sell and Purchase</a>
                    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                        <li class="nav-item">
                            <a href="{{ route('admin.client.placedOrder', $account->id) }}" class="nav-link">Sell</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.returnOrder', $account->id) }}" class="nav-link">Return
                                Sell</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.placedPurchase', $account->id) }}" class="nav-link">Purchase</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.returnPurchase', $account->id) }}" class="nav-link">Return
                                Purchase</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" class="nav-link dropdown-toggle">Others</a>
                    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                        <li class="nav-item">
                            <a href="{{ route('admin.client.productUnit', $account->id) }}" class="nav-link">Product
                                Unit</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.incomeType', $account->id) }}" class="nav-link">Income
                                Type</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('admin.client.incomePurpose', $account->id) }}" class="nav-link">Income
                                Purpose</a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.client.expenseType', $account->id) }}" class="nav-link">Expense
                                Type</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('admin.client.expensePurpose', $account->id) }}" class="nav-link">Expense
                                Purpose</a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.client.balanceTransfer', $account->id) }}"
                                class="nav-link">Balance Transfer</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.finance', $account->id) }}" class="nav-link">Finance</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
