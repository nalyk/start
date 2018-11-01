<?php
// Routes

$app->get('/[{language:[ro|en|ru]+}[/]]', App\Action\HomeAction::class)
    ->setName('homepage_view');

$app->get('/{language:[ro|en|ru]+}/news/{id}[/{slug}[.html]]', App\Controller\NewsController::class . ':viewNews')
    ->setName('news_view');

$app->any('/{language:[ro|en|ru]+}/add/news[/]', App\Controller\NewsController::class . ':addNews')
    ->setName('article_add');

$app->any('/{language:[ro|en|ru]+}/edit/news/{id}[/]', App\Controller\NewsController::class . ':editNews')
    ->setName('article_edit');

$app->post('/{language:[ro|en|ru]+}/upload/media[/]', App\Controller\MediaController::class . ':uploadMediaRoute')
    ->setName('media_upload');

// Catch-all 404
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler;
    return $handler($req, $res);
});
