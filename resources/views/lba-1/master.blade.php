<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>LetsBuyAsia - {{ $data->campaign }}</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta property="og:title" content="{{ $data->campaign }}">
  <meta property="og:description" content="{{ $data->campaign }}">
  <meta property="og:image" content="{{ env('BASE_URL_DASHBOARD').'/assets/brand/images/'.$data->brand_logo }}">
  <meta property="og:url" content="{{ url()->full() }}">
  <meta property="og:type" content="website">
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
  <link rel="stylesheet" href="{{ asset('assets/lba-1/css/custom.css') }}">

  <link href="https://fonts.googleapis.com/css?family=Libre+Barcode+128&display=swap" rel="stylesheet">
  <style>
    .mw-500 {max-width: 500px; margin: 0 auto;}
    .bg-auth {width: 500px; height: 100%; position: absolute; object-fit: cover; z-index: -1;}
    
    .code128 {
        font-family: "Libre Barcode 128";
        font-size: 3rem;
        transform: scaleY(1.5);
    }
  </style>
</head>

@if($data->template_background != null || $data->template_background != '')
  <body class="mw-500" style="background-image: url('{{ env('BASE_URL_DASHBOARD').'/assets/pageview/background/'.$data->template_background }}'); background-size: cover; background-attachment: fixed;">
@else
  <body class="mw-500">
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

  <div class="body-wrapper space-pt--70" @if (session('success')) data-notif-success="{{session('success')}}" @else data-notif-success="" @endif 
    @if (session('failed')) data-notif-failed="{{session('failed')}}" @else data-notif-failed="" @endif>
    <!--====================  header area ====================-->
    <header>
      <div class="header-wrapper border-bottom">
        <div class="container space-y--15 mw-500">
          <div class="row align-items-center">
            <div class="col-auto">
              <!-- header logo -->
              <div class="header-logo">
                <a href="{{ route('index', ['brand' => $data->brand, 'campaign' => $data->slug]) }}">
                  <img src="{{ env('BASE_URL_DASHBOARD').'/assets/brand/images/'.$data->brand_logo }}" class="img-fluid" alt="" style="height: 41px">
                </a>
              </div>
            </div>
            <div class="col d-flex justify-content-center">

            </div>
            <div class="col-auto">
              <!-- header menu trigger -->
              <button id="share-button" class="header-menu-trigger" id="header-menu-trigger">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                  class="bi bi-share-fill" viewBox="0 0 16 16">
                  <path
                    d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

    </header>
    <!--====================  End of header area  ====================-->
    @yield('content')
  </div>

  <!-- JS
    ============================================ -->
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
  <script src="{{ asset('assets/plugins/sweetalert.min.js') }}"></script>
  <!-- Main JS -->
  <script src="{{ asset('assets/lba-1/js/main.js') }}"></script>
  <script src="{{ asset('assets/lba-1/js/colorUtils.js') }}"></script>
  <script src="{{ asset('assets/lba-1/js/select-partner.js') }}"></script>
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

  <script id="rendered-js" >
  const shareButton = document.querySelector('#share-button');

  shareButton.addEventListener('click', event => {
    if (navigator.share) {
      navigator.share({
          url: '{{ url()->full() }}',
          title: '{{$data->campaign}}',
          text: '{{$data->campaign}}'
      })
      .then(() => {
        console.log('Thanks for sharing!');
      })
      .catch(console.error);
    } else {
      //shareDialog.classList.add('is-open');
      console.log('error share');
    }
  });
  </script>
</body>

</html>
