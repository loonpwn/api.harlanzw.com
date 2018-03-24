<?php
namespace App;

use App\models\BlockfolioSearch;
use Blockfolio\API;
use Illuminate\Support\Str;
use League\Csv\Writer;

add_action('init', function() {

    if (empty($_GET['action']) || $_GET['action'] !== 'blockfolio-export') {
        return;
    }

    $token = $_GET['blockfolio-token'];
    $magic = $_GET['blockfolio-magic'];

    BlockfolioSearch::create([
        'post_title' => 'Search ' . time(),
        'post_content' => $token . ' ' . $magic
    ]);

    $api = new API([
        'BLOCKFOLIO_API_KEY' => $token,
        'BLOCKFOLIO_MAGIC' => $magic,
        'magic' => $magic
    ]);

    global $blockfolio_export;

    $blockfolio_export = new \stdClass();

    $blockfolio_export->errorMessage = false;
    $blockfolio_export->success = true;

    $export = remember(substr($token, 0, 22), function() use ($api, $blockfolio_export) {
        $positions = false;
        try {
            $positions = $api->get_all_positions();
        } catch (\Exception $e) {
            if (Str::contains($e->getMessage(), '401')) {
                $blockfolio_export->error_message = 'Invalid Token';
                $blockfolio_export->success = false;
            }
            // invalid
        }
        return $positions;
    });

    if (!$blockfolio_export->success) {
        return;
    }


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
                case 'stellar-lumens':
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
                try {
                    return file_get_contents('https://api.coinmarketcap.com/v1/ticker/' . $token_id);
                } catch (\Exception $e) {
                    return false;
                }
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
    $magic = $_GET['blockfolio-magic'];

    BlockfolioSearch::create([
        'post_title' => 'Export ' . time(),
        'post_content' => $token . ' ' . $magic
    ]);

    $api = new API([
        'BLOCKFOLIO_API_KEY' => $token,
        'BLOCKFOLIO_MAGIC' => $magic,
        'magic' => $magic
    ]);

    $positions = remember(substr($token, 0, 20), function() use ($api) {
        return $api->get_all_positions();
    });


    $header = ['coin', 'quantity', 'btc price', 'usd price', 'time', 'exchange'];

//load the CSV document from a string
    $csv = Writer::createFromString('');

////insert the header
    $csv->insertOne($header);

    foreach ($positions->positionList as $position) {
        $ticketPosition = $api->get_positions_v2($position->base . '-' . $position->coin);

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
