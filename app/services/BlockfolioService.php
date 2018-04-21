<?php
namespace App\services;

use Blockfolio\API;
use Illuminate\Support\Str;

class BlockfolioService {

    const CACHE_TIME = 5 * 60;

    private $token;
    public $api;

    /**
     * BlockfolioService constructor.
     */
    public function __construct($token) {
        $this->token = $token;

        $this->api = new API([
            'BLOCKFOLIO_API_KEY' => $token,
        ]);
    }

    public function get_token_cache_key() {
        return substr($this->token, 0, 22);
    }

    public function get_position_cache_key($position) {
        return $this->get_token_cache_key() . $position->base . '-' . $position->coin;
    }

    public function get_all_meta() {
        $service = $this;
        $export = \App\remember($this->token, function() use ($service) {
            $positions = false;
            try {
                $positions = $service->api->get_all_positions();
            } catch (\Exception $e) {
                if (Str::contains($e->getMessage(), '401')) {
                    return false;
                }
                // invalid
            }
            return $positions;
        }, self::CACHE_TIME);

        // failed
        if (empty($export)) {
            return false;
        }

        $export->allPositions = [];
        $export->watching = [];
        foreach ($export->positionList as $position) {
            $ticketPosition = \App\remember($this->get_position_cache_key($position), function () use ($service, $position) {
                return $service->api->get_positions_v2($position->base . '-' . $position->coin);
            }, self::CACHE_TIME);
            $export->allPositions[$position->coin] = $ticketPosition;

            if ($position->watchOnly) {
                $export->watching[$position->coin] = $position;
            }
        }

        $export->portfolio->btcValue =  round($export->portfolio->btcValue, 4);
        $export->portfolio->usdValue =  round($export->portfolio->usdValue, 2);
        $export->portfolio->ethValue =  round($export->portfolio->ethValue, 4);


        $export->watching = collect($export->watching)
            ->map(function($coin) {
                return $this->find_cmc_rank($coin);
            })->toArray();

        $export->positionList = collect($export->positionList)
            ->filter(function($coin) {
                return $coin->quantity > 0;
            })
            ->sort(function($a, $b) {
                return ceil($a->holdingValueBtc - $b->holdingValueBtc);
            })
            ->map(function($coin) {
                return $this->find_cmc_rank($coin);
            })->toArray();

        return $export;
    }

    public function find_cmc_rank($coin) {
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
        $cmc = \App\remember('cmc- ' . $token_id, function() use ($token_id) {
            try {
                return file_get_contents('https://api.coinmarketcap.com/v1/ticker/' . $token_id);
            } catch (\Exception $e) {
                return false;
            }
            // cache cmc for 24 hours
        }, 60 * 60 * 24);
        if (empty($cmc)) {
            $coin->rank = '(lookup failed)';
            return $coin;
        }

        $cmc = json_decode($cmc);

        $coin->rank = $cmc[0]->rank;

        $coin->holdingValueBtc = round($coin->holdingValueBtc, 4);

        return $coin;
    }
}
