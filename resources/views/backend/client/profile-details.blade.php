@extends('backend.layouts.master')
@section('title', 'Profile details')

@section('backend')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Navbar -->
            @include('backend.layouts.partials._client-navbar')
            <!-- /.navbar -->


                
            <div class="alert alert-light">
                Profile details
            </div>
            <div class="row">
                @foreach ($users as $user)
                    <div class="col-md-3">

                        <!-- Profile Image -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle" src="{{ asset('images/dp.jpg') }}"
                                        alt="User profile picture">
                                </div>

                                <h3 class="profile-username text-center">{{ $user->name ?? 'Not Set Yet' }} @if($user->id == $user->user_id) - Owner @endif</h3>

                                <p class="text-muted text-center">{{ $user->business_name }}</p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Role</strong>

                                <p class="text-muted">
                                    {{ $user->role->name }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Email</strong>

                                <p class="text-muted">
                                    {{ $user->email }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Business Type</strong>

                                <p class="text-muted">
                                    {{ $user->businessType->en_name ?? 'Not Set Yet' }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Country</strong>

                                <p class="text-muted">
                                    {{ $user->country->en_name ?? 'Not Set Yet' }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> State</strong>

                                <p class="text-muted">
                                    {{ $user->state->en_name ?? 'Not Set Yet' }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Police Station</strong>

                                <p class="text-muted">
                                    {{ $user->policeStation->en_name ?? 'Not Set Yet' }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Reference ID</strong>

                                <p class="text-muted">
                                    {{ $user->reference_id }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Status</strong>

                                <p class="text-muted">
                                    {{ $user->status == 1 ? 'Active' : 'Inactive' }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Validity</strong>

                                <p class="text-muted">
                                    {{ $user->validity->diffForHumans() }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Profile Created</strong>

                                <p class="text-muted">
                                    {{ $user->created_at->diffForHumans() }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-pencil-alt mr-1"></i> Last Updated</strong>

                                <p class="text-muted">
                                    {{ $user->updated_at->diffForHumans() }}
                                </p>

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                @endforeach
                <!-- /.col -->
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
