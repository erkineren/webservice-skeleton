<?php


namespace App\Model;

/**
 * Class Token
 * @package App\Model
 */
class Token
{

    /**
     * @var
     */
    private $decoded;

    /**
     * Token constructor.
     * @param array $decoded
     */
    public function __construct(array $decoded = [])
    {
        $this->populate($decoded);
    }

    /**
     * @param array $decoded
     */
    public function populate(array $decoded)
    {
        $this->decoded = $decoded;
    }

    /**
     * @param array $scope
     * @return bool
     */
    public function hasScope(array $scope)
    {
        return !!count(array_intersect($scope, $this->decoded["scope"]));
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        return $this->decoded;
    }

    /**
     * @return mixed
     */
    public function getClaims()
    {
        return $this->decoded['claims'];
    }
}
