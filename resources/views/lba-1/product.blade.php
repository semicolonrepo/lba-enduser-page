@extends('lba-1.master')

@section('content')
<!--====================  product image slider ====================-->
<div class="product-image-slider-wrapper space-pb--30 space-mb--30">
  <div class="product-image-single">
    <img src="{{ asset('assets/lba-1/img/product-slider/product1.png') }}" class="img-fluid" alt="">
  </div>
  <div class="product-image-single">
    <img src="{{ asset('assets/lba-1/img/product-slider/product2.png') }}" class="img-fluid" alt="">
  </div>
  <div class="product-image-single">
    <img src="{{ asset('assets/lba-1/img/product-slider/product3.png') }}" class="img-fluid" alt="">
  </div>
</div>
<!--====================  End of product image slider  ====================-->
<!--====================  product content ====================-->
<!-- product content header -->
<div class="product-content-header-area border-bottom--thick space-pb--25">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="product-content-header">
          <div class="product-content-header__main-info">
            <h3 class="title">Product Name</h3>
            <div class="price">
              <span class="discounted-price">Gratis</span>
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
        <p class="section-title space-mb--25">Description Here...</p>
      </div>
    </div>
  </div>
</div>

<div class="grand-total">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="total-shipping">
          <h2 class="section-title">Total shipping</h2>
          <ul>
            <!-- loop voucher list partner here -->
            <li><input type="radio" name="shippingInput"> Alfamart</li>
            <li><input type="radio" name="shippingInput"> Indomaret</li>
            <li><input type="radio" name="shippingInput"> Merchant LetsBuyAsia</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- get voucher button -->
<a href="{{ route('lba-1::login', ['brand' => $brand, 'campaign' => $campaign]) }}" class="w-100">
  <div class="shop-product-button">
    <!-- button use primary color -->
    <button style="background-color: green" class="buy w-100">
      Dapatkan Voucher Sekarang
    </button>
  </div>
</a>
<!--====================  End of product content  ====================-->
@endsection
