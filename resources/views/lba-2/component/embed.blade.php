          @if($block2['type'] == 'youtubeEmbed')
          <?php
            // Parse the URL
            $urlParts = parse_url($block2['data']['url']);
            parse_str($urlParts['query'], $query);
            $videoId = $query['v'];
            ?>
            <iframe style="width:100%; aspect-ratio: 16/9;" class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $videoId }}"
                allowfullscreen></iframe>
          @endif