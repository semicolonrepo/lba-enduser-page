@extends('lba-1.master')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/jquery.rateyo.min.css') }}">
@endsection

@section('content')
<div class="product-content-header-area border-bottom--thick space-pb--25 space-pt--70">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h5 class="mb-3 fw-bold">Nilai Produk</h5>
        <form id="form-render" action="{{ route('rating::post', ['brand' => $data->brand, 'campaign' => $data->slug]) }}" method="POST"></form>
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
<script src="{{ asset("assets/plugins/form-render.min.js") }}"></script>
<script src="{{ asset("assets/plugins/jquery.rateyo.min.js") }}"></script>
<script type="text/javascript">
  let formData = {!! $data->formbuilder_rating_json !!};
  let starRating = null;

  var typeToFind = "starRating";
  var foundIndex = formData.findIndex(item => item.type === typeToFind);

  if (foundIndex !== -1) {
    starRating = formData.splice(foundIndex, 1)[0];
  }

  $("#form-render").formRender({
    formData,
    dataType: 'json',
    render: true
  });

  if (starRating) {
    $("#form-render").append(`
      <div id='star-rating'>
        <label>${starRating.label}</label>
        <input name="${starRating.name}" type="hidden" id="rating-value" />
        <div id="star"></div>
      </div>
      <input type="hidden" value="{{ csrf_token() }}" name="_token" />
      
      <div class="shop-product-button mt-4">
        <button type="submit" class="buy w-100" style="background-color: {{ $data->template_primary_color }}; border-radius: 10px; line-height: 1">
          Kirim
        </button>
      </div>
    `);

    $("#star").rateYo({
      rating: starRating.value,
      spacing: "5px",
      fullStar: true,
      onSet: function (rating) {
        $("#rating-value").val(rating);
      }
    });
  }
</script>
@endsection
