<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL;

use GraphQL\Util\StringLiteralFormatter;

/**
 * Class Variable.
 */
class Variable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $required;

    /**
     * @var null|bool|float|int|string
     */
    protected $defaultValue;

    /**
     * Variable constructor.
     *
     * @param null $defaultValue
     */
    public function __construct(string $name, string $type, bool $isRequired = false, $defaultValue = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $isRequired;
        $this->defaultValue = $defaultValue;
    }

    public function __toString(): string
    {
        $varString = "\${$this->name}: {$this->type}";
        if ($this->required) {
            $varString .= '!';
        } elseif (!empty($this->defaultValue)) {
            $varString .= '='.StringLiteralFormatter::formatValueForRHS($this->defaultValue);
        }

        return $varString;
    }
}
