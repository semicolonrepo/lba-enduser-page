@extends('lba-2.auth.master')

@section('content')

<!-- CONTENT -->
<div id="page-content" style="background-color: white">
  <div class="login-form">
    <div class="container">
      <div class="row">
        <div class="col s12" style="margin-top: 20px">
          <div>
            <p class="center text-size-heading text-type-bold">Welcome to LetsBuyAsia</p>
			<p class="center text-size-normal">Number #1 Online-to-Offline Platform</p>
          </div>
        </div>
      </div>
      <div class="row">
        <form class="col s12" action="{{ route('otp::send', ['brand' => $brand, 'campaign' => $campaign, 'productId' => $productId]) }}" method="post">
        @csrf
          <div class="row">
			<p class="center text-size-normal">Masuk dengan No. Handphone</p>
          </div>
          <div class="row" style="margin-top: 20px">
            <div class="input-field col s12 m6 l4 offset-m3 offset-l4">
              <input id="phone" type="number" class="validate" name="phone_number" placeholder="081xxxxxxxxx">
              <label for="phone">No. Handphone</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12 m6 l4 offset-m3 offset-l4 center">
              <button style="margin-top:0px;" class="waves-effect waves-light btn" type="submit">Kirim OTP ke Whatsapp</button>
            </div>
          </div>
        </form>
      </div>
	  
    </div>
  </div>
</div>
<!-- END CONTENT-->