<?php

namespace App\Exception;

use Exception;
use Slim\Middleware\TokenAuthentication\UnauthorizedExceptionInterface;

/**
 * Class UnauthorizedException
 * @package App\Exception
 */
class UnauthorizedException extends Exception implements UnauthorizedExceptionInterface
{

}