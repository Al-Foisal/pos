<!doctype html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Link of CSS files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/odometer.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/meanmenu.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <title>@yield('title') - {{ $company->name }}</title>

    <link rel="icon" type="image/png" href="{{ asset($company->favicon) }}">
</head>

<body>

    <!-- Start Navbar Area -->
    <div class="navbar-area">
        <div class="pakap-responsive-nav">
            <div class="container">
                <div class="pakap-responsive-menu">
                    <div class="logo">
                        <a href="{{ url('/') }}"><img src="{{ asset($company->logo) }}" alt="logo"></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="pakap-nav" style="background: black;">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ asset($company->logo) }}" alt="logo">
                    </a>
                    <div class="collapse navbar-collapse mean-menu">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a href="javascript:;" class="nav-link">Email:
                                    {{ $company->email }}</a></li>
                            <li class="nav-item"><a href="javascript:;" class="nav-link">Partner:
                                    {{ $company->phone_one }}</a></li>
                            <li class="nav-item"><a href="javascript:;" class="nav-link">Customer care:
                                    {{ $company->phone_two }}</a></li>
                            <li class="nav-item"><a href="javascript:;" class="nav-link">Whatsapp:
                                    {{ $company->phone_three }}</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <!-- End Navbar Area -->

    @yield('content')

    <!-- Start Footer Area -->
    <div class="footer-area">
        <div class="container">
            <div class="footer-content">
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset($company->logo) }}" alt="logo">
                </a>
                <ul class="social-links">
                    <li><a href="{{ $company->facebook }}" target="_blank"><i class="ri-facebook-fill"></i></a></li>
                    <li><a href="{{ $company->twitter }}" target="_blank"><i class="ri-twitter-fill"></i></a></li>
                    <li><a href="{{ $company->linkedin }}" target="_blank"><i class="ri-linkedin-fill"></i></a></li>
                    <li><a href="{{ $company->youtube }}" target="_blank"><i class="ri-youtube-fill"></i></a></li>
                    <li><a href="{{ $company->instagram }}" target="_blank"><i class="ri-instagram-fill"></i></a></li>
                    <li><a href="{{ $company->pinterest }}" target="_blank"><i class="ri-pinterest-fill"></i></a></li>
                </ul>
                <ul class="navbar-nav">
                    @foreach ($pages as $page)
                        <li class="nav-item"><a href="{{ route('page_details', $page->slug) }}"
                                class="nav-link">{{ $page->en_name }}</a></li>
                    @endforeach

                </ul>
                <p class="copyright">Copyright @
                    <script>
                        document.write(new Date().getFullYear())
                    </script> <strong>{{ $company->name }}</strong>. Developed by <a
                        href="http://wiztecbd.com/" target="_blank">Wizard Software & Technology Bangladesh ltd.</a>
                </p>
            </div>
        </div>
    </div>
    <!-- End Footer Area -->

    <div class="go-top"><i class="ri-arrow-up-s-line"></i></div>

    <!-- Link of JS files -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/js/meanmenu.min.js') }}"></script>
    <script src="{{ asset('assets/js/appear.min.js') }}"></script>
    <script src="{{ asset('assets/js/odometer.min.js') }}"></script>
    <script src="{{ asset('assets/js/form-validator.min.js') }}"></script>
    <script src="{{ asset('assets/js/contact-form-script.js') }}"></script>
    <script src="{{ asset('assets/js/ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('assets/js/aos.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>
