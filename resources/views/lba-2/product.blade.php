@extends('lba-2.master')

@section('css')
  @vite('resources/css/qty-voucher.css')
@endsection

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

<form method="POST" id="form-get-voucher">
  @csrf
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
                  <p>{{$data->description_product_price_template}} <span class="d-inline-block">{{ formatCurrency($product->normal_price -  $product->subsidi_price) }}</span></p>
                @endif
              </h3>
              <p class="text-center mt-4 mb-2">Jumlah Voucher</p>
              <div class="d-flex justify-content-center">
                <button type="button" id="decrement">-</button>
                <input type="text" max="{{ $product->limit_claim }}" name="claim_qty" id="claim-qty" readonly>
                <button type="button" id="increment">+</button>
              </div>
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
        <button class="accordion-button accordion-lba-2 collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#panel-term-condition" aria-expanded="false" aria-controls="panel-term-condition">
          Syarat dan Ketentuan
        </button>
      </h2>
      <div id="panel-term-condition" class="accordion-collapse collapse" aria-labelledby="term-condition">
        <div class="accordion-body campaign-term-condition">
          {!! $data->campaign_detail !!}
        </div>
      </div>
    </div>
    @if($merchantCities->isNotEmpty())
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
    @endif
  </div>

  <div class="grand-total space-pb--15" style="border-radius: 0 0 12px 12px">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="total-shipping pb-0">
            <h3 class="section-title fw-bold text-center mb-1">Pilih Lokasi Penukaran Voucher</h3>
            <h6 class="text-center mb-3">Klik pada logo merchant</h6>
            <div class="row" id="list-partner" style="row-gap: 16px">
              @foreach ($retailer as $retailPartner)
              <div class="col-6">
                <label for="{{ $retailPartner->id }}" class="select-partner" data-partner-checked="{{ session('partner') == $retailPartner->id ? 'true' : 'false' }}">
                  <img src="{{ env('BASE_URL_DASHBOARD').'/assets/provider/images/'.$retailPartner->photo }}" height="45px" class="{{ $retailPartner->remaining_vouchers == 0 ? 'greyscale' : ''}}">
                  <input {{ $retailPartner->remaining_vouchers == 0 ? 'disabled' : ''}} type="radio" name="partner" class="d-none partner" value="{{ $retailPartner->id }}" id="{{ $retailPartner->id }}" {{ session('partner') == $retailPartner->id ? 'checked' : '' }}>
                </label>
              </div>
              @endforeach
              @if($internal->isNotEmpty())
              <div class="col-6">
                <label for="internal" class="select-partner" data-partner-checked="{{ session('partner') == 'internal' ? 'true' : 'false' }}">
                  <p class="m-0 internal-partner">Merchant Partner Kami</p>
                  <input {{ $internal->first()->remaining_vouchers == 0 ? 'disabled' : ''}} type="radio" name="partner" class="d-none partner" value="internal" id="internal" {{ session('partner') == 'internal' ? 'checked' : '' }}>
                </label>
              </div>
              @endif
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="alert alert-danger m-0 mt-2 d-none" role="alert" id="alert"></div>
          <div class="total-shipping pb-2 pt-0">
            @if ($product->questionares_json != null)
              <h3 class="section-title fw-bold text-center mb-3 mt-2">Isi data dan dapatkan vouchernya</h3>
              @foreach (json_decode($product->questionares_json) as $formBuilder)
                @include('show_form_builder')
              @endforeach
            @endif

            @if($data->enabled_recaptcha)
            <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.google_recaptcha.site_key') }}" data-action="claim-voucher" data-expired-callback="expCallback"></div>
            @endif

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

            @if ($authData->needAuthGmail)
              @if ($authData->isAuthGmail)
                <div class="form-group">
                  <label class="w-100 text-center fs-6">Email :</label>
                  <input type="text" class="form-control" style="height: 45px" value="{{ $authData->userGmail }}" disabled readonly>
                </div>
              @else
                <div class="auth-page-social-login" style="margin-top:-8px">
                  @php
                    $urlParams = [];
                    $urlParams['brand'] = $brand;
                    $urlParams['campaign'] = $data->slug;
                    $urlParams['productId'] = $product->id;

                    if (request()->query('utm_source')) {
                        $urlParams['utm_source'] = request()->query('utm_source');
                    }

                    $urlGoogleLogin = route('google::redirect', $urlParams);
                  @endphp
                  <button type="button" id="login-google" class="d-flex justify-content-center align-items-center" style="height: 45px" data-url="{{ $urlGoogleLogin }}" data-brand="{{ $brand }}">
                    <img src="{{ asset('assets/lba-2/img/icons/google.svg') }}" class="injectable space-mr--10 position-static" style="transform: unset">
                    <a class="term-condition-link" style="color: unset">
                      Login dengan Google
                    </a>
                  </button>
                </div>
              @endif
            @endif

            @if ($authData->needAuthWA)
              @php
                $urlParams = request()->query();
                $utmSource = isset($urlParams['utm_source']) ? $urlParams['utm_source'] : null;
                $urlParams['brand'] = $brand;
                $urlParams['campaign'] = $data->slug;
                $urlParams['productId'] = $product->id;

                // Include utm_source in the URL parameters if present
                if ($utmSource !== null) {
                    $urlParams['utm_source'] = $utmSource;
                }

                $urlOTPWa = route('otp::send', $urlParams);
              @endphp
              <input type="hidden" name="partner" class="partner-selected">
              <div class="form-group mt-3">
                <label class="w-100 text-center fs-6">WhatsApp Number :</label>
                <input type="text" name="phone_number" class="form-control" style="height: 45px" value="{{ $authData->userWA }}" {{ $authData->isAuthWA ? 'disabled' : 'required' }}>
              </div>
              @if (!$authData->isAuthWA)
                <div class="shop-product-button mt-3 mb-2">
                  <button type="button" class="buy w-100" data-url="{{ $urlOTPWa }}" id="send-otp"
                    style="border-radius: 10px; line-height: 1; background-color: {{ $data->template_primary_color }}">
                    Kirim OTP Ke WhatsApp
                  </button>
                </div>
              @endif
            @endif

            @if ($authData->isAuthGmail && $authData->isAuthWA)
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

                $urlClaimVoucher = route('voucher::claim', $urlParams);
              @endphp
              <input type="hidden" name="partner" class="partner-selected">
              <div class="col-12 mt-4">
                <div class="shop-product-button">
                  <button type="button" id="get-voucher" data-url="{{ $urlClaimVoucher }}" data-brand="{{ $brand }}" class="buy w-100" disabled
                    style="background-color: #9CA3AF; cursor: unset; border-radius: 10px; line-height: 1">
                    Dapatkan Voucher Sekarang
                  </button>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--====================  End of product content  ====================-->
</form>

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
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.0/autoNumeric.min.js" integrity="sha512-IBnOW5h97x4+Qk4l3EcqmRTFKTgXTd4HGiY3C/GJKT5iJeJci9dgsFw4UAoVfi296E01zoDNb3AZsFrvcJJvPA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    const expCallback = function() {
      grecaptcha.reset();
   };
</script>
@vite(['resources/js/lba-2/product.js', 'resources/css/qty-voucher.css', 'resources/js/qty-voucher.js'])
@endsection
