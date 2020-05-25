<?php

namespace App\Markets;

use App\Command\getFxCommand;
use Knp\Console\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use App\Command\getMarketsCommand;

/**
 * Provider pour gestion utilisateurs
 */
class ConsoleServiceProvider implements ServiceProviderInterface
{

    public function register(Container $container)
    {
        if (isset($container['console'])) {
            $container->extend('console', function (Application $console) {
                $app = $console->getSilexApplication();
                $console->add(new getMarketsCommand('import:markets', $app));
                $console->add(new getFxCommand('import:fx', $app));

                return $console;
            });
        }
    }

    public function boot(Container $app)
    {

    }

}
