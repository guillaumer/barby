<?php

namespace App;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

class RepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        if (!isset($app['repository.repositories'])) {
            return;
        }

        foreach ($app['repository.repositories'] as $label => $class) {

            $app[$label] = function () use ($app, $class) {
                return new $class($app['db']);
            };
        }
    }

    public function boot(Application $app)
    {
    }
}
