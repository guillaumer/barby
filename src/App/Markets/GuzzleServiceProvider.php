<?php

namespace App\Markets;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use GuzzleHttp\Client;

/**
 * Provider pour gestion utilisateurs
 */
class GuzzleServiceProvider implements ServiceProviderInterface
{

    /**
     * @var array
     */
    private $configuration = array();

    public function register(Container $app)
    {
        $app['guzzle'] = function($app) {
            $this->setConfiguration($app);
            return new Client($this->configuration);
        };
    }

    public function boot(Container $app)
    {
    }

    /** method to catch configuration params throw by $app['guzzle.*]
     * @param $app
     */
    protected function setConfiguration(Container $app)
    {

        if (isset($app['guzzle.base_uri'])) {
            $this->configuration['base_uri'] = $app['guzzle.base_uri'];
        }
        if (isset($app['guzzle.timeout'])) {
            $this->configuration['timeout'] = $app['guzzle.timeout'];
        }
        if (isset($app['guzzle.verify'])) {
            $this->configuration['verify'] = $app['guzzle.verify'];
        }
        if (isset($app['guzzle.debug'])) {
            $this->configuration['debug'] = $app['guzzle.debug'];
        }
        if (isset($app['guzzle.request_options']) && is_array($app['guzzle.request_options'])) {
            foreach ($app['guzzle.request_options'] as $valueName => $value) {
                $this->configuration[$valueName] = $value;
            }
        }
    }
}
