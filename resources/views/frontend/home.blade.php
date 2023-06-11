@extends('frontend.master')
@section('title','G - Point of sale')
@section('content')
    <!-- Start Banner Wrapper Area -->
    <div class="banner-ten owl-carousel owl-theme">
        @foreach ($slider as $item)
            <img src="{{ asset($item->image) }}" alt="">
        @endforeach
    </div>
    <!-- End Banner Wrapper Area -->
    <div class="overview-area mt-5">
        @foreach ($feature as $data)
            @if ($loop->even)
                <div class="container pt-100">
                    <div class="row mt-5">
                        <div class="col-xl-6 col-lg-12 col-md-12 p-10">
                            <div class="overview-content">
                                <h2>{{ $data->name }}</h2>
                                <p>{!! $data->details !!}</p>

                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-md-12 p-0">
                            <img src="{{ asset($data->image) }}" alt="overview">
                            {{-- <div class="overview-image bg1">
                            </div> --}}
                        </div>
                    </div>
                </div>
            @else
                <div class="container pt-100">
                    <div class="row mt-5">

                        <div class="col-xl-6 col-lg-12 col-md-12 p-0">
                            <img src="{{ asset($data->image) }}" alt="overview">
                            {{-- <div class="overview-image bg1">
                            </div> --}}
                        </div>
                        <div class="col-xl-6 col-lg-12 col-md-12" style="padding-left: 5rem;">
                            <div class="overview-content">
                                <h2>{{ $data->name }}</h2>
                                <p>{!! $data->details !!}</p>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <!-- Start App Download Area -->
    <div class="app-download-area pb-100 pt-100">
        <div class="container">
            <div class="app-download-inner bg-gray">
                <div class="row align-items-center">
                    <div class="col-lg-12 col-md-12">
                        <div class="app-download-content">
                            <span class="sub-title">DOWNLOAD APP</span>
                            <h2>Let's Get Your Free Copy From and Play Store</h2>

                            <div class="btn-box">
                                <a href="{{ $company->app_link }}" class="playstore-btn" target="_blank">
                                    <img src="{{ asset('assets/img/play-store.png') }}" alt="image">
                                    Get It On
                                    <span>Google Play</span>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End App Download Area -->
@endsection
