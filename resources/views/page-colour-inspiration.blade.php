@extends('layouts.app')

@section('content')

    @php
        do_action('acf-widget/render', ['include' => ['wysiwyg', 'call-to-action']]);
    @endphp

    <section class="section">

        <div class="container">

            <header>
                <h1>Color Inspiration</h1>
                <small>Source: <a href="https://codepen.io/devi8/pen/lvIeh">https://codepen.io/devi8/pen/lvIeh</a></small>
            </header>

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left grapefruit-light"></section>
                <section class="color-right grapefruit-dark"></section>
                <h1>Grapefruit</h1>
                <p>#ED5565 #DA4453</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left bittersweet-light"></section>
                <section class="color-right bittersweet-dark"></section>
                <h1>Bittersweet</h1>
                <p>#FC6E51 #E9573F</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left sunflower-light"></section>
                <section class="color-right sunflower-dark"></section>
                <h1>Sunflower</h1>
                <p>#FFCE54 #F6BB42</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left grass-light"></section>
                <section class="color-right grass-dark"></section>
                <h1>Grass</h1>
                <p>#A0D468 #8CC152</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left mint-light"></section>
                <section class="color-right mint-dark"></section>
                <h1>Mint</h1>
                <p>#48CFAD #37BC9B</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left aqua-light"></section>
                <section class="color-right aqua-dark"></section>
                <h1>Aqua</h1>
                <p>#4FC1E9 #3BAFDA</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left blueJeans-light"></section>
                <section class="color-right blueJeans-dark"></section>
                <h1>Blue Jeans</h1>
                <p>#5D9CEC #4A89DC</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left lavander-light"></section>
                <section class="color-right lavander-dark"></section>
                <h1>Lavender</h1>
                <p>#AC92EC #967ADC</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left pinkRose-light"></section>
                <section class="color-right pinkRose-dark"></section>
                <h1>Pink Rose</h1>
                <p>#EC87C0 #D770AD</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left lightGray-light"></section>
                <section class="color-right lightGray-dark"></section>
                <h1>Light Gray</h1>
                <p>#F5F7FA #E6E9ED</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left mediumGray-light"></section>
                <section class="color-right mediumGray-dark"></section>
                <h1>Medium Gray</h1>
                <p>#CCD1D9 #AAB2BD</p>
            </section>
            <!-- Box -->

            <!-- Box -->
            <section class="colour-box">
                <section class="color-left darkGray-light"></section>
                <section class="color-right darkGray-dark"></section>
                <h1>Dark Gray</h1>
                <p>#656D78 #434A54</p>
            </section>
            <!-- Box -->
        </div>
    </section>

    @php
        do_action('acf-widget/render', ['exclude' => ['wysiwyg', 'call-to-action']]);
    @endphp
@endsection
