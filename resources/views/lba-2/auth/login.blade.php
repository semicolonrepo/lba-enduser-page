@extends('lba-2.master')

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
                  <a href="{{ $item['caption'] }}" target="_blank">
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
      @endif
  @endforeach

</div>
<!--====================  End of HEADER COMPONENT  ====================-->

<div class="auth-page-footer">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="auth-text-title">
          <div class="auth-text-header">Welcome to LetsBuyAsia</div>
          <div class="auth-text-body">Number #1</div>
          <div class="auth-text-body">Online to Offline Platform</div>
        </div>
        <span class="auth-page-separator text-center space-mt--20 space-mb--20">Login untuk melanjutkan</span>
        <div class="auth-page-social-login" style="margin-top:-8px">
          <button class="d-flex justify-content-center align-items-center">
            <img src="{{ asset('assets/lba-2/img/icons/google.svg') }}" class="injectable space-mr--10 position-static" style="transform: unset">
            <a href="{{ route('google::redirect', ['brand' => $brand, 'campaign' => $campaign, 'productId' => $productId]) }}">Login dengan Google</a>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

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
