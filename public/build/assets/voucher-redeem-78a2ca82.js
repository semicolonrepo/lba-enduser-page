$(document).ready(function(){const a=$("#rating-product").data("brand"),r=$("#rating-product").data("campaign"),e=`/${a}/${r}/rating/`;new Swiper(".swiper",{loop:!0,pagination:{el:".swiper-pagination"},navigation:{nextEl:".swiper-button-next",prevEl:".swiper-button-prev"},on:{init:function(){i()},slideChangeTransitionEnd:function(){i()}}});function i(){const t=$(".swiper-slide-active").find(".voucher-code").text();$("#rating-product").attr("href",e+t)}const n=[];$("iframe").iframeTracker({blurCallback:function(t){n.includes(this._overId)||(n.push(this._overId),$.ajax({url:this.activityUrl,type:"POST",headers:{"X-CSRF-TOKEN":$('meta[name="csrf_token"]').attr("content")},data:{link_video:this.videoLink},success:function(o){}}))},overCallback:function(t,o){this._overId=$(t).data("video-id"),this.activityUrl=$(t).data("url-activity"),this.videoLink=$(t).attr("src")}})});
