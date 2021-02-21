<?php

use App\Core\App;
use App\Repository\AuthRepository;
use App\Response\ApiResponse as Response;
use Firebase\JWT\JWT;
use Slim\Http\Request;
use Tuupola\Base62;


return function (App $app) {


    $app->group('/auth', function () use ($app) {

        $container = $app->getContainer();
        $authRepository = function () use ($container) {
            return $container->getRepository(AuthRepository::class);
        };

        $app->get("/me", function (Request $request, Response $response, $arguments) use ($container) {
            return $response->withJsonAuto($container->token->toArray());
        });

        $app->get("/login", function (Request $request, Response $response, $arguments) {
            $requested_scopes = $request->getParsedBody() ?: [];
            $valid_scopes = [
                "todo.create",
                "todo.read",
                "todo.update",
                "todo.delete",
                "todo.list",
                "todo.all"
            ];
            $scopes = array_filter($requested_scopes, function ($needle) use ($valid_scopes) {
                return in_array($needle, $valid_scopes);
            });
            $now = new DateTime();
            $future = new DateTime("now +2 hours");
            $server = $request->getServerParams();
            $jti = (new Base62)->encode(random_bytes(16));
            $payload = [
                "iat" => $now->getTimeStamp(),
                "exp" => $future->getTimeStamp(),
                "jti" => $jti,
                "sub" => 'subject',
                "scope" => $scopes
            ];
            $secret = getenv("JWT_SECRET");
            $token = JWT::encode($payload, $secret, "HS256");
            $data["token"] = $token;
            $data["expires"] = $future->getTimeStamp();
            return $response->withJsonAuto($data);
        });


    });
};
