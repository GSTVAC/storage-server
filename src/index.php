<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;

$env = realpath(__DIR__ . '/../.env');
if ($env)
    (Dotenv\Dotenv::create(dirname($env)))->load();


$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$c = new \Slim\Container($configuration);

$c['notFoundHandler'] = function ($c) {
    return function (Request $request, Response $response, Exception $exception) use ($c) {
        return $response->withStatus(404)->withoutHeader('Content-Type');
    };
};

$app = new Slim\App($c);

$app->get('/f/{uuid}', function (Request $request, Response $response, array $args) {
    $name = $args['uuid'];

    $file = realpath(getenv('FILES_FOLDER') . '/' . $name . '.png');

    if ($file === false) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    $fh = fopen($file, 'rb');

    $stream = new \Slim\Http\Stream($fh);

    return $response->withHeader('Content-Type', 'application/octet-stream')
        ->withBody($stream);
});

$app->run();