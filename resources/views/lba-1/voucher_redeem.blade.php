@extends('lba-1.master')

@section('content')
<div class="product-content-header-area border-bottom--thick space-pb--25 space-pt--70">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <p class="text-center space-mb--25">
          <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="green" class="bi bi-check-circle-fill"
            viewBox="0 0 16 16">
            <path
              d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
          </svg>
        </p>

        <h3 class="text-center space-mb--5">Congratulation</h3>
        <h5 class="text-center">Kamu berhasil mendapatkan voucher</h5>
      </div>
    </div>
  </div>
</div>

<!-- product content description -->
<div class="product-content-description border-bottom--thick space-pt--25 space-pb--25 space-mb--25">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <p class="section-title space-mb--25 text-center" style="font-weight:700; font-size:24px; color: green">
          IDNLB0793ZK
        </p>
      </div>
    </div>
  </div>
</div>

<div class="space-pb--25">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <p class="text-center space-mb--15" style="font-size: 16px;">
          Voucher berlaku hingga {expired_date} dan dapat digunakan di {retail_partner} terdekat.
        </p>

        <p class="text-center space-mb--5" style="font-size: 16px;">
          Kami juga telah mengirimkan kode voucher beserta cara pemakaian nya melalui {email} dan {no handphone} kamu ya.
        </p>
      </div>
    </div>
  </div>
</div>

<!--====================  Start footer component ====================-->
<div class="category-slider-area space-pb--25 "> <!-- use secondary color for footer -->
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
