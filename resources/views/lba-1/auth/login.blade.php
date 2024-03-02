@extends('lba-1.auth.master')

@section('content')
<div class="auth-page-footer">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <span class="auth-page-separator text-center space-mt--20 space-mb--20">Login untuk melanjutkan</span>
        <div class="auth-page-social-login">
          <button class="d-flex justify-content-center align-items-center">
            <img src="{{ asset('assets/lba-1/img/icons/google.svg') }}" class="injectable space-mr--10 position-static" style="transform: unset">

            @php
                $urlParams = request()->query();
                $utmSource = isset($urlParams['utm_source']) ? $urlParams['utm_source'] : null;
                $urlParams['brand'] = $brand;
                $urlParams['campaign'] = $campaign;
                $routeName = null;

                if (isset($productId)) {
                    $urlParams['productId'] = $productId;
                    $routeName = 'google::redirect';
                }

                if (isset($voucherCode)) {
                    $urlParams['voucherCode'] = $voucherCode;
                    $routeName = 'google::redirect::rating';
                }

                // Include utm_source in the URL parameters if present
                if ($utmSource !== null) {
                    $urlParams['utm_source'] = $utmSource;
                }

                $urlRedirectGoogle = route($routeName, $urlParams);
            @endphp

            <a href="{{ $urlRedirectGoogle }}">Login dengan Google</a>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
