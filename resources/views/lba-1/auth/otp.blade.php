@extends('lba-1.auth.master')

@section('content')
<div class="auth-page-body">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <!-- Auth form -->
        <div class="auth-form">

          @php
            $urlParams = request()->query();
            $utmSource = isset($urlParams['utm_source']) ? $urlParams['utm_source'] : null;
            $urlParams['brand'] = $brand;
            $urlParams['campaign'] = $campaign;
            $urlParams['productId'] = $productId;
            $urlParams['phoneNumber'] = $phoneNumber;

            // Include utm_source in the URL parameters if present
            if ($utmSource !== null) {
                $urlParams['utm_source'] = $utmSource;
            }

            $urlValidateOtp = route('otp::validate::post', $urlParams);
          @endphp

          <form action="{{ $urlValidateOtp }}" method="post">
            @csrf
            <div class="auth-form__single-field space-mb--30">
              <label for="otpCode">Input 5 Digit Kode OTP</label>
              <input type="number" name="otp_number" id="otpCode" placeholder="XXXXXX">
            </div>
            <div class="auth-form__single-field space-mb--40">
              <p class="auth-form__info-text">
                Tidak terima OTP? 
                <a href="{{ route('otp::resend', ['brand' => $brand, 'campaign' => $campaign, 'productId' => $productId, 'phoneNumber' => $phoneNumber]) }}">
                  Kirim ulang
                </a>
              </p>
            </div>
            <button type="submit" class="auth-form__button" style="background: green"> <!-- button color use primary color -->
              Validasi Kode OTP
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
