<div class="jumbotron jumbotron-fluid mb-xl px-lg-md text-white lazyload call-to-action"
     data-bgset="{!! wp_get_attachment_image_srcset($background_image, 'full') !!}">
    <div class="justify-content-center call-to-action__container container ">
        @if(!empty($title))
            <h1 class="typography-display-3">{{ $title }}</h1>
        @endif
        @if(!empty($sub_title))
            <div class="call-to-action__subtitle-wrap">
                <p class="font-weight-light typography-title call-to-action__subtitle">
                    {{ $sub_title }}
                </p>
            </div>
        @endif
        @if(!empty($buttons))
            @foreach($buttons as $button)
                @if($button['link_relation'] == 'regular')
                    <a href="{{ $button['link'] }}" class="btn btn-primary"
                       target="{{ $button['link_type'] == 'same_window' ? '_self' : '_blank' }}">{{ $button['button_text'] }}</a>
                @else
                    <a href="#widget-{{ $button['link_relation'] }}-1"
                       class="btn btn-default">{{ $button['button_text'] }}</a>
                @endif
            @endforeach
        @endif
    </div>
</div>
