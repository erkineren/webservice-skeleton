<?php


namespace App\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class RouteGroup
{
    /**
     * @Required
     * @var string
     */
    public $pattern;
}