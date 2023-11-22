@extends('lba-1.auth.master')

@section('content')
<div class="auth-page-body">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <!-- Auth form -->
        <div class="auth-form">
          <form action="#">
            <div class="auth-form__single-field space-mb--30">
              <label for="otpCode">Input 6 Digit Kode OTP</label>
              <input type="number" name="otpCode" id="otpCode" placeholder="XXXXXX">
            </div>
            <div class="auth-form__single-field space-mb--40">
              <p class="auth-form__info-text">Tidak terima OTP? <a href="#">Kirim ulang</a></p>
            </div>
            <button type="button" class="auth-form__button" style="background: green"> <!-- button color use primary color -->
              <a href="{{ route('lba-1::voucher-redeem', ['brand' => $brand, 'campaign' => $campaign]) }}">Validasi Kode OTP</a>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
