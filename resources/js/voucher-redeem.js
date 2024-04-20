$(document).ready(function () {
  const brand = $('#rating-product').data('brand');
  const campaign = $('#rating-product').data('campaign');
  const urlRating = `/${brand}/${campaign}/rating/`;

  new Swiper('.swiper', {
    loop: true,
    pagination: {
      el: '.swiper-pagination',
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    on: {
      init: function () {
        setUrlRatingProduct();
      },
      slideChangeTransitionEnd: function () {
        setUrlRatingProduct();
      }
    }
  });

  function setUrlRatingProduct() {
    const voucherCode = $('.swiper-slide-active').find('.voucher-code').text();
    $('#rating-product').attr('href', urlRating + voucherCode);
  }

    const arrayIdClicked = [];
    $('iframe').iframeTracker({
        blurCallback: function(event) {
            if (!arrayIdClicked.includes(this._overId)) {
                arrayIdClicked.push(this._overId);

                $.ajax({
                    url: this.activityUrl,
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                    data:  {
                        'link_video' : this.videoLink
                    },
                    success: function(res) {

                    }
                });
            }
		},
        overCallback: function(element, event) {
			this._overId = $(element).data('video-id');
            this.activityUrl = $(element).data('url-activity');
            this.videoLink = $(element).attr('src');
		},
    });
});
