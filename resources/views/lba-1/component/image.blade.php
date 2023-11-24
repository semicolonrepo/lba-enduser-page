        @if($block2['type'] == 'image')
            <div class="text-center embed-responsive embed-responsive-1by1 space-mt--20 space-mb--20">
              <img style="width:100%; height: auto;" src="{{ $block2['data']['file']['url'] }}"></img>
            </div>
          @endif