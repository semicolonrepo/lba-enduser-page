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
});
