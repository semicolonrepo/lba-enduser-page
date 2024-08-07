@extends('lba-2.master')

@section('content')


<!--====================  Start HEADER COMPONENT ====================-->
<?php
  $cover = json_decode($data->template_cover_json, true);
  $coverBlock = $cover['blocks'];
?>

<div style="padding:14px; border-radius: 12px; background-color: white !important">
  <div class="hero-slider" style="height:670px">
    <div class="container">
      <div class="row row-10">
        <div class="col-12">

          @foreach ($coverBlock as $block)
            @if ($block['type'] == 'carousel')
              <div class="hero-slider-wrapper" style="height: 100%; margin-left:-12px; margin-right:-12px; margin-bottom:-8px;">

                @foreach ($block['data']['items'] as $item)
                  <div class="cover-hero-slider-item d-flex bg-img" data-bg="{{ $item['url'] }}" style="background-position: center; background-size: contain;">
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
                @endforeach
              </div>
            @elseif ($block['type'] == 'paragraph')
              <div class="cover-product-button">

                @php
                  $urlParams = request()->query();
                  $utmSource = isset($urlParams['utm_source']) ? $urlParams['utm_source'] : null;
                  $urlParams['brand'] = Str::slug($data->brand);
                  $urlParams['campaign'] = $data->slug;

                  // Include utm_source in the URL parameters if present
                  if ($utmSource !== null) {
                      $urlParams['utm_source'] = $utmSource;
                  }

                  $url = route('index', $urlParams);
                @endphp

                <a style="width:100%" href="{{ $url }}" style="text-decoration: none;">
                  <button style="background-color:{{ $data->template_primary_color }} !important" class="next">
                    @if ($block['data']['text'] != '')
                      {{ $block['data']['text'] }}
                    @else
                      Dapatkan Voucher
                    @endif
                  </button>
                </a>
              </div>
            @endif

          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
<!--====================  End of HEADER COMPONENT  ====================-->

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
