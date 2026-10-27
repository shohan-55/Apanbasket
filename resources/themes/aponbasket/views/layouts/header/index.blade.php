{!! view_render_event('bagisto.shop.layout.header.before') !!}

@include('aponbasket.announcement')

<div class="ab-header-wrap" data-ab-header>
    <header class="ab-header">
        <div class="ab-header-desktop">
            @include('aponbasket.desktop-nav')
        </div>

        <div class="ab-header-mobile">
            @include('aponbasket.mobile-nav')
        </div>
    </header>
</div>

{!! view_render_event('bagisto.shop.layout.header.after') !!}
