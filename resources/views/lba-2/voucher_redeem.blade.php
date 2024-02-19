@extends('lba-2.master')

@section('content')

<?php
require_once base_path('vendor/autoload.php');
use Hbgl\Barcode\Code128Encoder;

try {
  $encoded_voucher = Code128Encoder::encode($voucher->code);
  $hasEncoded = true;
} catch (\Exception $e) {
  $hasEncoded = false;
}

$thankpage = json_decode($data->template_thankyou_json, true);
?>

<div class="product-content-header-area border-bottom--thick space-pb--25 space-pt--30" style="border-top-left-radius: 12px; border-top-right-radius: 12px">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <p class="text-center space-mb--25">
          <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="green" class="bi bi-check-circle-fill"
            viewBox="0 0 16 16">
            <path
              d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
          </svg>
        </p>

        <h3 class="text-center space-mb--5">Selamat!</h3>
        <h5 class="text-center lh-sm">Kamu berhasil mendapatkan voucher {{ $voucher->product_name }}</h5>
      </div>
    </div>
  </div>
</div>

<!-- product content description -->
<div class="product-content-description border-bottom--thick space-pt--25">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <p class="section-title text-center" style="font-weight:700; font-size:24px; color: green">
            {{$voucher->code}}
            @if($hasEncoded)
            <br><div class="code128 text-center">{{ htmlspecialchars($encoded_voucher) }}</div>
            @else
            <p></p>
            @endif
        </p>
      </div>
    </div>
  </div>
</div>

<div class="space-pb--25" style="background: white">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <p class="text-center space-mt--30 space-mb--15" style="font-size: 16px;">
          Voucher <b>berlaku hingga {{ date('d M Y', strtotime($voucher->expires_at)) }}</b> dan dapat digunakan di <b>{{ $voucher->provider_name }} terdekat</b>.
        </p>

        <p class="text-center space-mb--5" style="font-size: 16px;">
          Kami juga telah mengirimkan kode voucher beserta <b>cara pemakaian nya</b> melalui @if($voucher->email) <b>{{$voucher->email}}</b> @endif @if($voucher->email && $voucher->phone_number) dan @endif @if($voucher->phone_number) <b>{{$voucher->phone_number}}</b> nomor @endif kamu ya.
        </p>

        @if($thankpage != null)
          @php
          $thankBlock = $thankpage['blocks'];
          @endphp

          @foreach ($thankBlock as $block)
            @if ($block['type'] == 'paragraph')
              @if ($block['data']['text'] != '')
              <p class="text-center" style="font-size: 16px;">{!! $block['data']['text'] !!}</p>
              @endif
            @endif

            @if ($block['type'] == 'youtubeEmbed')
              @php
                $urlParts = parse_url($block['data']['url']);
                parse_str($urlParts['query'], $query);
                $videoId = $query['v'];
              @endphp
              <div class="text-center embed-responsive embed-responsive-1by1 space-mt--20">
                <iframe style="width:100%; aspect-ratio: 16/9; border-radius: 8px;" class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
              </div>
            @endif
          @endforeach
        @endif
      </div>
      <div class="col-12 mt-4 gap-2 d-flex flex-column">
        <div class="shop-product-button">
          <a href="https://t.me/LetsbuyAsia" target="_blank" class="w-100">
            <button class="buy w-100" style="background-color: {{ $data->template_primary_color }}; border-radius: 10px; line-height: 1">
              Join Komunitas
            </button>
          </a>
        </div>
        {{-- <div class="shop-product-button">
          <a href="#" class="w-100">
            <button class="buy w-100" style="background-color: unset; color: #4e4e4e; border: 1px solid {{ $data->template_primary_color }}; border-radius: 10px; line-height: 1">
              Cek Promo Lainnya
            </button>
          </a>
        </div> --}}
      </div>
    </div>
  </div>
</div>

<!--====================  Start footer component ====================-->
<div class="category-slider-area space-pb--25" style="background: white"> <!-- use secondary color for footer -->
  <div class="container">
    <div class="row">
      <div class="col-12">
        <p class="powered-by section-title text-center">Powered by</p>
        <p class="text-center space-mb--20">
          <a href="https://letsbuyasia.com" target="_blank">
            <img style="height:30px" src="https://app-dev.letsbuyasia.id/assets/img/logo-text.png" />
          </a>
        <p>
      </div>
    </div>
  </div>
</div>
<!--====================  End of footer Component  ====================-->
@endsection
