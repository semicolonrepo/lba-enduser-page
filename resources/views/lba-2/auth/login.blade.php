@extends('lba-2.auth.master')

@section('content')
<div class="auth-page-footer">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <span class="auth-page-separator text-center space-mt--20 space-mb--20">Login untuk melanjutkan</span>
        <div class="auth-page-social-login">
          <button class="d-flex justify-content-center align-items-center">
            <img src="{{ asset('assets/lba-2/img/icons/google.svg') }}" class="injectable space-mr--10 position-static" style="transform: unset">
            <a href="{{ route('google::redirect', ['brand' => $brand, 'campaign' => $campaign, 'productId' => $productId]) }}">Login dengan Google</a>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
