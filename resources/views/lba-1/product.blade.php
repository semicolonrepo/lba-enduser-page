@extends('lba-1.master')

@section('content')
<!--====================  product image slider ====================-->
<div class="product-image-slider-wrapper">
  <div class="product-image-single">
    <img src="{{ env('BASE_URL_DASHBOARD').'/assets/product/images/'.$product->photo }}" class="img-fluid" alt="">
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
                @if(strtolower($product->type) == 'free')
                  Gratis
                @else
                  Tawaran menarik
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
          {{ $product->description }}
        </p>
      </div>

      <div class="col-12">
        <p style="font-weight: 500;" class="section-title space-mt--20 space-mb--10">Syarat & Ketentuan:</p>
        <p class="section-title space-mb--25">
          {{ $data->campaign_detail }}
        </p>
      </div>
    </div>
  </div>
</div>

<div class="grand-total">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="total-shipping">
          <h3 style="font-weight: 500;" class="section-title">Pilih Gerai Retail Partner:</h3>
          <p style="font-style:italic: font-weight: 500; font-size:16px">
            (Voucher berlaku di gerai retail yang kamu pilih)
          </p>
          <ul style="margin-top: -20px;">
            @foreach ($retailer as $retailPartner)
              <li style="font-size: 18px; padding-left:12px">
                <input style="padding-left: 8px" type="radio" name="{{ $retailPartner->id }}"> {{ $retailPartner->name }}
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- get voucher button -->
<a href="#" class="w-100">
  <div class="shop-product-button">
    <!-- button use primary color -->
    <button style="background-color: {{ $data->template_primary_color }}" class="buy w-100">
      Dapatkan Voucher Sekarang
    </button>
  </div>
</a>
<!--====================  End of product content  ====================-->
@endsection
