@extends('lba-2.master')

@section('content')

<?php
  $header = json_decode($data->template_header_json, true);
  $headerBlock = $header['blocks'];
?>
    @foreach ($headerBlock as $block)

    <!-- start header component -->
        @if ($block['type'] == 'carousel')
            <!-- BANNER SLIDER -->
            <div class="main-slider" data-indicators="true">
            <div class="carousel carousel-slider " data-indicators="true">
                @foreach ($block['data']['items'] as $item)
                    <a class="carousel-item"><img src="{{ $item['url'] }}" alt="slider"></a>
                @endforeach
            </div>
            </div>
            <!-- END BANNER SLIDER -->
        @else
            <!-- OTHER ADDITIONAL COMPONENT IN HEADER -->
            <div class="section promo">
                <div class="container">
                    <div class="col s12">
                    
                        <!-- Text Component -->
                        @include('lba-2.component.text')
                    </div>
                </div>
            </div>
        @endif

        <!-- END OTHER ADDITIONAL COMPONENT IN HEADER -->
    <!-- END HEADER -->
    @endforeach


<?php
  $body = json_decode($data->template_body_json, true);
  $bodyBlock = $body['blocks'];
?>

    @foreach ($bodyBlock as $block2)
    <!-- BODY COMPONENT -->
    
        @if ($block2['type'] == 'product')
        <!-- LIST PRODUCT -->
        <div class="section product-item si-featured">
            <div class="container">
                <div class="row slick-product">
                    <div class="col s12">
                        <div id="featured-product" class="featured-product">
                        
                            <!-- Product item -->
                            @foreach ($product as $stock)
                            <div>
                                <div class="col-slick-product">
                                    <div class="box-product">
                                        <div class="bp-top">
                                            <div class="product-list-img">
                                                <div class="pli-one">
                                                <div class="pli-two">
                                                    <img src="{{ env('BASE_URL_DASHBOARD').'/assets/product/images/'.$stock->photo }}" alt="img">
                                                </div>
                                                </div>
                                            </div>
                                            <h5>
                                                @if(!$is_preview)
                                                    <a href="{{ route('product::show', ['brand' => Str::slug($data->brand), 'campaign' => $data->slug, 'productId' => $stock->id]) }}">{{$stock->name}}
                                                @else
                                                    <a href="#">{{$stock->name}}
                                                @endif
                                                    </a>
                                            </h5>
                                            <div style="color: green" class="item-info">
                                                @if(strtolower($stock->type) == 'free')
                                                    Gratis
                                                @else
                                                    Tawaran menarik
                                                @endif
                                            </div>
                                            <div class="stock-item"></div>
                                        </div>
                                        <div class="bp-bottom">
                                            @if(!$is_preview)
                                                <a href="{{ route('product::show', ['brand' => Str::slug($data->brand), 'campaign' => $data->slug, 'productId' => $stock->id]) }}"><button class="btn button-add-cart">Ambil</button></a>
                                            @else
                                                <a href="#"><button class="btn button-add-cart">Ambil</button></a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <!-- End Product item-->
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END LIST PRODUCT -->
        @else
        <!-- OTHER ADDITIONAL IN FOOTER COMPONENT -->
        <div class="section promo">
            <div class="container">
                <div class="col s12">
                
                    <!-- Image Component -->
                    @include('lba-2.component.image')
                    
                    <!-- Text Component -->
                    @include('lba-2.component.text')
                    
                    <!-- Video component -->
                    @include('lba-2.component.embed')
                
                </div>
            </div>
        </div>
        @endif

    <!-- END BODY COMPONENT -->
    @endforeach

<?php
  $footer = json_decode($data->template_footer_json, true);
  $footerBlock = $footer['blocks'];
?>
    <!-- FOOTER COMPONENT  -->
        <footer id="footer" style="background-color: {{$data->template_primary_color}}"> <!-- use bg color with primary color -->
        <div class="footer-info">
        <div class="container">
            <div class="col s12 center">

            @foreach ($footerBlock as $block3)

                <!-- Text Component -->
                @include('lba-2.component.text')
                
                <!-- Bio Link component -->
                @include('lba-2.component.biolink')
                
            @endforeach
            
            </div>
        </div>
        </div>
    