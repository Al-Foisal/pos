@extends('frontend.master')
@section('title', $page->en_name)
@section('content')
    <!-- Start Blog Details Area -->
    <div class="blog-details-area ptb-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="blog-details-desc">

                        <div class="article-content">

                            <div class="text-center pb-100 pt-50">
                                <h1>{{ $page->en_name }}</h1>
                            </div>
                            {!! $page->en_details !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Blog Details Area -->
@endsection
