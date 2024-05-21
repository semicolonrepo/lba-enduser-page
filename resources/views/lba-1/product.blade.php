@extends('lba-1.master')

@section('css')
  @vite('resources/css/qty-voucher.css')
@endsection

@section('content')
<!--====================  product image slider ====================-->
<div class="product-image-slider-wrapper">
  <div class="product-image-single">
    <img src="{{ env('BASE_URL_DASHBOARD').'/assets/product/images/'. $product->photo }}" class="img-fluid" alt="">
  </div>
</div>
<!--====================  End of product image slider  ====================-->
<!--====================  product content ====================-->
<!-- product content header -->
@php
  $urlParams = request()->query();
  $utmSource = isset($urlParams['utm_source']) ? $urlParams['utm_source'] : null;
  $urlParams['brand'] = Str::slug($data->brand);
  $urlParams['campaign'] = $data->slug;
  $urlParams['productId'] = $product->id;

  // Include utm_source in the URL parameters if present
  if ($utmSource !== null) {
    $urlParams['utm_source'] = $utmSource;
  }

  $urlClaim = route('voucher::claim', $urlParams);
@endphp

<form action="{{ $urlClaim }}" method="post" id="form-get-product">
  @csrf
  <div style="margin-top:-80px" class="product-content-header-area border-bottom--thick space-pt--100 space-pb--25">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="product-content-header">
            <div class="product-content-header__main-info">
              <h3 class="title" style="font-size: 24px">{{ $product->name }}</h3>
              <div class="price">
                <span class="discounted-price">
                  @if($product->normal_price == 0)
                    Gratis
                  @elseif($product->normal_price != 0 && $product->subsidi_price == 0)
                    <p class="text-black fw-bold">{{ formatCurrency($product->normal_price) }}</p>
                  @else
                    <p class="text-black">{{$data->description_product_price_template}}</p>
                    <p style="text-decoration: line-through; color: red; font-size: 19px">{{ formatCurrency($product->normal_price) }}</p>
                    <p class="text-black fw-bold" style="margin-top: -20px;">{{ formatCurrency($product->normal_price -  $product->subsidi_price) }}</p>
                  @endif
                </span>
              </div>
            </div>
          </div>
          <p class="text-center mt-4 mb-2">Jumlah Voucher</p>
          <div class="d-flex justify-content-center">
            <button type="button" id="decrement" class="lba-1">-</button>
            <input type="text" max="{{ $product->limit_claim }}" name="claim_qty" id="claim-qty" class="lba-1" readonly>
            <button type="button" id="increment" class="lba-1">+</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- product content description -->
  <div class="product-content-description border-bottom--thick space-pt--25 space-pb--25">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <p style="font-weight: 500;" class="section-title space-mb--10">Tentang Produk:</p>
          <p class="section-title space-mb--25">
            {!! $product->description !!}
          </p>
        </div>

        <div class="col-12 campaign-term-condition">
          <p style="font-weight: 500;" class="section-title space-mt--20 space-mb--10">Syarat & Ketentuan:</p>
          <p class="section-title space-mb--25">
            {!! $data->campaign_detail !!}
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="grand-total">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="total-shipping pb-2">
            <h3 style="font-weight: 500;" class="section-title">Pilih Gerai Retail Partner:</h3>
            <p style="font-style:italic; font-weight: 500; font-size:16px;">(Voucher berlaku di gerai retail yang kamu pilih)</p>
            <div class="row" id="list-partner" style="row-gap: 16px">
              @foreach ($retailer as $retailPartner)
              <div class="col-6">
                <label for="{{ $retailPartner->id }}" class="select-partner">
                  <img src="{{ env('BASE_URL_DASHBOARD').'/assets/provider/images/'.$retailPartner->photo }}" height="45px" class="{{ $retailPartner->remaining_vouchers == 0 ? 'greyscale' : ''}}">
                  <input {{ $retailPartner->remaining_vouchers == 0 ? 'disabled' : ''}} type="radio" name="partner" class="d-none partner" value="{{ $retailPartner->id }}" id="{{ $retailPartner->id }}">
                </label>
              </div>
              @endforeach
              @if($internal->isNotEmpty())
              <div class="col-6">
                <label for="internal" class="select-partner">
                  <p class="m-0 internal-partner">Merchant Partner Kami</p>
                  <input {{ $internal->first()->remaining_vouchers == 0 ? 'disabled' : ''}} type="radio" name="partner" class="d-none partner" value="internal" id="internal">
                </label>
              </div>
              @endif
            </div>
            @if ($product->questionares_json != null)
            <div class="col-12">
                <h3 class="section-title fw-bold text-center mb-3">Isi data dan dapatkan vouchernya</h3>
                <form method="POST" id="questionare-form">
                    @csrf
                    @foreach (json_decode($product->questionares_json) as $formBuilder)
                    @include('show_form_builder')
                    @endforeach
                </form>
            </div>
            @endif

            @if($data->enabled_recaptcha)
            <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.google_recaptcha.site_key') }}" data-action="claim-voucher" data-expired-callback="expCallback"></div>
            @endif

            <div class="col12">
              @if (strtoupper($brand) === 'MILO' || strtoupper($brand) === 'BEARBRAND')
                <div class="form-check">
                  <input class="form-check-input check-term-condition" type="checkbox" id="check-term-condition-1" data-primary-color="{{ $data->template_primary_color }}" {{ session('termStatus') ? 'checked' : '' }}>
                  <label class="form-check-label" for="check-term-condition-1">
                    Saya berusia lebih dari 18 tahun. Saya menyetujui
                    <a href="{{ strtoupper($brand) === 'MILO' ? 'https://www.milo.co.id/terms-and-conditions' : '' }}{{ strtoupper($brand) === 'BEARBRAND' ? 'https://www.bearbrand.co.id/term-condition' : '' }}"
                      class="term-condition-link link-primary" target="_blank">Syarat dan Ketentuan</a>
                    yang berlaku.*
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input check-term-condition" type="checkbox" id="check-term-condition-2" data-primary-color="{{ $data->template_primary_color }}" {{ session('termStatus') ? 'checked' : '' }}>
                  <label class="form-check-label" for="check-term-condition-2">
                    Saya memberikan persetujuan kepada PT Nestlé Indonesia dan afiliasinya ("Nestlé") untuk memproses data pribadi saya dengan mengacu pada
                    <a href="{{ strtoupper($brand) === 'MILO' ? 'https://www.milo.co.id/privacy-policy' : '' }}{{ strtoupper($brand) === 'BEARBRAND' ? 'https://www.bearbrand.co.id/privacy-policy' : '' }}"
                      class="term-condition-link link-primary" target="_blank">Kebijakan Kerahasiaan</a>
                    {{ strtoupper($brand) === 'MILO' ? 'MILO' : '' }}{{ strtoupper($brand) === 'BEARBRAND' ? 'BEAR BRAND' : '' }}
                    , saya dapat menarik persetujuan saya kapan saja.*
                  </label>
                </div>
                <div class="form-check mb-4">
                  <input class="form-check-input check-term-condition" type="checkbox" id="check-term-condition-3" data-primary-color="{{ $data->template_primary_color }}" {{ session('termStatus') ? 'checked' : '' }}>
                  <label class="form-check-label" for="check-term-condition-3">
                    Saya bersedia menerima segala informasi mengenai materi promosi, penawaran, dan diskon dari
                    {{ strtoupper($brand) === 'MILO' ? 'MILO' : '' }}{{ strtoupper($brand) === 'BEARBRAND' ? 'BEAR BRAND' : '' }}
                    serta segala bentuk komunikasi lainnya dari Nestlé dan produknya melalui: Buletin dan email, SMS, nomor telepon.*
                  </label>
                </div>
              @else
                <div class="form-check mb-4">
                  <input class="form-check-input check-term-condition" type="checkbox" id="check-term-condition" data-primary-color="{{ $data->template_primary_color }}" {{ session('termStatus') ? 'checked' : '' }}>
                  <label class="form-check-label" for="check-term-condition">
                    I have read and agreed to the
                    <a href="{{ route('term-condition') }}/{{(strtoupper($brand) === 'MILO') ? 'milo' : '' }}{{(strtoupper($brand) === 'BEARBRAND') ? 'bear-brand' : '' }}" class="term-condition-link link-primary" target="_blank">
                      Terms and Conditions
                    </a>
                  </label>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- get voucher button -->
<!-- <a href="#" class="w-100"> -->
  <div class="shop-product-button">
    <!-- button use primary color -->
    <button form="form-get-product" id="get-voucher" type="submit" class="buy w-100" disabled
      style="background-color: #9CA3AF; cursor: unset">
        Dapatkan Voucher Sekarang
    </button>
  </div>
<!-- </a> -->
<!--====================  End of product content  ====================-->
@endsection

@section('js')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    const expCallback = function() {
      grecaptcha.reset();
   };
</script>
@vite(['resources/js/lba-1/product.js', 'resources/css/qty-voucher.css', 'resources/js/qty-voucher.js'])
@endsection
