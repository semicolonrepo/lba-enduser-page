@extends('lba-2.master')

@section('content')

<script type="text/javascript" src="{{$snapUrl}}" data-client-key="{{$clientKey}}"></script>

<div class="product-content-header-area border-bottom--thick space-pb--25 space-pt--30" style="border-top-left-radius: 12px; border-top-right-radius: 12px">
  <div class="container">
    <div class="row">
      <div class="col-12" id="snap-container"></div>
    </div>
  </div>
</div>

<!--====================  Start footer component ====================-->
<div class="category-slider-area space-pb--25" style="background: white"> <!-- use secondary color for footer -->
  <div class="container">
    <div class="row">
      <div class="col-12">
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

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        window.snap.embed('{{ $snapToken }}', {
            embedId: 'snap-container'
        });
    });
</script>
