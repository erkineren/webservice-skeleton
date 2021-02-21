<?php


namespace App\Middleware;


use App\Response\ApiResponse;
use App\Response\ApiResponse as Response;
use Slim\Http\Request;
use Slim\Route;

class Logger extends BaseMiddleware
{
    public function __invoke(Request $request, ApiResponse $response, callable $next)
    {
        /** @var Route $route */
        $route = $request->getAttribute('route');
        $logger = $this->container->logger;
        $token = $this->container->token;

        if ($route) {
            $context = [];
            $context['args'] = $route->getArguments();
            $context['ip'] = $request->getAttribute('ip_address');
            $context['user-agent'] = $request->getHeaderLine('user-agent');
            $claims = $token->getClaims();

            if ($user = $claims->user) {
                $context['user'] = "{$user->id}-{$user->firstname} {$user->lastname}";
            }

            $logger->info($request->getMethod() . ' ' . $route->getPattern(), $context);
        }

        /** @var Response $response */
        $response = $next($request, $response);

        if ($route) {
            $logger->info($response->getStatusCode() . ' ' . $response->getReasonPhrase());
        }

        return $response;
    }

}