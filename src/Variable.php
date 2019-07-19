<?php

namespace GraphQL;

/**
 * Class Variable
 *
 * @package GraphQL
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
     * @var null|string|int|float|bool
     */
    protected $defaultValue;

    /**
     * Variable constructor.
     *
     * @param string $name
     * @param string $type
     * @param bool   $isRequired
     * @param null   $defaultValue
     */
    public function __construct(string $name, string $type, bool $isRequired = false, $defaultValue = null)
    {
        $this->name         = $name;
        $this->type         = $type;
        $this->required     = $isRequired;
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return bool|float|int|string|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $varString = "\$$this->name: $this->type";
        if ($this->required) {
            $varString .= '!';
        } elseif (!empty($this->defaultValue)) {
            $varString .= "=$this->defaultValue";
        }

        return $varString;
    }
}