@extends('lba-1.master')

@section('content')


<!--====================  Start HEADER COMPONENT ====================-->
<?php
  $header = json_decode($data->template_header_json, true);
  $headerBlock = $header['blocks'];
?>
<div class="space-pb--30" style="background-color: {{$data->template_primary_color}} !important"> <!-- use primary color for header -->
  
  @foreach ($headerBlock as $block)

    @if ($block['type'] == 'carousel')
      <div class="hero-slider bg-color--grey space-y--10">
        <div class="container">
          <div class="row row-10">
            <div class="col-12">
              <div class="hero-slider-wrapper">

                @foreach ($block['data']['items'] as $item)
                  <div class="hero-slider-item d-flex bg-img" data-bg="{{ $item['url'] }}">
                      <div class="container">
                          <div class="row">
                              <div class="col-12">
                                  <!-- hero slider content -->
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
              <!--<h2 class="space-mt--10 space-mb--20">Text left, heading, bold</h2>
              <h2 class="text-center fst-italic section-title space-mt--10 space-mb--20">Text center, medium, italic</h2>
              <p class="text-white text-end text-decoration-underline section-title space-mt--10 space-mb--20">Text right, small, underline w color</p>
              -->

              <!-- text & link component -->
              @include('lba-1.component.text')
 
              <!-- bio link component -->
              @include('lba-1.component.biolink')

            </div>
          </div>
        </div>
      </div>
      @endif
  @endforeach

</div>
<!--====================  End of HEADER COMPONENT  ====================-->

<!--====================  Start of BODY COMPONENT ====================-->
<div class="products-area">
  <div class="container">
    <div class="row">
      <div class="col-12">

        <h2 class="space-mt--10 space-mb--20">Text left, heading, bold</h2>

        <!-- section title -->
        <h2 class="section-title space-mb--20">All Products</h2>

        <!-- all products -->
        <div class="all-products-wrapper space-mb-m--20">
          <div class="row row-10">

            @foreach ($product as $stock)
            <div class="col-6">
              <div class="grid-product space-mb--20">
                <div class="grid-product__image">
                  <a href="#">
                    <img src="{{ env('BASE_URL_DASHBOARD').'/assets/product/images/'.$stock->photo }}" class="img-fluid" alt="">
                  </a>
                </div>
                <div class="grid-product__content">
                  <h3 class="title"><a href="#">{{$stock->name}}</a></h3>
                  <div class="price space-mt--10">
                    <span class="discounted-price">
                      @if(strtolower($stock->type) == 'free')
                        Gratis
                      @else
                        Tawaran menarik
                      @endif
                    </span>
                  </div>
                </div>
              </div>
            </div>
            @endforeach

          </div>
        </div>

        <!-- other component in body -->

        <div class="text-center embed-responsive embed-responsive-1by1 space-mt--20 space-mb--20">
          <iframe style="width:100%; aspect-ratio: 16/9;" class="embed-responsive-item" src="https://www.youtube.com/embed/VhC-Ni_7m4I"
            allowfullscreen></iframe>
        </div>

      </div>
    </div>
  </div>
</div>
<!--====================  End of body component  ====================-->

<!--====================  Start footer component ====================-->
<div style="background-color: yellow !important" class="category-slider-area space-pb--25 ">
  <!-- use secondary color for footer -->
  <div class="container">
    <div class="row">
      <div class="col-12">
        <!-- If in header have text -->
        <h2 class="space-mt--20 space-mb--20">Text left, heading, bold</h2>
        <h2 class="text-center fst-italic section-title space-mt--10 space-mb--20">Text center, medium, italic</h2>
        <p style="color: #4287f5!important;"
          class="text-end text-decoration-underline section-title space-mt--10 space-mb--20">Text right, small,
          underline w color</p>

        <!-- If in header have link -->
        <p class="text-center space-mt--10 space-mb--20">
          <a href="https://google.com" target="_blank" style="color: #4287f5!important;"
            class="text-decoration-underline section-title">
            Link Name Here
          </a>
        </p>

        <p class="powered-by section-title text-center">Powered by</p>
        <p class="text-center space-mb--20">
          <a href="https://letsbuyasia.com" target="_blank">
            <img style="height:30px" src="https://app-dev.letsbuyasia.id/assets/img/logo-text.png" />
          </a>
        <p>
      </div>
    </div>
  </div>
</div>
<!--====================  End of footer Component  ====================-->
@endsection
