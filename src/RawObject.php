<?php

namespace GraphQL;

/**
 * Class RawObject
 *
 * @package GraphQL
 */
class RawObject
{
    /**
     * @var string
     */
    protected $objectString;

    /**
     * JsonObject constructor.
     *
     * @param string $objectString
     */
    public function __construct($objectString)
    {
        if (!is_string($objectString) || empty($objectString)) {
            throw new \Exception('RawObject should only be constructed with string parameter');
        }
        $this->objectString = $objectString;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->objectString;
    }
}