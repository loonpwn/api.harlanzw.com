<div class="jumbotron jumbotron-fluid mb-xl px-lg-md text-white doc-jumbotron lazyload"
     data-bg="{!! wp_get_attachment_image_src($background_image, 'full')[0] !!}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-6">
                @if(!empty($title))
                    <h1 class="typography-display-4">{{ $title }}</h1>
                @endif
                @if(!empty($sub_title))
                    <p class="font-weight-light typography-title">
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
    </div>
</div>
