<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Pimple\Container;
use GuzzleHttp\Exception\RequestException;

class getFxCommand extends Command
{

    private $app;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'import:fx', $app)
    {
        $this->app = $app;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Import fx data in DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = time();
        $url = 'https://currency13.p.rapidapi.com/convert/1/EUR/JPY';
        try {
            $guzzle  = $this->app['guzzle'];
            $request = $guzzle->get($url, [
                'headers' => [
                    'x-rapidapi-host' => 'currency13.p.rapidapi.com',
                    'x-rapidapi-key'  => $this->app['rapidApiKey']
                ]
            ]);
            if ($request->getStatusCode() != 200) {
                return false;
            }
            $body = $request->getBody()->getContents();
            $body = \GuzzleHttp\json_decode($body);

            $this->app['rates']->insert(array(
                'market'  => 'fx',
                'cfrom'   => 'eur',
                'cto'     => 'jpy',
                'last'    => round($body->amount, 2),
                'created' => $now
            ));

            return $body;
        } catch (RequestException $e) {
        }

        die;
    }

}
