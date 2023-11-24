<?php
            $issetText = false;
            
            if(isset($block['type'])) {
                $issetText = (($block['type'] == 'paragraph') ? true : false);
                if($issetText) {
                    $text = $block['data']['text'];
                }else {
                    $text = '';
                }
            }

            if(isset($block2['type'])) {
                $issetText = (($block2['type'] == 'paragraph') ? true : false);
                if($issetText) {
                    $text = $block2['data']['text'];
                }else {
                    $text = '';
                }
            }

            if(isset($block3['type'])) {
                $issetText = (($block3['type'] == 'paragraph') ? true : false);
                if($issetText) {
                    $text = $block3['data']['text'];
                }else {
                    $text = '';
                }
            }
            ?>            
            
            @if($issetText)
                <p class="space-mt--10">{!! $text !!}</p>
            @endif