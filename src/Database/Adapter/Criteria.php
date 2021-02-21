<?php


namespace App\Database\Adapter;


class Criteria
{
    protected $key;
    protected $comparison;
    protected $value;

    /**
     * Criteria constructor.
     * @param string $key
     * @param string $comparison
     * @param string $value
     */
    public function __construct(string $key, string $comparison, ?string $value)
    {
        $this->key = $key;
        $this->comparison = $comparison;
        $this->value = $value;
    }

    public static function equals(string $key, ?string $value)
    {
        return new Criteria($key, '=', "'$value'");
    }

    public static function contains(string $key, ?string $value)
    {
        return new Criteria($key, 'LIKE', "%$value%");
    }

    public static function startsWith(string $key, ?string $value)
    {
        return new Criteria($key, 'LIKE', "$value%");
    }

    public static function endsWith(string $key, ?string $value)
    {
        return new Criteria($key, 'LIKE', "%$value");
    }

    public function toString()
    {
        return $this->getKey() . ' ' . $this->getComparison() . ' ' . $this->getValue();
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getComparison(): string
    {
        return $this->comparison;
    }

    /**
     * @param string $comparison
     */
    public function setComparison(string $comparison): void
    {
        $this->comparison = $comparison;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }


}