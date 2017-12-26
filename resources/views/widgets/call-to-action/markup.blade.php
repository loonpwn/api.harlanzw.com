<section class="hero-banner lazyload" data-bg="{!! wp_get_attachment_image_src($background_image, 'full')[0] !!}">
    <div class="hero-banner-inner">
        <div class="container">
            @if(!empty($title))
                <h1 class="title">{{ $title }}</h1>
            @endif
            @if(!empty($sub_title))
                <p class="sub-title">
                    {{ $sub_title }}
                </p>
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
</section>
