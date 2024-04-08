@extends('lba-2.master')

@section('css')
  <meta name="csrf_token" content="{{ csrf_token() }}" />
  <link rel="stylesheet" href="{{ asset('assets/plugins/swiper-bundle.min.css') }}"/>
  @vite('resources/css/voucher-redeem.css')
@endsection

@section('content')

<?php
require_once base_path('vendor/autoload.php');
use Hbgl\Barcode\Code128Encoder;

$thankpage = json_decode($data->template_thankyou_json, true);

if ($vouchers->first()->provider_name == 'Indomaret') {
  $voucherText = 'i-Kupon';
} else {
  $voucherText = 'voucher';
}
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
        <h5 class="text-center lh-sm">Kamu berhasil mendapatkan {{ $voucherText . ' ' . $vouchers->first()->product_name }}</h5>
        @if($thankpage != null)
          @php
          $thankBlock = $thankpage['blocks'];
          @endphp

          @foreach ($thankBlock as $block)
            @if ($block['type'] == 'text_header')
              @if ($block['data']['text'] != '')
              <h5 class="text-{{$block['data']['alignment']}} lh-sm mt-1">{!! $block['data']['text'] !!}</h5>
              @endif
            @endif
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>

<!-- product content description -->
<div class="product-content-description border-bottom--thick space-pt--25">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="swiper">
          <div class="swiper-wrapper">
            @foreach ($vouchers as $voucher)
              <div class="swiper-slide d-flex justify-content-center align-items-center flex-column">
                <p class="section-title text-center voucher-code" style="font-weight:700; font-size:24px; color: green">{{ $voucher->code }}</p>
                <div class="code128 text-center mb-4">{{ htmlspecialchars(Code128Encoder::encode($voucher->code)) }}</div>
              </div>
            @endforeach
          </div>
          <div class="swiper-pagination"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-button-next"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="space-pb--25" style="background: white">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <p class="text-center space-mt--30 space-mb--15" style="font-size: 16px;">
          {{ $voucherText == 'voucher' ? Str::ucfirst($voucherText) : $voucherText }} <b> berlaku hingga {{ date('d M Y', strtotime($vouchers->first()->expires_at)) }}</b> dan dapat digunakan di <b>{{ $vouchers->first()->provider_name }} terdekat</b>.
        </p>

        <p class="text-center space-mb--5" style="font-size: 16px;">
          Kami juga telah mengirimkan {{ $voucherText }} beserta <b>cara pemakaian nya</b> melalui @if($vouchers->first()->email) <b>{{$vouchers->first()->email}}</b> @endif @if($vouchers->first()->email && $vouchers->first()->phone_number) dan @endif @if($vouchers->first()->phone_number) <b>{{$vouchers->first()->phone_number}}</b> nomor @endif kamu ya.
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
                <iframe id='test' style="width:100%; aspect-ratio: 16/9; border-radius: 8px;" class="embed-responsive-item youtube-embed" data-url-activity="{{route('youtube::activity', ['campaignId'=> $data->id, 'productId' => $productId])}}" data-video-id="{{$videoId}}" src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
              </div>
            @endif

            @if ($block['type'] == 'image')
              <div class="mt-2">
                <img src="{{ $block['data']['file']['url'] }}" alt="{{ $block['data']['caption'] }}" class="w-100 rounded-2">
              </div>
            @endif
          @endforeach
        @endif
      </div>
      <div class="col-12 mt-4 gap-2 d-flex flex-column">
        @if(!empty(json_decode($data->formbuilder_rating_json)))
        <div class="shop-product-button mb-2">
          <a class="w-100" data-brand="{{ $brand }}" data-campaign="{{ $campaign }}" id="rating-product">
            <button class="buy w-100" style="background-color: {{ $data->template_primary_color }}; border-radius: 10px; line-height: 1">
              Nilai Produk
            </button>
          </a>
        </div>
        @endif
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


@section('js')
  <script src="https://cdn.jsdelivr.net/npm/jquery.iframetracker@2.1.0/dist/jquery.iframetracker.min.js"></script>
  <script src="{{ asset('assets/plugins/swiper-bundle.min.js') }}"></script>
  @vite('resources/js/voucher-redeem.js')
@endsection
