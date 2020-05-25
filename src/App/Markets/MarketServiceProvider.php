<?php

namespace App\Markets;

use GuzzleHttp\Exception\RequestException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

/**
 */
class MarketServiceProvider implements ServiceProviderInterface
{

    private $app;
    private $bitFlyerBtcSellCommission = 0;
    private $bitFlyerEthSellCommission = 3;
    private $bitFlyerBchSellCommission = 3;

    public function register(Container $app)
    {
        $this->app     = $app;
        $app['market'] = function ($app) {
            return $this;
        };
    }

    public function boot(Container $app)
    {

    }

    public function getPair($market, $pair)
    {
        $guzzle = $this->app['guzzle'];
        $url    = 'https://api.cryptowat.ch/markets/' . $market . '/' . $pair . '/price';
        try {
            $request = $guzzle->get($url);
            if ($request->getStatusCode() != 200) {
                return false;
            }
            $body = $request->getBody()->getContents();
            $body = \GuzzleHttp\json_decode($body);

            return $body;
        } catch (RequestException $e) {
        }

        return false;
    }

    public function convertCurrency($from, $to, $nb, $time = false)
    {
        $eurJpyRate = $this->app['fx']->getFxRate('eur', 'jpy', $time);

        if (($from == 'eur') && ($to == 'jpy')) {
            return round($nb * $eurJpyRate, 2);
        }

        if (($from == 'jpy') && ($to == 'eur')) {
            return round($nb / $eurJpyRate, 2);
        }

        return false;
    }

    public function convertBitflyerBtc($amount)
    {
        $amount = (float)$amount;
        return ($amount - ($amount * $this->bitFlyerBtcSellCommission / 100));
    }

    public function convertBitflyerEthJpyFromBtc($ethBtc, $btcJpy)
    {
        $tot = $ethBtc * $btcJpy;

        return ($tot - ($tot * $this->bitFlyerEthSellCommission / 100));
    }

    public function convertBitflyerBchJpyFromBtc($bchBtc, $btcJpy)
    {
        $tot = $bchBtc * $btcJpy;

        return ($tot - ($tot * $this->bitFlyerBchSellCommission / 100));
    }

}
