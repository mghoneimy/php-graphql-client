<?php

namespace GraphQL;

/**
 * Class InlineFragment
 *
 * @package GraphQL
 */
class InlineFragment
{
    use FieldTrait;

    /**
     * Stores the format for the inline fragment format
     *
     * @var string
     */
    protected const FORMAT = '... on %s%s';

    /**
     * @var string
     */
    protected $typeName;

    /**
     * InlineFragment constructor.
     *
     * @param string $typeName
     */
    public function __construct(string $typeName)
    {
        $this->typeName = $typeName;
    }

    /**
     *
     */
    public function __toString()
    {
        return sprintf(static::FORMAT, $this->typeName, $this->constructSelectionSet());
    }
}