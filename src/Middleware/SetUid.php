<?php


namespace App\Middleware;


use App\Response\ApiResponse;
use Slim\Http\Request;

class SetUid extends BaseMiddleware
{
    public function __invoke(Request $request, ApiResponse $response, Callable $next)
    {
        $this->container['uid'] = $request->getHeaderLine('X-Uid');
        return $next($request, $response);
    }

}