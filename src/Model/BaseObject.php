<?php


namespace App\Model;


/**
 * Class BaseObject
 * @package App\Model
 */
abstract class BaseObject
{
    /**
     * @param object|array $obj
     * @return BaseObject|bool
     */
    static function create($obj)
    {
        if (!is_object($obj) && !is_array($obj)) return false;
        $className = static::class;
        /** @var BaseObject $clsObj */
        $clsObj = new $className;
        foreach ($obj as $key => $value) {
            $clsObj->set($key, $value);
        }
        return $clsObj;
    }

    /**
     * @param $property
     * @param $value
     * @param bool $strict
     * @return $this
     */
    function set($property, $value, $strict = true)
    {
        if (!$strict) $this->{$property} = $value;
        elseif (property_exists($this, $property)) $this->{$property} = $value;
        return $this;
    }
}