<!DOCTYPE html>
<html class="no-js" lang="zxx">

<!-- Mirrored from htmldemo.net/rick/rick/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 19 Nov 2023 04:06:30 GMT -->

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>LetsBuyAsia</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ asset('assets/img/logo.ico') }}" type="image/x-icon">
  <!-- CSS ============================================ -->
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="{{ asset('assets/lba-1/css/bootstrap.min.css') }}">
  <!-- FontAwesome CSS -->
  <link rel="stylesheet" href="{{ asset('assets/lba-1/css/font-awesome.min.css') }}">
  <!-- Slick CSS -->
  <link rel="stylesheet" href="{{ asset('assets/lba-1/css/plugins/slick.min.css') }}">
  <!-- Animate CSS -->
  <link rel="stylesheet" href="{{ asset('assets/lba-1/css/plugins/cssanimation.min.css') }}">
  <!-- IonRange slider CSS -->
  <link rel="stylesheet" href="{{ asset('assets/lba-1/css/plugins/ion.rangeSlider.min.css') }}">
  <!-- Vendor & Plugins CSS (Please remove the comment from below vendor.min.css & plugins.min.css for better website load performance and remove css files from above) -->
  <!--
			<link rel="stylesheet" href="assets/css/vendor.min.css">
			<link rel="stylesheet" href="assets/css/plugins/plugins.min.css">
  -->
  <!-- Main Style CSS -->
  <link rel="stylesheet" href="{{ asset('assets/lba-1/css/style.css') }}">

  <style>
    .mw-500 {max-width: 500px; margin: 0 auto;}
    .bg-auth {width: 500px; height: 100%; position: absolute; object-fit: cover; z-index: -1;}
  </style>
</head>

<!--<body class="bg-login mw-500" style="background-image: url('https://s40424.pcdn.co/in/wp-content/uploads/2022/02/digital-marketing-2.jpg.optimal.jpg')">-->

@if($data->template_background != null || $data->template_background != '')
  <body class="bg-login mw-500" style="background-image: url('{{ env('BASE_URL_DASHBOARD').'/assets/pageview/background/'.$data->template_background }}'); background-size: cover; background-attachment: fixed;">
@else
  <body class="bg-login mw-500">
@endif

  <!--====================  preloader area ====================-->
  <div class="preloader-activate preloader-active">
    <div class="preloader-area-wrap">
      <div class="spinner d-flex justify-content-center align-items-center h-100">
        <div class="img-loader"></div>
      </div>
    </div>
  </div>
  <!--====================  End of preloader area  ====================-->
  <div class="body-wrapper space-pt--70 space-pb--25" style="background-color: #1B743A" @if (session('success')) data-notif-success="{{session('success')}}" @else data-notif-success="" @endif 
    @if (session('failed')) data-notif-failed="{{session('failed')}}" @else data-notif-failed="" @endif>
    <!-- auth page header -->
    <div class="auth-page-header space-mb--50">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <h3 class="auth-page-header__title">Welcome to LetsBuyAsia</h3>
            <p class="auth-page-header__text">Number #1 online-to-offline platform</p>
          </div>
        </div>
      </div>
    </div>

    @yield('content')
  </div>
  <!-- JS ============================================ -->
  <!-- Modernizer JS -->
  <script src="{{ asset('assets/lba-1/js/modernizr-2.8.3.min.js') }}"></script>
  <!-- jQuery JS -->
  <script src="{{ asset('assets/lba-1/js/jquery.min.js') }}"></script>
  <!-- Bootstrap JS -->
  <script src="{{ asset('assets/lba-1/js/bootstrap.min.js') }}"></script>
  <!-- IonRanger JS -->
  <script src="{{ asset('assets/lba-1/js/plugins/ion.rangeSlider.min.js') }}"></script>
  <!-- SVG inject JS -->
  <script src="{{ asset('assets/lba-1/js/plugins/svg-inject.min.js') }}"></script>
  <!-- Slick slider JS -->
  <script src="{{ asset('assets/lba-1/js/plugins/slick.min.js') }}"></script>
  <!-- Plugins JS (Please remove the comment from below plugins.min.js for better website load performance and remove plugin js files from above) -->
  <!--
  <script src="assets/js/plugins/plugins.min.js"></script>
  -->
  <!-- Main JS -->
  <script src="{{ asset('assets/lba-1/js/main.js') }}"></script>
  <script src="{{ asset('assets/lba-1/js/colorUtils.js') }}"></script>
  <script src="{{ asset('assets/plugins/sweetalert.min.js') }}"></script>
  <script>
    $(document).ready(function() {
      const messageNotifSuccess = $('body .body-wrapper').data("notif-success");
      const messageNotifFailed = $('body .body-wrapper').data("notif-failed");

      if (messageNotifSuccess != "") {
        swal({
          title: "Berhasil",
          text: messageNotifSuccess,
          icon: "success",
          buttons: false,
          timer: 5000,
        });
      }

      if (messageNotifFailed != "") {
        swal({
          title: "Oops...",
          text: messageNotifFailed,
          icon: "error",
          buttons: false,
          timer: 5000,
        });
      }
    });
  </script>
</body>

<!-- Mirrored from htmldemo.net/rick/rick/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 19 Nov 2023 04:06:31 GMT -->

</html>
