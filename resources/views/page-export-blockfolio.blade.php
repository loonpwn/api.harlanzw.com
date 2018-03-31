@extends('layouts.app')

@section('content')

    @php
        do_action('acf-widget/render', ['include' => ['wysiwyg', 'call-to-action']]);
    @endphp

    <div class="container">


        <?php
        global $blockfolio_export;
        global $wp;
        ?>
        @if(!empty($blockfolio_export) && !$blockfolio_export->success)

            <div class="alert alert-danger">
                <h3>Invalid Token/Magic Provided</h3>
                <p>You need to supply a valid blockfolio token/magic to export your data. </p>
                <img style="display: block; margin: 0 auto;" src="{{ \App\asset_path('images/blockfolio-token.png') }}" width="auto" height="400" alt="Export Blockfolio Token">
            </div>
        @endif

        @if(empty($blockfolio_export) || empty($blockfolio_export->portfolio))

            <form
                ga-on="submit,change"
                ga-event-category="Blockfolio"
                ga-event-action="Submit"
                method="get">

                <div class="form-group">
                    <label class="form-control-label" for="lockfolio-token">Blockfolio Token</label>
                    <input
                        ga-on="change"
                        ga-event-category="Blockfolio"
                        ga-event-action="Changed Token"
                        required
                        type="text" class="form-control" name="blockfolio-token" id="blockfolio-token" aria-describedby="plugin-url-help" placeholder="Enter Token">
                    <small id="plugin-url-help" class="form-text text-muted">You can get this from the app by going to the Settings -> Token.</small>
                </div>

                {{--<div class="form-group">--}}
                    {{--<label class="form-control-label" for="blockfolio-magic">Blockfolio Magic</label>--}}
                    {{--<input--}}
                        {{--ga-on="change"--}}
                        {{--ga-event-category="Blockfolio"--}}
                        {{--ga-event-action="Changed Magic"--}}
                        {{--required--}}
                        {{--type="text" class="form-control" name="blockfolio-magic" id="blockfolio-magic" aria-describedby="magic-help" placeholder="Enter Magic">--}}
                    {{--<small id="magic-help" class="form-text text-muted">This requires packet sniffing to detect the magic of your account.</small>--}}
                {{--</div>--}}

                <input name="action" value="blockfolio-export" type="hidden">

                <button type="submit" class="btn btn-primary">Export</button>
            </form>
        @else


            <div class="text-center">
                <h3>Portfolio Value</h3>
                <p><i class="fab fa-btc" style="margin-right: 3px"></i><strong>{{ $blockfolio_export->portfolio->btcValue }}</strong> </p>
                <p><i class="fab fa-ethereum" style="margin-right: 3px"></i><strong>{{ $blockfolio_export->portfolio->ethValue }}</strong> </p>
                <p><i class="fas fa-dollar-sign" style="margin-right: 3px"></i><strong>{{ $blockfolio_export->portfolio->usdValue }}</strong> </p>
            </div>

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#overview" role="tab" data-toggle="tab">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       ga-on="click"
                       ga-event-category="Blockfolio"
                       ga-event-action="Tab - Trades"
                       href="#trades" role="tab" data-toggle="tab">Trades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       ga-on="click"
                       ga-event-category="Blockfolio"
                       ga-event-action="Tab - Graphs"
                       href="#graphs" role="tab" data-toggle="tab">Graphs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       ga-on="click"
                       ga-event-category="Blockfolio"
                       ga-event-action="Tab - Share"
                       href="#share" role="tab" data-toggle="tab">Share</a>
                </li>
            </ul>


            <div class="tab-content">

                <div role="tabpanel" class="tab-pane fade active show" id="overview">

                    <div class="text-center" style="margin: 1em 0;">
                        <div class="btn-group">
                            <a target="_blank" href="{{ home_url($wp->request) . '/?blockfolio-token=' . $_GET['blockfolio-token'] . '&action=blockfolio-export-csv' }}" class="btn btn-primary">Export Trades CSV</a>
                        </div>
                    </div>

                    <table class="table datatable" data-searching="false" data-paging="false" data-info="false" data-order='[[2, "desc"]]'>
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Holdings</th>
                            <th>Value</th>
                            <th>Coin Market Cap Rank</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($blockfolio_export->positionList as $coin)
                            <tr>
                                <td><img src="{!! $coin->coinUrlDark !!}" height="40"> {{ $coin->fullName }} </td>
                                <td>{{ $coin->quantity }}</td>
                                <td><i class="fab fa-btc" style="margin-right: 3px"></i>{{ $coin->holdingValueBtc }}</td>
                                <td>
                                    <a href="https://coinmarketcap.com/currencies/{{ $coin->cmc_token_id }}" target="_blank">
                                        <strong>{{ $coin->rank }}</strong>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="trades">

                    <div class="text-center" style="margin: 1em 0;">
                        <div class="btn-group">
                            <a target="_blank" href="{{ home_url($wp->request) . '/?blockfolio-token=' . $_GET['blockfolio-token'] . '&action=blockfolio-export-csv' }}" class="btn btn-primary">Export Trades CSV</a>
                        </div>
                    </div>

                    <div class="text-center" style="margin: 1em 0;">

                        <table class="table datatable" data-searching="false" data-paging="false" data-info="false" data-order='[[2, "desc"]]'>
                            <thead>
                            <tr>

                                <th>Coin</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>USD Price</th>
                                <th>Time</th>
                                <th>Exchange</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($blockfolio_export->allPositions as $coin => $position)

                                @foreach($position->positionList as $event)
                                    <tr>
                                        <td>{{ $coin }}</td>
                                        <td>{{ $event->quantity }}</td>
                                        <td>{{ $event->priceString }}</td>
                                        <td>{{ $event->priceFiatString }}</td>
                                        <td>{{ date('d-m-y H:m', $event->date / 1000) }}</td>
                                        <td>{{ $event->exchange }}</td>
                                    </tr>
                                @endforeach

                            @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="graphs">

                    <div class="text-center" style="margin: 1em 0;">
                        <h4>Coming Soon!</h4>
                        <p>Let us know if this feature is important to you below.</p>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="share">

                    <div class="text-center" style="margin: 1em 0;">
                        <h4>Coming Soon!</h4>
                        <p>Let us know if this feature is important to you below.</p>
                    </div>
                </div>
            </div>

            <a href="{{ home_url($wp->request) }}" class="btn btn-primary">Back</a>

        @endif

    </div>

    @php
        do_action('acf-widget/render', ['exclude' => ['wysiwyg', 'call-to-action']]);
    @endphp
@endsection
