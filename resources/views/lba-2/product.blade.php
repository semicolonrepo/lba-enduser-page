@extends('lba-2.master')

@section('content')

<!--====================  product image  ====================-->
<div class="products-area space-pb--25 mt-0">
  <div class="container space-pt--15">
    <div class="row justify-content-center">
      <div class="col-8">
        <div class="grid-product px-5" style="border:1px {{$data->template_primary_color}} solid">
          <div class="grid-product__image">
            <img src="{{ env('BASE_URL_DASHBOARD').'/assets/product/images/'. $product->photo }}" class="img-fluid" alt="" style="height: 200px">
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
                <p>Rp. {{$product->normal_price}}</p>
              @else
                <p>SEKARANG HANYA Rp. {{$product->normal_price -  $product->subsidi_price}}</p>
              @endif
            </h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- product content description -->
<div class="product-content-description space-pb--25">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-10">
        <p class="section-title fw-bold m-0">Tentang Produk:</p>
        <p class="section-title space-mb--25">
          {!! $product->description !!}
        </p>
      </div>
    </div>
  </div>
</div>

<div class="accordion accordion-flush">
  <div class="accordion-item">
    <h2 class="accordion-header" id="term-condition">
      <button class="accordion-button accordion-lba-2" type="button" data-bs-toggle="collapse" data-bs-target="#panel-term-condition" aria-expanded="false" aria-controls="panel-term-condition">
        Syarat dan Ketentuan
      </button>
    </h2>
    <div id="panel-term-condition" class="accordion-collapse collapse show" aria-labelledby="term-condition">
      <div class="accordion-body">
        {!! $data->campaign_detail !!}
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="panelsStayOpen-headingThree">
      <button class="accordion-button accordion-lba-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
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
      <form action="{{ route('voucher::claim', ['brand' => Str::slug($data->brand), 'campaign' => $data->slug, 'productId' => $product->id]) }}" method="post" id="form-get-product">
        @csrf
        <div class="col-12">
          <div class="total-shipping pb-2">
            <h3 class="section-title fw-bold text-center mb-3">Lokasi Penukaran Voucher</h3>
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
