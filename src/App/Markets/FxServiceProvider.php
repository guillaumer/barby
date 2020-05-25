<?php

namespace App\Markets;

use GuzzleHttp\Exception\RequestException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

/**
 */
class FxServiceProvider implements ServiceProviderInterface
{

    private $app;
    private $defaultEurJpyRate = 135.2;

    public function register(Container $app)
    {
        $this->app = $app;
        $app['fx'] = function ($app) {
            return $this;
        };
    }

    public function boot(Container $app)
    {

    }

    public function getFxRate($cfrom, $cto, $date = null)
    {
        if (!$date) {
            $date = time();
        }
        $eurJpy = $this->app['rates']->findFxSince($cfrom, $cto, $date);

        if ($eurJpy) {
            return $eurJpy['last'];
        }

        return $this->defaultEurJpyRate;
    }

}
