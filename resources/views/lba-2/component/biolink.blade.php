<?php
            $issetBioLink = false;
            
            if(isset($block['type'])) {
                $issetBioLink = (($block['type'] == 'socialMediaAccount') ? true : false);
                if($issetBioLink) {
                    $fbLink = $block['data']['facebook'];
                    $igLink = $block['data']['instagram'];
                    $xLink = $block['data']['twitterx'];
                    $tokLink = $block['data']['tiktok'];
                    $webLink = $block['data']['website'];
                }else {
                    $fbLink = '';
                    $igLink = '';
                    $xLink = '';
                    $tokLink = '';
                    $webLink = '';
                }
            }

            if(isset($block3['type'])) {
                $issetBioLink = (($block3['type'] == 'socialMediaAccount') ? true : false);
                if($issetBioLink) {
                    $fbLink = $block3['data']['facebook'];
                    $igLink = $block3['data']['instagram'];
                    $xLink = $block3['data']['twitterx'];
                    $tokLink = $block3['data']['tiktok'];
                    $webLink = $block3['data']['website'];
                }else {
                    $fbLink = '';
                    $igLink = '';
                    $xLink = '';
                    $tokLink = '';
                    $webLink = '';
                }
            }
            ?>
            
            @if ($issetBioLink)
            <div class="row row-footer-icon">
                <div class="col s12">
                <div class="footer-sosmed-icon ">

                    @if($igLink != '')
                        <div class="wrap-circle-sosmed ">
                        <a href="{{ $igLink }}">
                        <div class="circle-sosmed">
                            <i class="fab fa-instagram"></i>
                        </div>
                        </a>
                        </div>
                    @endif

                    @if($tokLink != '')
                        <div class="wrap-circle-sosmed ">
                        <a href="{{ $tokLink }}">
                        <div class="circle-sosmed">
                            <i class="fab fa-linkedin-in"></i>
                        </div>
                        </a>
                        </div>
                    @endif

                    @if($xLink != '')
                        <div class="wrap-circle-sosmed ">
                        <a href="{{ $xLink }}">
                        <div class="circle-sosmed">
                            <i class="fab fa-twitter"></i>
                        </div>
                        </a>
                        </div>
                    @endif

                    @if($fbLink != '')
                        <div class="wrap-circle-sosmed ">
                        <a href="{{ $fbLink }}">
                        <div class="circle-sosmed">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        </a>
                        </div>
                    @endif

                    @if($webLink != '')
                        <div class="wrap-circle-sosmed ">
                        <a href="{{ $webLink }}">
                        <div class="circle-sosmed">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        </a>
                        </div>
                    @endif

                </div>
                </div>
            </div>
            @endif