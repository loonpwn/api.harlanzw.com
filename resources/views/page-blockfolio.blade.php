@extends('layouts.app')

@section('content')

    @php
        do_action('acf-widget/render', ['include' => ['wysiwyg', 'call-to-action']]);
    @endphp

    <div class="container">


        <?php
        global $blockfolio_export;
        ?>
        @if(empty($blockfolio_export))
            <form method="get">

                <div class="form-group">
                    <label class="form-control-label" for="plugin-url">Blockfolio Token</label>
                    <input type="text" class="form-control" name="blockfolio-token" id="blockfolio-token" aria-describedby="plugin-url-help" placeholder="Enter Token">
                    <small id="plugin-url-help" class="form-text text-muted">You can get this from the app by going to the settings page at the bottom.</small>
                </div>
                <input name="action" value="blockfolio-export" type="hidden">


                <button type="submit" class="btn btn-primary">Export</button>
            </form>
        @else

            <div class="text-center">
                <h3>Your Portfolio</h3>
                <p>Portfolio Value: <strong>{{ $blockfolio_export->portfolio->btcValue }}</strong> bitcoins</p>

                <button type="submit" class="btn btn-primary">Integrate</button>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Holdings</th>
                    <th>Value</th>
                    <th>Coin Market Cap Rank</th>
                    <th>Moon Shot (~2b - ~10b)</th>
                    <th>Twitter</th>
                </tr>
                </thead>
                <tbody>
                @foreach($blockfolio_export->positionList as $coin)
                    <tr>
                        <td><img src="{!! $coin->coinUrlDark !!}" height="40"> {{ $coin->fullName }} </td>
                        <td>{{ $coin->quantity }}</td>
                        <td>{{ $coin->holdingValueBtc }}</td>
                        <td>{{ $coin->rank }}</td>
                        <td>{{ $coin->moon_shot }}</td>
                        <td>{{ $coin->twitter }}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        @endif

    </div>

    @php
        do_action('acf-widget/render', ['exclude' => ['wysiwyg', 'call-to-action']]);
    @endphp
@endsection
