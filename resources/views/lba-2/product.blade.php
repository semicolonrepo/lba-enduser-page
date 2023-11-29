@extends('lba-2.master')

@section('content')

<!-- Product Detail -->
<div id="page-content" class="product-page" style="background-color: white">
  <div id="product-image" class="pg-product-image">
    <!-- image -->
    <div>
      <div class="pgp-wrap-img">
        <div class="pgp-wrap-img-in">
          <div class="pgp-img" style="background-image: url('{{ env('BASE_URL_DASHBOARD').'/assets/product/images/'. $product->photo }}');">
          </div>
        </div>
      </div>
    </div>
</div>

  <div class="container" style="padding-top: 20px; background-color: white">
    <div class="row">
      <div class="col s12">
        <div class="name-price">
          <div class="pg-product-name">{{ $product->name }}</div>
          <div style="clear:both;"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="desciption-product">
    <div class="container">
      <div class="row">
        <div class="col s12">
           <p class="text-size-normal">
            <b>Tentang Produk:</b><br>
            {!! $product->description !!}
		   </p>

           <p class="text-size-normal" style="margin-top:20px">
            <b>Syarat & Ketentuan:</b><br><br>
            {!! $product->campaign_detail !!}
		   </p>

		   <div class="shipping-checkout-page payment-method-wrap ck-box">
            <div class="row">
              <div class="input-field col s12 m12 l12 ">
                <div class="payment-method-text">
                  <i class="far fa-credit-card"></i> Pilih Voucher yang berlaku di:
                </div>
              </div>
            </div>

            <form action="{{ route('voucher::claim', ['brand' => Str::slug($data->brand), 'campaign' => $data->slug, 'productId' => $product->id]) }}" method="post" id="form-get-product">
            @csrf

                @foreach ($retailer as $retailPartner)
                    <div class="row">
                    <div class="col s12 m12 l12 ">
                        <p>
                        <input class="with-gap" name="partner" type="radio" value="{{ $retailPartner->id }}" id="{{ $retailPartner->id }}"/>
                        <label for="{{ $retailPartner->id }}">{{ $retailPartner->name }}</label>
                        </p>
                    </div>
                    </div>
                @endforeach
                @if($internal->isNotEmpty())
                    <div class="row">
                    <div class="col s12 m12 l12 ">
                        <p>
                        <input class="with-gap" name="partner" type="radio" value="internal" id="internal"/>
                        <label for="internal">Merchant Partner Kami</label>
                        </p>
                    </div>
                    </div>
                @endif
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="qty-total-price">
    <div class="container">
      <div class="row">
        <div class="col s12">
			<!-- use secondary color -->
			<button form="form-get-product" type="submit" style="background-color: {{ $data->template_primary_color }}" class="btn button-add-cart">Dapatkan Voucher Sekarang</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- END Product -->

<!-- FOOTER COMPONENT  -->
<?php
  $footer = json_decode($data->template_footer_json, true);
  $footerBlock = $footer['blocks'];
?>
<footer id="footer" style="background-color: {{ $data->template_primary_color }}"> <!-- use bg color with primary color -->
<div class="footer-info">
  <div class="container">
    <div class="col s12 center">

        @foreach ($footerBlock as $block3)

            <!-- Text Component -->
            @include('lba-2.component.text')

            <!-- Bio Link component -->
            @include('lba-2.component.biolink')

        @endforeach

    </div>
  </div>
</div>
