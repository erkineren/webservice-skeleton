<?php

use App\Core\App;
use App\Middleware;
use App\Response\ApiResponse;


return function (App $app) {
    $container = $app->getContainer();

    $app->add(Middleware\Logger::class);

    $app->add(new Tuupola\Middleware\JwtAuthentication([
        "path" => "/",
        "ignore" => ['/auth/login', '/test'],
        "secret" => getenv('JWT_SECRET'),
        "logger" => $container->logger,
        "attribute" => false,
        "error" => function ($response, $arguments) {
            return (new ApiResponse())->withJsonError($arguments["message"], 401);
        },
        "before" => function ($request, $arguments) use ($app) {
            $app->getContainer()->token->populate($arguments["decoded"]);
        },
//        'algorithm' => 'HS256'
    ]));

    $app->add(new RKA\Middleware\IpAddress());
};
