@extends('lba-1.master')

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
                  <p>Rp. {{$product->normal_price}}</p>
                @else
                    <p style="text-decoration: line-through; color: red;">Rp. {{$product->normal_price}}</p>
                    <p style="margin-top: -20px;">Rp. {{$product->normal_price -  $product->subsidi_price}}</p>
                @endif
              </span>
            </div>
          </div>
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
        <form action="{{ route('voucher::claim', ['brand' => Str::slug($data->brand), 'campaign' => $data->slug, 'productId' => $product->id]) }}" method="post" id="form-get-product">
            @csrf
            <div class="col-12">
                <div class="total-shipping pb-2">
                    <h3 style="font-weight: 500;" class="section-title">Pilih Gerai Retail Partner:</h3>
                    <p style="font-style:italic; font-weight: 500; font-size:16px;">
                        (Voucher berlaku di gerai retail yang kamu pilih)
                    </p>
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
        </form>
    </div>
  </div>
</div>

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
@vite('resources/js/lba-1/product.js')
@endsection
