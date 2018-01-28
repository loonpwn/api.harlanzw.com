<?php

use App\models\WPASearch;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

if (empty($_GET['action']) || $_GET['action'] !== 'blockfolio-export') {
    return;
}

$token = $_GET['blockfolio-token'];

$export = cached_url_request('https://api-v0.blockfolio.com/rest/get_all_positions/' . $token);

$export = json_decode($export);

global $blockfolio_export;

$blockfolio_export = $export;

$blockfolio_export->positionList = collect($blockfolio_export->positionList)
    ->filter(function($coin) {
        return $coin->quantity > 0;
    })
    ->sort(function($a, $b) {
        return ceil($b->holdingValueBtc - $a->holdingValueBtc);
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
        }
        $cmc = cached_url_request('https://api.coinmarketcap.com/v1/ticker/' .$token_id);
        if (empty($cmc)) {
            $coin->rank = 'n/a';
            return $coin;
        }

//        $html = cached_url_request('https://coinmarketcap.com/currencies/' . $token_id . '/');
//
//        $dom = new Dom();
//        $dom = $dom->load($html);
//        /** @var Dom\Collection $a */
//        $a = $dom->find('#social');
//        dd($a);
//
//        foreach($a as $anchor) {
//            $anchor->getAttribute('href');
//        }
//
//        $social = $dom->find('#social');
//        if ($a->count() > 0) {
//            dd($a);
//        } else {
//            dd($social, $a, $dom, $coin);
//        }
//
//        $coin->twitter = $a;

        $cmc = json_decode($cmc);

        $coin->rank = $cmc[0]->rank;

        $potential_value = 2000000000;
        if ($coin->rank <= 30) {
            $potential_value = 10000000000;
        }

        $coin->moon_shot = $cmc[0]->market_cap_usd > 0 ? ceil($potential_value / $cmc[0]->market_cap_usd) : 'high';

        return $coin;
    })
    ->toArray();



function cached_url_request($url) {
    $cache_key = 'url-' . $url;
    if (($content = get_transient($cache_key)) !== false) {
        return $content;
    }
    $content = file_get_contents($url);
    // 10 minute cache
    set_transient($cache_key, $content, 60 * 10);
    return $content;
}
