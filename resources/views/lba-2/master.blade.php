<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Template 2 LetsBuyAsia</title>
<meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="HandheldFriendly" content="True">
<meta property="og:title" content="{{ $data->campaign }}">
<meta property="og:description" content="{{ $data->campaign }}">
<meta property="og:image" content="{{ env('BASE_URL_DASHBOARD').'/assets/brand/images/'.$data->brand_logo }}">
<meta property="og:url" content="{{ url()->full() }}">
<meta property="og:type" content="website">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<!-- CSS  -->
<link rel="stylesheet" href="{{ asset('assets/lba-2/lib/font-awesome/web-fonts-with-css/css/fontawesome-all.css') }}">
<link rel="stylesheet" href="{{ asset('assets/lba-2/css/materialize.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/lba-2/css/normalize.css') }}">
<link rel="stylesheet" href="{{ asset('assets/lba-2/css/style.css') }}">
<!-- materialize icon -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- Owl carousel -->
<link rel="stylesheet" href="{{ asset('assets/lba-2/lib/owlcarousel/assets/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/lba-2/lib/owlcarousel/assets/owl.theme.default.min.css') }}">
<!-- Slick CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/lba-2/lib/slick/slick/slick.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/lba-2/lib/slick/slick/slick-theme.css') }}">
<!-- Magnific Popup core CSS file -->
<link rel="stylesheet" href="{{ asset('assets/lba-2/lib/Magnific-Popup-master/dist/magnific-popup.css') }}">
</head>

@if($data->template_background != null || $data->template_background != '')
    <body id="homepage" style="background-image: url('{{ env('BASE_URL_DASHBOARD').'/assets/pageview/background/'.$data->template_background }}'); background-size: cover; background-attachment: fixed;">
@else
    <body id="homepage">
@endif

<!-- BEGIN PRELOADING -->
<div class="preloading">
    <div class="wrap-preload">
        <div class="cssload-loader"></div>
    </div>
</div>
<!-- END PRELOADING -->

<!-- HEADER COMPONENT -->
<header id="header" style="background-color: {{$data->template_primary_color}}"> <!-- primary color -->
	<div class="nav-wrapper container">
	  <div class="header-logo">
            <a href="#" class="nav-logo">
                <img style="height:30px" src="{{ env('BASE_URL_DASHBOARD').'/assets/brand/images/'.$data->brand_logo }}" /> <!-- logo brand here -->
            </a>
	  </div>
	</div>
</header>

    <!-- content -->
    @yield('content')

    <!-- default footer (not remove) -->
    <div class="container">
    <div class="row copyright">
        Powered by<br>
        <span>LetsBuyAsia</span>
    </div>
    </div>
</footer>
<!-- END FOOTER COMPONENT -->


<!-- Script -->
<script src="{{ asset('assets/lba-2/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/lba-2/js/materialize.min.js') }}"></script>
<!-- Owl carousel -->
<script src="{{ asset('assets/lba-2/lib/owlcarousel/owl.carousel.min.js') }}"></script>
<!-- Magnific Popup core JS file -->
<script src="{{ asset('assets/lba-2/lib/Magnific-Popup-master/dist/jquery.magnific-popup.js') }}"></script>
<!-- Slick JS -->
<script src="{{ asset('assets/lba-2/lib/slick/slick/slick.min.js') }}"></script>
<!-- Custom script -->
<script src="{{ asset('assets/lba-2/js/custom.js') }}"></script>
</body>

</html>