@extends('lba-1.master')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/jquery.rateyo.min.css') }}">
@endsection

@section('content')
<div class="product-content-header-area border-bottom--thick space-pb--25 space-pt--30">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h5 class="mb-3 fw-bold">Penilaian Produk</h5>
        <form action="{{ route('rating::store', ['brand' => $brandSlug, 'campaign' => $campaignSlug, 'voucherCode' => $voucher->code]) }}" id="form-render" method="POST">
          @csrf
          @php $starRatings = []; @endphp
          @if ($data->formbuilder_rating_json != null)
            @foreach (json_decode($data->formbuilder_rating_json) as $formBuilder)
              @include('show_form_builder')
              @if ($formBuilder->type === 'starRating')
                @php array_push($starRatings, $formBuilder->name) @endphp
              @endif
            @endforeach
            <div class="shop-product-button mt-4">
              <button type="submit" class="buy w-100" style="background-color: {{ $data->template_primary_color }}; border-radius: 10px; line-height: 1">
                Kirim
              </button>
            </div>
          @endif
        </form>
      </div>
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

@section('js')
<script src="{{ asset('assets/plugins/jquery.rateyo.min.js') }}"></script>
<script>
$(document).ready(function(){
  const starRatings = {!! json_encode($starRatings) !!};

  $.each(starRatings, function(index, starRating) {
    $(`#${starRating}`).rateYo({
      spacing: '5px',
      fullStar: true,
      onSet: function (rating, rateYoInstance) {
        $(`#form-render input[name='${rateYoInstance.node.id}']`).val(rating);
      }
    });
  });
});
</script>
@endsection
