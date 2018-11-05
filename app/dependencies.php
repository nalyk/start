<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');

    $theme = require __DIR__ . '/themes/'.$settings['theme'].'/settings.php';
    $settings['view'] = array_merge($settings['view'], $theme);

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
    $view->addExtension(new \PurpleBooth\HtmlStripperExtension());
    $view->addExtension(new \Aaronadal\TwigListLoop\Twig\TwigExtension());
    $view->addExtension(new \olivers\Twig\Extension\AvatarExtension());

    return $view;
};

$container['adminview'] = function ($c) {
    $settings = $c->get('settings');

    $theme = require __DIR__ . '/themes/admin/settings.php';
    $settings['view'] = array_merge($settings['view'], $theme);

    $adminview = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    $adminview->getLoader()->addPath($settings['view']['twig']['paths']['public'], 'public');

    // Add extensions
    $adminview->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $adminview->addExtension(new Cocur\Slugify\Bridge\Twig\SlugifyExtension(Cocur\Slugify\Slugify::create()));
    $adminview->addExtension(new Twig_Extensions_Extension_Text());
    $adminview->addExtension(new Twig_Extensions_Extension_Array());
    $adminview->addExtension(new Twig_Extensions_Extension_Intl());
    $adminview->addExtension(new Twig_Extensions_Extension_I18n());
    $adminview->addExtension(new Snilius\Twig\SortByFieldExtension());
    $adminview->addExtension(new Twig_Extension_Debug());
    $adminview->addExtension(new \Umpirsky\Twig\Extension\PhpFunctionExtension());
    $adminview->addExtension(new \Odan\Twig\TwigAssetsExtension($adminview->getEnvironment(), $settings['assets']));
    $adminview->addExtension(new \PurpleBooth\HtmlStripperExtension());
    $adminview->addExtension(new \Aaronadal\TwigListLoop\Twig\TwigExtension());
    $adminview->addExtension(new \olivers\Twig\Extension\AvatarExtension());

    return $adminview;
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
// Controller factories
// -----------------------------------------------------------------------------

$container[App\Controller\AdminController::class] = function ($c) {
    return new App\Controller\AdminController($c->get('adminview'), $c->get('logger'), $c->get('deployd'));
};

$container[App\Controller\IndexController::class] = function ($c) {
    return new App\Controller\IndexController($c->get('view'), $c->get('logger'), $c->get('deployd'));
};