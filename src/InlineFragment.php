<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL;

/**
 * Class InlineFragment.
 */
class InlineFragment extends NestableObject
{
    use FieldTrait;

    /**
     * Stores the format for the inline fragment format.
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
     */
    public function __construct(string $typeName)
    {
        $this->typeName = $typeName;
    }

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
