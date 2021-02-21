<?php


namespace App\Annotations;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Route
{
    /**
     * @Required
     * @var string
     */
    public $pattern;
}