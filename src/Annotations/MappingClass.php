<?php


namespace App\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class MappingClass
{
    /**
     * @Required
     * @var string
     */
    public $className;
}