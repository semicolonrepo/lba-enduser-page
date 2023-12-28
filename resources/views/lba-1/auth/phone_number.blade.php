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

            // Include utm_source in the URL parameters if present
            if ($utmSource !== null) {
                $urlParams['utm_source'] = $utmSource;
            }

            $urlOtpSend = route('otp::send', $urlParams);
          @endphp

          <form action="{{ $urlOtpSend }}" method="post">
          @csrf
            <div class="auth-form__single-field space-mb--30">
              <label for="mobileNumber">No Handphone (Whatsapp)</label>
              <input type="number" name="phone_number" id="mobileNumber" placeholder="081xxxxxxxxx">
            </div>
            <button type="submit" class="auth-form__button" style="background: green"> <!-- button color use primary color -->
              Kirim OTP ke Whatsapp
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
