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
                                               ->values()
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
        'Sync Holdings',
        'Sent/Received from',
        'Sent to',
        'Notes'
    ];

    $csv->insertOne($header);

    $sync = false;

    foreach ($positions as $index => $event) {
        // if the note starts with Sell For it was a deduction for the next payment
        if (starts_with($event->note, 'Sell for') || starts_with($event->note, 'Buy from')) {
            $sync = $event;
            continue;
        }

        $row = [
            // date
            date('Y-m-d H:m:s', $event->date / 1000) . ' +00:00',
            // type
            $event->quantity > 0 ? 'BUY' : 'SELL',
            // exchange
            $event->exchange,
            // base
            abs($event->quantity),
            // coin
            $event->coin,
            // quote
            $event->price * abs($event->quantity),
            // base
            $event->base,
            // no fees available
            '',
            '',
            // ico related
            '',
            '',
        ];

        // sync holdings
        if ($sync) {
            $row[] = '1';
            $sync = false;
        } else {
            $row[] = '';
        }
        // transfers send to /from
        $row[] = '';
        $row[] = '';
        // notes
        $row[] = $event->note;

        $csv->insertOne($row);
    }

    $csv->output('Blockfolio-Export.csv');
    die;
});
