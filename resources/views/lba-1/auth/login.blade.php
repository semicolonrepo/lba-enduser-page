@extends('lba-1.auth.master')

@section('content')
<div class="auth-page-footer">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <span class="auth-page-separator text-center space-mt--20 space-mb--20">Login dengan Email & No. Handphone</span>
        <div class="auth-page-social-login">
          <button class="d-flex justify-content-center align-items-center">
            <img src="{{ asset('assets/lba-1/img/icons/google.svg') }}" class="injectable space-mr--10 position-static" style="transform: unset">
            <a href="#">Login dengan Google</a>
          </button>
        </div>
        <div class="auth-page-social-login mt-3">
          <button class="d-flex justify-content-center align-items-center">
            <img src="{{ asset('assets/lba-1/img/icons/whatsapp.svg') }}" class="injectable space-mr--10 position-static" style="transform: unset">
            <a href="{{ route('lba-1::login::phone-number', ['brand' => $brand, 'campaign' => $campaign]) }}">Login dengan No. Handphone</a>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
