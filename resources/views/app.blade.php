<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- Stylesheets -->
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/responsive.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/css/custom/theme-2.css') }}">
    <link rel="icon" href="img/File_000.png-2.png" type="image/x-icon">
    <style>
        .logo-box {
            background: url(../../images/logo/logo1.pn) no-repeat 9% 100%;
        }

        .owl-carousel .owl-stage-outer {
            position: relative;
            /* overflow: hidden; */
            -webkit-transform: translate3d(0px, 0px, 0px);
        }

        .main-slider .slider-wrapper .image img {
            min-height: 404px;
            width: 100%;
            max-width: none;
        }

        @media only screen and (max-width: 991px) {
            .vv {
                display: none;
            }
        }
    </style>
</head>
<!-- page wrapper -->

<body class="boxed_wrapper">


    <div id="app"></div>

    @vite('resources/js/app.js')
</body>

</html>
