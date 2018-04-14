<?php
namespace App;

use App\models\BlockfolioSearch;
use App\services\BlockfolioService;
use Blockfolio\API;
use Google_Service_Sheets;
use Illuminate\Support\Str;
use League\Csv\Writer;

add_action('sage/template/export-blockfolio/data', function($data) {

    if (empty($_GET['action']) || $_GET['action'] !== 'blockfolio-export') {
        return $data;
    }

    $token = $_GET['blockfolio-token'];

    $service = new BlockfolioService($token);

    $export = $service->get_all_meta();


    if (empty($export)) {
        $export = new \stdClass();
        $export->error_message = 'Invalid Token';
        $export->success = false;
        BlockfolioSearch::create([
            'post_title' => 'Invalid Search: ' . $token,
        ]);
    } else {
        BlockfolioSearch::create([
            'post_title' => 'Valid Search: ' . $token,
        ]);
    }
    $data['export'] = $export;
    return $data;
});


add_action('init', function() {

    if (empty($_GET['action']) || $_GET['action'] !== 'blockfolio-portfolio-export') {
        return;
    }

    $token = $_GET['blockfolio-token'];


    BlockfolioSearch::create([
        'post_title' => 'Export Portfolio ' . time(),
        'post_content' => $token
    ]);

    $service = new BlockfolioService($token);

    $export = $service->get_all_meta();

    //load the CSV document from a string
    $csv = Writer::createFromString('');

    $header = [
        'Coin',
        'Holdings',
        'Value Eth',
        'Value BTC',
        'Value Fiat',
    ];

    $csv->insertOne($header);

    foreach ($export->positionList as $coin) {
        $csv->insertOne([
            $coin->coin,
            $coin->quantity,
            $coin->holdingValueEth,
            $coin->holdingValueBtc,
            $coin->holdingValueFiat,
        ]);
    }

    $csv->output('Blockfolio-Export.csv');
    die;
});



add_action('init', function() {

    if (empty($_GET['action']) || $_GET['action'] !== 'blockfolio-trade-export') {
        return;
    }

    $token = $_GET['blockfolio-token'];

    //888041861504-48bvq4p46f9n59bh2u3nm6q9ghckog27.apps.googleusercontent.com
    //59oUwB1-qbgG9gGvS9tH-qXT

    BlockfolioSearch::create([
        'post_title' => 'Export Trades ' . time(),
        'post_content' => $token
    ]);

    $service = new BlockfolioService($token);

    $export = $service->get_all_meta();
    $positions = collect($export->allPositions)->map(function($position) {
        return $position->positionList;
    })
                                               ->flatten()
                                               ->filter(function($position) {
                                                   return $position->quantity != 0;
                                               })
                                               ->sort(function($a, $b) {
                                                   return $b->date - $a->date;
                                               })
                                               ->toArray();

    //load the CSV document from a string
    $csv = Writer::createFromString('');

    $header = [
        'Date',
        'Type',
        'Exchange',
        'Base amount',
        'Base currency',
        'Quote amount',
        'Quote currency',
        'Fee',
        'Fee currency',
        'Costs/Proceeds',
        'Costs/Proceeds currency',
        'Sent/Received from',
        'Sent to',
        'Notes'
    ];

    $csv->insertOne($header);

    foreach ($positions as $event) {
        $csv->insertOne([
            date('Y-m-d H:m:s', $event->date / 1000),
            $event->quantity > 0 ? 'BUY' : 'SELL',
            $event->exchange,
            // base
            $event->quantity,
            $event->coin,
            // quote
            $event->price * $event->quantity,
            $event->base,
            // no fees available
            '',
            '',
            // ico related
            '',
            '',
            // transfers send to /from
            '',
            '',
            $event->note
        ]);
    }

    $csv->output('Blockfolio-Export.csv');
    die;
});
