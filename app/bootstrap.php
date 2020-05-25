<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Provider\ConsoleServiceProvider;

$app = new Application();

$app['debug']       = $conf['debug'];
$app['locale']      = $conf['locale'];
$app['secret']      = $conf['secret'];
$app['rapidApiKey'] = $conf['rapidApiKey'];

$app['db.options'] = $conf['db.options'];

$app['swiftmailer.options'] = $conf['swiftmailer.options'];

// DOCTRINE
$app->register(new DoctrineServiceProvider());

// SESSION
$app->register(new Silex\Provider\SessionServiceProvider());

// ROUTES
$app->register(new Silex\Provider\RoutingServiceProvider());

// TRANSLATIONS
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array($app['locale']),
));

// REPOSITORIES
$app['repository.repositories'] = array(
    'rates' => 'App\Repository\Rate',
);
$app->register(new App\RepositoryServiceProvider());

// FORMS
$app->register(new FormServiceProvider());

$app->extend('form.extensions', function ($extensions) use ($app) {
    $extensions[] = new App\Form\FormExtension($app);

    return $extensions;
});

$app->register(new ValidatorServiceProvider());

// TWIG
$app->register(new TwigServiceProvider(), array(
    'twig.options' => array(
        'debug' => $app['debug'],
        'cache' => __DIR__ . '/cache'
    ),
    'twig.path'    => array(__DIR__ . '/../src/App/Views')
));

$app->extend('twig', function (Twig_Environment $twig, $app) {

    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
        return sprintf('/assets/%s', ltrim($asset, '/'));
    }));

    return $twig;
});

// MAILER
$app->register(new Silex\Provider\SwiftmailerServiceProvider());

// SECURITY
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\RememberMeServiceProvider());
$app['security.firewalls']    = array(
    'default'      => array(
        'pattern'     => '^.*$',
        'anonymous'   => true,
        'remember_me' => array(
            'key'                => 'L7h3m.~A+A~e-vG=',
            'always_remember_me' => true,
        ),
        'form'        => array(
            'login_path' => '/',
            'check_path' => 'login'
        ),
        'users'       => array(
            'admin' => array(
                'ROLE_ADMIN',
                '$2y$13$TuSQOJTjuTLh9lF1OksteeYMcel8gd3mZWZvhfyNETK4VKUT39nm6'
            )
        )
    ),
    'cron_markets' => array(
        'pattern'   => '^/cron_markets$',
        'anonymous' => true,
    ),
    'cron_fx'      => array(
        'pattern'   => '^/cron_fx$',
        'anonymous' => true,
    ),
);
$app['security.access_rules'] = array(
    array(
        '^/cron_markets$',
        'IS_AUTHENTICATED_ANONYMOUSLY'
    ),
    array(
        '^/cron_fx$',
        'IS_AUTHENTICATED_ANONYMOUSLY'
    ),
    array(
        '^/.+$',
        'ROLE_ADMIN'
    )
);

// ROUTES
$app['routes'] = $app->extend('routes', function (RouteCollection $routes, Application $app) {
    $loader     = new YamlFileLoader(new FileLocator(__DIR__));
    $collection = $loader->load('routes.yml');
    $routes->addCollection($collection);

    return $routes;
});

// Controller
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());

$app['main.controller'] = function () use ($app) {
    return new \App\Controller\MainController($app);
};

// Markets
$app->register(new \App\Markets\GuzzleServiceProvider(), array(
    'guzzle.verify' => false,
));
$app->register(new \App\Markets\MarketServiceProvider);
$app->register(new \App\Markets\FxServiceProvider);

//Console Service Provider
$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => 'ConsoleBarby',
    'console.version'           => '1.0.0',
    'console.project_directory' => __DIR__ . '/..'
));
$app->register(new \App\Markets\ConsoleServiceProvider());

define('WEB_DIRECTORY', __DIR__);

return $app;
