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
        @if(empty($blockfolio_export))
            <form method="get">

                <div class="form-group">
                    <label class="form-control-label" for="plugin-url">Blockfolio Token</label>
                    <input type="text" class="form-control" name="blockfolio-token" id="blockfolio-token" aria-describedby="plugin-url-help" placeholder="Enter Token">
                    <small id="plugin-url-help" class="form-text text-muted">You can get this from the app by going to the Settings -> Token.</small>
                </div>

                {{--<div class="form-group">--}}
                    {{--<label class="form-control-label" for="fiat-currency">Fiat Currency</label>--}}
                    {{--<select class="form-control select2" name="fiat" id="fiat-currency" aria-describedby="fiat-help" >--}}
                        {{--@foreach($currencies->currencyList as $currency)--}}
                            {{--<option value="{{ $currency->currency }}"--}}
                                    {{--@if($currency->currency === (isset($_GET['fiat']) ? $_GET['fiat'] : 'USD'))--}}
                                    {{--selected="selected"--}}
                                {{--@endif--}}
                            {{-->{{ $currency->fullName }} - {{ $currency->currency }}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                    {{--<small id="fiat-help" class="form-text text-muted">Which currency to display the export in.</small>--}}
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
                    <a class="nav-link" href="#trades" role="tab" data-toggle="tab">Trades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#graphs" role="tab" data-toggle="tab">Graphs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#share" role="tab" data-toggle="tab">Share</a>
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
                        <h4>Coming Soon!</h4>
                        <p>Let us know if this feature is important to you below.</p>
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
