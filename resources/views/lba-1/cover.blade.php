@extends('lba-1.master')

@section('content')


<!--====================  Start HEADER COMPONENT ====================-->
<?php
  $cover = json_decode($data->template_cover_json, true);
  $coverBlock = $cover['blocks'];
?>

<div style="padding:14px; background-color: white !important">
  <div class="hero-slider" style="height:670px">
    <div class="container">
      <div class="row row-10">
        <div class="col-12">

          @foreach ($coverBlock as $block)
            @if ($block['type'] == 'carousel')
              <div class="hero-slider-wrapper" style="height: 100%; margin-left:-12px; margin-right:-12px; margin-bottom:-8px;">

                @foreach ($block['data']['items'] as $item)
                  <div class="cover-hero-slider-item d-flex bg-img" data-bg="{{ $item['url'] }}">
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
                <a style="width:100%" href="{{ route('index', ['brand' => Str::slug($data->brand), 'campaign' => $data->slug]) }}" style="text-decoration: none;">
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
