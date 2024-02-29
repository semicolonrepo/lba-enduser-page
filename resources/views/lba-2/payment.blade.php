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
            embedId: 'snap-container',

            onSuccess: function(result){
              console.log(result);
              
              var data = {
                brand: '{{$brand}}',
                campaign: '{{$data->slug}}',
                order_id: result.order_id,
                status_code: result.status_code,
                transaction_status: result.transaction_status
              };

              // Use the fetch API to make an AJAX POST request
              fetch('{{$baseUri}}/api/payment-redirect', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                  },
                  body: JSON.stringify(data),
              })
              .then(response => {
                  if (response.ok) {
                      return response.json();
                  } else {
                      throw new Error('Failed to process payment');
                  }
              })
              .then(responseData => {
                  console.log(responseData);

                  if (responseData.status === 200) {
                    alert('Pembayaran berhasil, Anda akan diarahkan ke halaman voucher!');

                      var redirectUrl = responseData.redirect_url;
                      window.location.href = redirectUrl;
                  } else {
                      console.error('Non-200 status:', responseData);
                      alert('Something went wrong with the payment redirect.');
                  }
              })
              .catch(error => {
                  // Handle errors or non-successful responses
                  console.error('Error:', error);
                  alert('Something wrong!');
              });
            },
            onPending: function(result){
              alert("Menunggu pembayaran Anda!"); console.log(result);
              location.reload();
            },
            onError: function(result){
              alert("Pembayaran Anda gagal!"); console.log(result);
              location.reload();
            },
            onClose: function(){
              alert('Anda belum melakukan pembayaran. Yakin ingin pergi?');
              location.reload();
            }
        });
    });
</script>
