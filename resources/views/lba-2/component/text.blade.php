<?php
    $issetText = false;
    $alignment = '';

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
            $alignment = $block3['data']['alignment'];
        }else {
            $text = '';
        }
    }
?>

@if($issetText)
    <p class="space-mt--10" style="text-align: {{ $alignment }}">{!! $text !!}</p>
@endif
