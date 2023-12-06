@extends('lba-3.master')

@section('content')


<!--====================  Start HEADER COMPONENT ====================-->
<?php
  $header = json_decode($data->template_header_json, true);
  $headerBlock = $header['blocks'];

  $countHeader = count($header['blocks']);
  $containsCarousel = in_array('carousel', array_column($header['blocks'], 'type') ?? []);
?>

@if ($countHeader == 1 && $containsCarousel)
  <div style="border-radius: 12px; background-color: white !important"> <!-- use primary color for header -->
@else
  <div class="space-pb--15" style="border-radius: 12px; background-color: white !important"> <!-- use primary color for header -->
@endif

  @foreach ($headerBlock as $block)

    @if ($block['type'] == 'carousel')
      <div class="hero-slider">
        <div class="container">
          <div class="row row-10">
            <div class="col-12">
              <div class="hero-slider-wrapper" style="margin-left:-12px; margin-right:-12px; margin-bottom:-8px;">

                @foreach ($block['data']['items'] as $item)
                  @if ($item['caption'] != null || $item['caption'] != '')
                  <a href="{{ $item['caption'] }}" id="checkLink" target="_blank">
                  @endif
                    <div class="hero-slider-item d-flex bg-img" data-bg="{{ $item['url'] }}">
                        <!--<div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="hero-slider-content">
                                        <p class="hero-slider-content__text">{{ $item['caption'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                    </div>
                  @if ($item['caption'] != null || $item['caption'] != '')
                  </a>
                  @endif
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
                        <div class="price space-mt--10">
                        <span class="discounted-price">
                        @if($stock->normal_price == 0)
                            Gratis
                        @elseif($stock->normal_price != 0 && $stock->subsidi_price == 0)
                            <p>Rp. {{$stock->normal_price}}</p>
                        @else
                            <p style="text-decoration: line-through; color: red;">Rp. {{$stock->normal_price}}</p>
                            <p style="margin-top: -8px;">Rp. {{$stock->normal_price -  $stock->subsidi_price}}</p>
                        @endif
                        </span>
                      </div>
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
        @if (count($footerBlock) > 0)
          <div class="border-top space-mt--10">
            @foreach ($footerBlock as $block3)

              @include('lba-3.component.text')

              @include('lba-3.component.biolink')

            @endforeach
          </div>
        @endif

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
