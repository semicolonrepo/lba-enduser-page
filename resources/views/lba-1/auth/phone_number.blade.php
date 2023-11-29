@extends('lba-1.auth.master')

@section('content')
<div class="auth-page-body">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <!-- Auth form -->
        <div class="auth-form">
          <form action="{{ route('otp::send', ['brand' => $brand, 'campaign' => $campaign, 'productId' => $productId]) }}" method="post">
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
