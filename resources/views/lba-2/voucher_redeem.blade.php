@extends('lba-2.master')

@section('content')

<!-- CONTENT -->
<div id="page-content" style="background-color: white; height:100%">
  <div class="container">
    <div class="row">
      <div class="col s12">
        <br>
		<p class="center">
							<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="green" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg>
		</p>
        <h5 class="center text-type-bold">Congratulation</h5>
        <p class="center text-size-normal">
           Kamu berhasil mendapatkan voucher
        </p>
        <br>
			<h5 style="color: green" class="center text-type-bold">{{$voucher->code}}</h5>
		<br>
        <p style="text-align: justify;" class="text-size-normal">
            Voucher <b>berlaku hingga {{ date('d M Y', strtotime($voucher->expires_at)) }}</b> dan dapat digunakan di <b>{{ $voucher->provider_name }} terdekat</b>.<br>
            <br>
            Kami juga telah mengirimkan kode voucher beserta <b>cara pemakaian nya</b> melalui @if($voucher->email) <b>{{$voucher->email}}</b> @endif @if($voucher->email && $voucher->phone_number) dan @endif @if($voucher->phone_number) <b>{{$voucher->phone_number}}</b> @endif nomor kamu ya.
        </p>
        <br></div>
    </div>
  </div>
</div>
<!-- END CONTENT -->


<!-- FOOTER COMPONENT  -->
<footer id="footer" style="background-color: green"> <!-- use bg color with primary color -->