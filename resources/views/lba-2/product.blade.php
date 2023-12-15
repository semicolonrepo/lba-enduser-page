@extends('lba-2.master')

@section('content')

<!--====================  product image  ====================-->
<div class="products-area space-pb--25 mt-0">
  <div class="container space-pt--15">
    <div class="row justify-content-center">
      <div class="col-8">
        <div class="grid-product px-3 px-md-5" style="border:1px {{$data->template_primary_color}} solid">
          <div class="grid-product__image">
            <img src="{{ env('BASE_URL_DASHBOARD').'/assets/product/images/'. $product->photo }}" class="img-fluid" alt="" style="height: 200px; object-fit: cover;">
          </div>
          <div class="grid-product__content text-center">
            <h3 class="title fw-bold">{{ $product->name }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--====================  product content ====================-->
<div style="margin-top:-100px" class="product-content-header-area space-pt--100 space-pb--25">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="product-content-header justify-content-center">
          <div class="product-content-header__main-info">
            <h3 class="title text-center fw-bold" style="font-size: 24px">
              @if($product->normal_price == 0)
                GRATIS
              @elseif($product->normal_price != 0 && $product->subsidi_price == 0)
                <p>{{ formatCurrency($product->normal_price) }}</p>
              @else
                <p>SEKARANG HANYA <span class="d-inline-block">{{ formatCurrency($product->normal_price -  $product->subsidi_price) }}</span></p>
              @endif
            </h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- product content description -->
@if ($product->description)
<div class="product-content-description space-pb--25 pt-3">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-10">
        <p class="section-title fw-bold m-0">Tentang Produk:</p>
        <p class="section-title space-mb--25 lh-sm" style="font-size: 16px">
          {!! $product->description !!}
        </p>
      </div>
    </div>
  </div>
</div>
@endif

<div class="accordion accordion-flush">
  <div class="accordion-item">
    <h2 class="accordion-header" id="term-condition">
      <button class="accordion-button accordion-lba-2 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#panel-term-condition" aria-expanded="false" aria-controls="panel-term-condition">
        Syarat dan Ketentuan
      </button>
    </h2>
    <div id="panel-term-condition" class="accordion-collapse collapse show" aria-labelledby="term-condition">
      <div class="accordion-body campaign-term-condition">
        {!! $data->campaign_detail !!}
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="panelsStayOpen-headingThree">
      <button class="accordion-button accordion-lba-2 collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
        Lokasi Merchant
      </button>
    </h2>
    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
      <div class="accordion-body">
        <ul>
        @forelse ($merchantCities as $merchantCity)
          <li>{{ $merchantCity->name }}</li>
        @empty
          -
        @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="grand-total space-pb--15" style="border-radius: 0 0 12px 12px">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="total-shipping pb-2">
          <h3 class="section-title fw-bold text-center mb-3">Pilih Lokasi Penukaran Voucher</h3>
          <div class="row" id="list-partner" style="row-gap: 16px">
            @foreach ($retailer as $retailPartner)
            <div class="col-6">
              <label for="{{ $retailPartner->id }}" class="select-partner">
                <img src="{{ env('BASE_URL_DASHBOARD').'/assets/provider/images/'.$retailPartner->photo }}" height="45px">
                <input type="radio" name="partner" class="d-none partner" value="{{ $retailPartner->id }}" id="{{ $retailPartner->id }}">
              </label>
            </div>
            @endforeach
            @if($internal->isNotEmpty())
            <div class="col-6">
              <label for="internal" class="select-partner">
                <p class="m-0 internal-partner">Merchant Partner Kami</p>
                <input type="radio" name="partner" class="d-none partner" value="internal" id="internal">
              </label>
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="total-shipping pb-2">
          <h3 class="section-title fw-bold text-center mb-3">Isi data dan dapatkan vouchernya</h3>
          @if ($authData->needAuthGmail)
            @if ($authData->isAuthGmail)
              <div class="form-group">
                <label class="w-100 text-center fs-6">Email :</label>
                <input type="text" class="form-control" style="height: 45px" value="{{ $authData->userGmail }}" disabled readonly>
              </div>
            @else
              <div class="auth-page-social-login" style="margin-top:-8px">
                <button class="d-flex justify-content-center align-items-center" style="height: 45px">
                  <img src="{{ asset('assets/lba-2/img/icons/google.svg') }}" class="injectable space-mr--10 position-static" style="transform: unset">
                  <a class="term-condition-link" style="color: unset" href="{{ route('google::redirect', ['brand' => $brand, 'campaign' => $data->slug, 'productId' => $product->id]) }}">
                    Login dengan Google
                  </a>
                </button>
              </div>
            @endif
          @endif

          @if ($authData->needAuthWA)
            <form action="{{ route('otp::send', ['brand' => $brand, 'campaign' => $data->slug, 'productId' => $product->id]) }}" method="post">
                @csrf
              <div class="form-group mt-2">
                <label class="w-100 text-center fs-6">WhatsApp Number :</label>
                <input type="text" name="phone_number" class="form-control" style="height: 45px" value="{{ $authData->userWA }}" {{ $authData->isAuthWA ? 'disabled' : 'required' }}>
              </div>
              @if (!$authData->isAuthWA)
                <div class="shop-product-button mt-3 mb-2">
                  <button type="submit" class="buy w-100"
                    style="border-radius: 10px; line-height: 1; background-color: {{ $data->template_primary_color }}">
                    Kirim OTP Ke WhatsApp
                  </button>
                </div>
              @endif
            </form>
          @endif
        </div>
      </div>

      @if ($authData->isAuthGmail && $authData->isAuthWA)
        <form action="{{ route('voucher::claim', ['brand' => Str::slug($data->brand), 'campaign' => $data->slug, 'productId' => $product->id]) }}" method="post" id="form-get-product">
          @csrf
          <div class="col12">
            <div class="form-check pb-2">
              <input class="form-check-input" type="checkbox" id="check-term-condition" data-primary-color="{{ $data->template_primary_color }}">
              <label class="form-check-label" for="check-term-condition">
                I have read and agreed to the
                <a href="{{ route('term-condition') }}" class="term-condition-link link-primary" target="_blank">
                  Terms and Conditions
                </a>
              </label>
            </div>
          </div>
          <div class="col-12">
            <!-- get voucher button -->
            <div class="shop-product-button">
              <!-- button use primary color -->
              <button form="form-get-product" id="get-voucher" type="submit" class="buy w-100" disabled
                style="background-color: #9CA3AF; cursor: unset; border-radius: 10px; line-height: 1">
                  Dapatkan Voucher Sekarang
              </button>
            </div>
          </div>
        </form>
      @endif
    </div>
  </div>
</div>
<!--====================  End of product content  ====================-->

<div class="category-slider-area space-pb--25 ">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <p class="text-center space-mb--20 space-mt--20">
          <span class="powered-by-text text-center">Powered by</span><br>
          <a href="https://letsbuyasia.com" target="_blank">
            <img style="height:30px" src="https://app-dev.letsbuyasia.id/assets/img/logo-text.png" />
          </a>
        <p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
@vite('resources/js/lba-2/product.js')
@endsection
