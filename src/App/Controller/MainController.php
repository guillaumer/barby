<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Application as SymfonyApp;
use Symfony\Component\HttpFoundation\Response;

class MainController
{

    /**
     * @var Application
     */
    private $app;

    /**
     * MainController constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->setGlobalUser();
    }

    /**
     * Set user as global app variable
     */
    private function setGlobalUser()
    {
        $app   = $this->app;
        $token = $app['security.token_storage']->getToken();
        if ($token) {
            //            $users = $app['users']->findBy('username', $token->getUsername(), 1);
            //            if ($users) {
            //                $user        = $users[0];
            //                $app['user'] = $user;
            //            }
        }
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $app = $this->app;
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app->redirect($app['url_generator']->generate('dashboard'));
        }

        $form = $app['form.factory']->create('login', $request->request->all(), array(
            'app' => $app
        ));

        return $app['twig']->render('main/login.html.twig', array(
            'form'  => $form->createView(),
            'error' => $app['security.last_error']($request)
        ));
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function dashboardAction(Request $request)
    {
        $app = $this->app;

        $since = new \DateTime();
        $since->modify("-1 hour");
        $since = $since->getTimestamp();

        $krakenBtcEur   = $this->app['rates']->findSince('kraken', 'btc', 'eur', $since);
        $bitflyerBtcJpy = $this->app['rates']->findSince('bitflyer', 'btc', 'jpy', $since);

        $krakenEthEur   = $this->app['rates']->findSince('kraken', 'eth', 'eur', $since);
        $bitflyerEthBtc = $this->app['rates']->findSince('bitflyer', 'eth', 'btc', $since);

//        $krakenLtcEur   = $this->app['rates']->findSince('kraken', 'ltc', 'eur', $since);
//        $bitflyerLtcBtc = $this->app['rates']->findSince('bitflyer', 'ltc', 'btc', $since);

        $krakenBchEur   = $this->app['rates']->findSince('kraken', 'bch', 'eur', $since);
        $bitflyerBchBtc = $this->app['rates']->findSince('bitflyer', 'bch', 'btc', $since);

        $bitflyerEthJpy = array();
        foreach ($bitflyerEthBtc as $k => $ethbtc) {
            foreach ($bitflyerBtcJpy as $btcjpy) {
                if ($btcjpy['created'] == $ethbtc['created']) {
                    $bitflyerEthJpy[] = array(
                        'last'    => $this->app['market']->convertBitflyerEthJpyFromBtc($btcjpy['last'], $ethbtc['last']),
                        'created' => $ethbtc['created']
                    );
                    break;
                }
            }
        }

        $bitflyerBchJpy = array();
        foreach ($bitflyerBchBtc as $k => $bchbtc) {
            foreach ($bitflyerBtcJpy as $btcjpy) {
                if ($btcjpy['created'] == $bchbtc['created']) {
                    $bitflyerBchJpy[] = array(
                        'last'    => $this->app['market']->convertBitflyerBchJpyFromBtc($btcjpy['last'], $bchbtc['last']),
                        'created' => $bchbtc['created']
                    );
                    break;
                }
            }
        }

        foreach ($bitflyerBtcJpy as $k => $btcJpy) {
            $bitflyerBtcJpy[$k]['last'] = $this->app['market']->convertBitflyerBtc($btcJpy['last']);
        }

        $dataSet1 = $this->buildGraphData($krakenBtcEur, $bitflyerBtcJpy, 'btc');
        $dataSet2 = $this->buildGraphData($krakenEthEur, $bitflyerEthJpy, 'eth');
        $dataSet3 = $this->buildGraphData($krakenBchEur, $bitflyerBchJpy, 'ch');

        return $app['twig']->render('main/dashboard.html.twig', array(
            'dataSet1'        => $dataSet1,
            'dataset1lastGap' => $this->calculateLastGap($dataSet1),
            'dataSet2'        => $dataSet2,
            'dataset2lastGap' => $this->calculateLastGap($dataSet2),
            'dataSet3'        => $dataSet3,
            'dataset3lastGap' => $this->calculateLastGap($dataSet3)
        ));
    }

    private function calculateLastGap($dataSet)
    {
        $lastData = end($dataSet);

        if ($lastData['buy'] == 0 || $lastData['buy'] == 0) {
            return 0;
        }
        return round(($lastData['sell'] - $lastData['buy']) * 100 / $lastData['buy'], 2);
    }

    private function buildGraphData($buyDataSet1, $sellDataSet2, $currency)
    {
        $data = array();
        foreach ($buyDataSet1 as $k => $buy) {
            $created               = $buy['created'] * 1000 + 32400;
            $buyLast               = floatval($buy['last']);
            $data[$buy['created']] = [
                'created' => $created,
                'buy'     => $buyLast
            ];

            foreach ($sellDataSet2 as $i => $sell) {
                if ($buy['created'] == $sell['created']) {
                    $sellConverted                    = $this->app['market']->convertCurrency('jpy', 'eur', $sell['last'], $sell['created']);
                    $data[$buy['created']]['sell']    = $sellConverted;
                    $data[$buy['created']]['average'] = round((($sellConverted + $buyLast) / 2), 0);
                    continue;
                }
            }
        }

        //Cleanup
        foreach ($data as $k => $val) {
            if (!isset($val['buy']) || !isset($val['sell'])) {
                unset($data[$k]);
            }
        }

        return $data;
    }

    public function cronMarketAction()
    {
        $app     = $this->app;
        $console = $app['console'];
        $output  = new NullOutput();
        $input   = new ArrayInput(array(
            'command' => 'import:markets'
        ));

        try {
            $console->run($input, $output);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return new Response('');
    }

    public function cronFxAction()
    {
        $app     = $this->app;
        $console = $app['console'];
        $output  = new NullOutput();
        $input   = new ArrayInput(array(
            'command' => 'import:fx'
        ));

        try {
            $console->run($input, $output);
        } catch (\Exception $e) {
        }

        return new Response('');
    }

}
