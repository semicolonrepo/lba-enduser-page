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

<div class="auth-page-body">
  <div class="container">
    <div class="row">
      <div class="col-12">

          @php
            $urlParams = request()->query();
            $utmSource = isset($urlParams['utm_source']) ? $urlParams['utm_source'] : null;
            $urlParams['brand'] = $brand;
            $urlParams['campaign'] = $campaign;
            $urlParams['phoneNumber'] = $phoneNumber;

            if (isset($productId)) {
                $urlParams['productId'] = $productId;
                $routeName = 'otp::validate::post';
            }

            if (isset($voucherCode)) {
                $urlParams['voucherCode'] = $voucherCode;
                $routeName = 'otp::validate::post::rating';
            }

            // Include utm_source in the URL parameters if present
            if ($utmSource !== null) {
                $urlParams['utm_source'] = $utmSource;
            }

            $urlValidateOtp = route($routeName, $urlParams);
          @endphp

        <!-- Auth form -->
        <div class="auth-form" style="margin-top: 5px">
        <form action="{{ $urlValidateOtp }}" method="post">
          @csrf
            <div class="auth-form__single-field space-mb--20">
              <label for="mobileNumber">Input 5 Digit Kode OTP</label>
              <input type="number" name="otp_number" id="otpCode" placeholder="XXXXX" inputmode="numeric">
            </div>
            <div class="auth-form__single-field space-mb--40">
              <p class="auth-form__info-text">
                Tidak terima OTP?
                <a href="{{ route('otp::resend', ['brand' => $brand, 'campaign' => $campaign, 'productId' => $productId, 'phoneNumber' => $phoneNumber]) }}">
                  Kirim ulang
                </a>
              </p>
            </div>
            <button type="submit" class="auth-form__button" style="background: green"> <!-- button color use primary color -->
              Validasi Kode OTP
            </button>
          </form>
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
