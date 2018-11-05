<?php
// Routes

$app->get('/[{language:[ro|en|ru]+}[/]]', App\Controller\IndexController::class)
    ->setName('homepage_view');

$app->get('/{language:[ro|en|ru]+}/admin[/]', App\Controller\AdminController::class . ':adminIndex')
    ->setName('admin_index_view');

// Catch-all 404
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler;
    return $handler($req, $res);
});
