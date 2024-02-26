@extends('lba-1.master')

@section('content')
<div class="product-content-header-area border-bottom--thick space-pb--25 space-pt--30">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h5 class="mb-3 fw-bold">Nilai Produk</h5>
        <form id="form-render" method="POST"></form>
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
<script src="{{ asset("assets/plugins/star-rating.js") }}"></script>
<script type="text/javascript">
  $("#form-render").formRender({
    formData: {!! $data->formbuilder_rating_json !!},
    dataType: 'json',
    render: true
  });

  $("#form-render").append(`
    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
    <div class="shop-product-button mt-4">
      <button type="submit" class="buy w-100" style="background-color: {{ $data->template_primary_color }}; border-radius: 10px; line-height: 1">
        Kirim
      </button>
    </div>
  `);
</script>
@endsection
