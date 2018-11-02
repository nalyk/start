<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    $view->getLoader()->addPath($settings['view']['twig']['paths']['public'], 'public');

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Cocur\Slugify\Bridge\Twig\SlugifyExtension(Cocur\Slugify\Slugify::create()));
    $view->addExtension(new Twig_Extensions_Extension_Text());
    $view->addExtension(new Twig_Extensions_Extension_Array());
    $view->addExtension(new Twig_Extensions_Extension_Intl());
    $view->addExtension(new Twig_Extensions_Extension_I18n());
    $view->addExtension(new Snilius\Twig\SortByFieldExtension());
    $view->addExtension(new Twig_Extension_Debug());
    $view->addExtension(new \Umpirsky\Twig\Extension\PhpFunctionExtension());
    $view->addExtension(new \Odan\Twig\TwigAssetsExtension($view->getEnvironment(), $settings['assets']));

    return $view;
};

// Flash messages
$container['flash'] = function ($c) {
    return new Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// Deployd
$container['deployd'] = function ($c) {
    $settings = $c->get('settings');
    $apiserver = $settings['deployd']['protocol']."://".$settings['deployd']['host'];
    $deployd = new App\Controller\DeploydController($c->get('logger'), $apiserver);
    return $deployd;
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container[App\Action\HomeAction::class] = function ($c) {
    return new App\Action\HomeAction($c->get('view'), $c->get('logger'), $c->get('deployd'));
};

// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------
