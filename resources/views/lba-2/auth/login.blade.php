@extends('lba-2.auth.master')

@section('content')

<!-- CONTENT -->
<div id="page-content" style="background-color: white; height:100%">
  <div class="login-form" style="">
    <div class="container">
      <div class="row">
        <div class="col s12" style="margin-top: 20px">
          <div>
            <p class="center text-size-heading text-type-bold">Welcome to LetsBuyAsia</p>
			<p class="center text-size-normal">Number #1 Online-to-Offline Platform</p>
          </div>
        </div>
      </div>
      <div class="row" style="margin-top: 50px">
        <form class="col s12" action="login_otp.html">
          <div class="row">
            <p class="center text-size-normal">Masuk dengan Gmail</p>
          
            <div class="col s12 center">
              <a href="{{ route('google::redirect', ['brand' => $brand, 'campaign' => $campaign, 'productId' => $productId]) }}" style="background: #C71610; margin-top:0px;" class="waves-effect waves-light btn">Gmail</a>
			</div>
          </div>
        </form>
      </div>
  </div>
</div>
<!-- END CONTENT-->
