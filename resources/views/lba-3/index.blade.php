@extends('lba-3.master')

@section('content')


<!--====================  Start HEADER COMPONENT ====================-->
<?php
  $header = json_decode($data->template_header_json, true);
  $headerBlock = $header['blocks'];
?>
<div class="space-pb--30" style="border-radius: 12px; background-color: white !important"> <!-- use primary color for header -->

  @foreach ($headerBlock as $block)

    @if ($block['type'] == 'carousel')
      <div class="hero-slider">
        <div class="container">
          <div class="row row-10">
            <div class="col-12">
              <div class="hero-slider-wrapper">

                @foreach ($block['data']['items'] as $item)
                  <div class="hero-slider-item d-flex bg-img" data-bg="{{ $item['url'] }}">
                      <div class="container">
                          <div class="row">
                              <div class="col-12">
                                  <div class="hero-slider-content">
                                      <p class="hero-slider-content__text">{{ $item['caption'] }}</p>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                @endforeach

              </div>
            </div>
          </div>
        </div>
      </div>
      @else
      <!-- other component header -->
      <div class="category-slider-area">
        <div class="container">
          <div class="row">
            <div class="col-12">

              @include('lba-3.component.text')

              @include('lba-3.component.biolink')

            </div>
          </div>
        </div>
      </div>
      @endif
  @endforeach

</div>
<!--====================  End of HEADER COMPONENT  ====================-->

<!--====================  Start of BODY COMPONENT ====================-->
<?php
  $body = json_decode($data->template_body_json, true);
  $bodyBlock = $body['blocks'];

  $footer = json_decode($data->template_footer_json, true);
  $footerBlock = $footer['blocks'];
?>
<div class="products-area space-pb--25">
  <div class="container">
    <div class="row">
      <div class="col-12">

        @foreach ($bodyBlock as $block2)

          <!-- products component -->
          @if ($block2['type'] == 'product')
            <div class="all-products-wrapper space-mb--10">
              <div class="row row-10">

                @foreach ($product as $stock)
                <div class="col-6">
                  <div class="grid-product space-mb--20" style="border:1px {{$data->template_primary_color}} solid">
                    <div class="grid-product__image">
                        <img src="{{ env('BASE_URL_DASHBOARD').'/assets/product/images/'.$stock->photo }}" class="img-fluid" alt="" style="height: 150px">
                      </a>
                    </div>
                    <div class="grid-product__content">
                      <h3 class="title">
                      @if(!$is_preview)
                          <a href="{{ route('product::show', ['brand' =>  Str::slug($data->brand), 'campaign' => $data->slug, 'productId' => $stock->id]) }}">{{$stock->name}}</a>
                      @else
                          <a href="#">{{$stock->name}}</a>
                      @endif
                        </h3>
                      <!--<div class="price space-mt--10">
                        <span class="discounted-price">
                          @if(strtolower($stock->type) == 'free')
                            Gratis
                          @else
                            Tawaran menarik
                          @endif
                        </span>
                      </div>-->
                    </div>
                  </div>
                </div>
                @endforeach

              </div>
            </div>
          @endif

          @include('lba-3.component.text')

          @include('lba-3.component.embed')

          @include('lba-3.component.image')

        @endforeach

        <!-- footer component -->
        <div class="border-top space-mt--10">
          @foreach ($footerBlock as $block3)

            @include('lba-3.component.text')

            @include('lba-3.component.biolink')

          @endforeach
        </div>

      </div>
    </div>
  </div>
</div>
<!--====================  End of body component  ====================-->

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
