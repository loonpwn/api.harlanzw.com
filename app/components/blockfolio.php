<?php
namespace App;

use Blockfolio\API;
use League\Csv\Writer;

add_action('init', function() {

    if (empty($_GET['action']) || $_GET['action'] !== 'blockfolio-export') {
        return;
    }

    $token = $_GET['blockfolio-token'];

    $api = new API([
        'BLOCKFOLIO_API_KEY' => $token
    ]);

    $export = remember(substr($token, 0, 20), function() use ($api) {
        return $api->get_all_positions();
    });

    global $blockfolio_export;

    $blockfolio_export = $export;

    $blockfolio_export->portfolio->btcValue =  round($blockfolio_export->portfolio->btcValue, 4);
    $blockfolio_export->portfolio->usdValue =  round($blockfolio_export->portfolio->usdValue, 2);
    $blockfolio_export->portfolio->ethValue =  round($blockfolio_export->portfolio->ethValue, 4);

    $blockfolio_export->positionList = collect($blockfolio_export->positionList)
        ->filter(function($coin) {
            return $coin->quantity > 0;
        })
        ->sort(function($a, $b) {
            return ceil($a->holdingValueBtc - $b->holdingValueBtc);
        })
        ->map(function($coin) {
            $token_id =  str_replace(' ', '-', strtolower($coin->fullName));

            switch ($token_id) {
                case 'lumen':
                    $token_id = 'stellar';
                    break;
                case 'int':
                    $token_id = 'internet-node-token';
                    break;
                case 'agi':
                    $token_id = 'singularitynet';
                    break;
                case 'polymath':
                    $token_id = 'polymath-network';
                    break;
            }
            $coin->cmc_token_id = $token_id;
            $cmc = remember('cmc- ' . $token_id, function() use ($token_id) {
                return file_get_contents('https://api.coinmarketcap.com/v1/ticker/' .$token_id);
            });
            if (empty($cmc)) {
                $coin->rank = 'n/a';
                return $coin;
            }

            $cmc = json_decode($cmc);

            $coin->rank = $cmc[0]->rank;

            $coin->holdingValueBtc = round($coin->holdingValueBtc, 4);

            return $coin;
        })
        ->toArray();

});

add_action('init', function() {

    if (empty($_GET['action']) || $_GET['action'] !== 'blockfolio-export-csv') {
        return;
    }

    $token = $_GET['blockfolio-token'];

    \App\blockfolio()->setKey($token);

    $positions = remember(substr($token, 0, 20), function() {
        return \App\blockfolio()->get_all_positions();
    });


    $header = ['coin', 'quantity', 'btc price', 'usd price', 'time', 'exchange'];

//load the CSV document from a string
    $csv = Writer::createFromString('');

////insert the header
    $csv->insertOne($header);

    foreach ($positions->positionList as $position) {
        $ticketPosition = \App\blockfolio()->get_positions_v2($position->base . '-' . $position->coin);

        foreach ($ticketPosition->positionList as $event) {
            if ($event->quantity > 0) {
                $csv->insertOne([
                    $position->coin,
                    $event->quantity,
                    $event->price,
                    $event->fiatPrice,
                    $event->date,
                    $event->exchange
                ]);
            }
        }
    }

    $csv->output('Blockfolio-Export.csv');
    die;
});
