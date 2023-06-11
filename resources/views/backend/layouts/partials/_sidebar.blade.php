<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset($company->logo) }}" alt="admin" class="brand-image  elevation-3" style="opacity: .8">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset(auth()->guard('admin')->user()->image) }}" class="img-circle elevation-2"
                    alt="AI">
            </div>
            <div class="info">
                <a href="{{ route('admin.dashboard') }}" class="d-block">{{ auth()->guard('admin')->user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
       with font-awesome or any other icon font library -->
                <li class="nav-item menu-open">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                {{-- admin --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p class="text">
                            Admin
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-header">
                        <li class="nav-item">
                            <a href="{{ route('admin.auth.adminList') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Admin List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.auth.createAdmin') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Create New Admin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.auth.customerList') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Customer List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- business_type --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p class="text">
                            Business Type
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-header">
                        <li class="nav-item">
                            <a href="{{ route('admin.business_type.index') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Business Type List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.business_type.create') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Create Business Type</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- country --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p class="text">
                            Country
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-header">
                        <li class="nav-item">
                            <a href="{{ route('admin.country.index') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Country List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.country.create') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Create Country</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- State --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p class="text">
                            State
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-header">
                        <li class="nav-item">
                            <a href="{{ route('admin.state.index') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>State List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.state.create') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Create State</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Police Station --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p class="text">
                            Police Station
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-header">
                        <li class="nav-item">
                            <a href="{{ route('admin.p_s.index') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Police Station List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.p_s.create') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Create Police Station</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- package feature --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p class="text">
                            Package Feature
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-header">
                        <li class="nav-item">
                            <a href="{{ route('admin.package_feature.index') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Package Feature List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.package_feature.create') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Create Package Feature</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- package --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p class="text">
                            Package
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-header">
                        <li class="nav-item">
                            <a href="{{ route('admin.package.index') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Package List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.package.create') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Create Package</p>
                            </a>
                        </li>
                    </ul>
                </li>



                

                {{-- company info --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p class="text">
                            Company Info
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-header">
                        <li class="nav-item">
                            <a href="{{ route('admin.showCompanyInfo') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Company Information</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.page.index') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Pages</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Clienst info --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p class="text">
                            Clienst Info
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-header">
                        <li class="nav-item">
                            <a href="{{ route('admin.client.presentActive') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Active Clients</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.presentInactive') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Inactive Clients</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.presentExpired') }}" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p>Expired Clients</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.getCompanyNotification') }}" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p>Notification</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.slider.index') }}" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p>Slider</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.feature.index') }}" class="nav-link">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p>Feature</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
