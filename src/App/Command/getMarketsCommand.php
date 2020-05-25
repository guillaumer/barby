<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Pimple\Container;

class getMarketsCommand extends Command
{

    private $app;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'import:markets', $app)
    {
        $this->app = $app;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Import markets data in DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now            = time();
        $bitflyerBTCJPY = $this->app['market']->getPair('bitflyer', 'btcjpy');
        if ($bitflyerBTCJPY) {
            $this->app['rates']->insert(array(
                'market'  => 'bitflyer',
                'cfrom'   => 'btc',
                'cto'     => 'jpy',
                'last'    => $bitflyerBTCJPY->result->price,
                'created' => $now
            ));
        }

        $krakenBTCEUR = $this->app['market']->getPair('kraken', 'btceur');
        if ($krakenBTCEUR) {
            $this->app['rates']->insert(array(
                'market'  => 'kraken',
                'cfrom'   => 'btc',
                'cto'     => 'eur',
                'last'    => $krakenBTCEUR->result->price,
                'created' => $now
            ));
        }

        $bitflyerETHBTC = $this->app['market']->getPair('bitflyer', 'ethbtc');
        if ($bitflyerETHBTC) {
            $this->app['rates']->insert(array(
                'market'  => 'bitflyer',
                'cfrom'   => 'eth',
                'cto'     => 'btc',
                'last'    => $bitflyerETHBTC->result->price,
                'created' => $now
            ));
        }

        $krakenETHEUR = $this->app['market']->getPair('kraken', 'etheur');
        if ($krakenETHEUR) {
            $this->app['rates']->insert(array(
                'market'  => 'kraken',
                'cfrom'   => 'eth',
                'cto'     => 'eur',
                'last'    => $krakenETHEUR->result->price,
                'created' => $now
            ));
        }

        $bitflyerBCHBTC = $this->app['market']->getPair('bitflyer', 'bchbtc');
        if ($bitflyerBCHBTC) {
            $this->app['rates']->insert(array(
                'market'  => 'bitflyer',
                'cfrom'   => 'bch',
                'cto'     => 'btc',
                'last'    => $bitflyerBCHBTC->result->price,
                'created' => $now
            ));
        }

        $krakenBCHEUR = $this->app['market']->getPair('kraken', 'bcheur');
        if ($krakenBCHEUR) {
            $this->app['rates']->insert(array(
                'market'  => 'kraken',
                'cfrom'   => 'bch',
                'cto'     => 'eur',
                'last'    => $krakenBCHEUR->result->price,
                'created' => $now
            ));
        }



        die;
    }

}
