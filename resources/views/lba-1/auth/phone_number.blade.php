@extends('lba-1.auth.master')

@section('content')
<div class="auth-page-body">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <!-- Auth form -->
        <div class="auth-form">
          <form>
            <div class="auth-form__single-field space-mb--30">
              <label for="mobileNumber">No Handphone (Whatsapp)</label>
              <input type="number" name="mobileNumber" id="mobileNumber" placeholder="081xxxxxxxxx">
            </div>
            <button type="button" class="auth-form__button" style="background: green"> <!-- button color use primary color -->
              <a href="{{ route('lba-1::login::otp', ['brand' => $brand, 'campaign' => $campaign]) }}">Kirim OTP ke Whatsapp</a>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
