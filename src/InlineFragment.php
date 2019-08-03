<?php

namespace GraphQL;

/**
 * Class InlineFragment
 *
 * @package GraphQL
 */
class InlineFragment extends NestableObject
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

    /**
     * @codeCoverageIgnore
     *
     * @return mixed|void
     */
    protected function setAsNested()
    {
        // TODO: Remove this method, it's purely tech debt
    }
}