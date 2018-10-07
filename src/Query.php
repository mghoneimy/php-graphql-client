<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 10/4/18
 * Time: 11:56 PM
 */

namespace GraphQL;

/**
 * Class Query
 *
 * @package GraphQL
 */
class Query
{
    /**
     * Stores the GraphQL query format
     *
     * @var string
     */
    private static $queryFormat = "%s(%s) {\n%s\n}";

    /**
     * Stores the object being queried for
     *
     * @var string
     */
    private $object;

    /**
     * Stores the list of constraints imposed on querying data
     *
     * @var array
     */
    private $constraints;

    /**
     * Stores the list of attribute we want to return from the query, can include nested queries
     *
     * @var array
     */
    private $returnAttributes;

    /**
     * Private member that's not accessible from outside the class, used internally to deduce if query is nested or not
     *
     * @var bool
     */
    private $isNested;

    /**
     * GQLQueryBuilder constructor.
     *
     * @param string $queryObject
     */
    public function __construct($queryObject)
    {
        $this->object           = $queryObject;
        $this->constraints      = [];
        $this->returnAttributes = [];
        $this->isNested         = false;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function constructConstraints()
    {
        $constraintsString = '';
        $first             = true;
        foreach ($this->constraints as $constraint => $value) {

            // Append space at the beginning if it's not the first item on the list
            if ($first) {
                $first = false;
            } else {
                $constraintsString .= ' ';
            }
            $constraintsString .= $constraint . ': ' . $value;
        }

        return $constraintsString;
    }

    /**
     * @return string
     */
    protected function constructAttributes()
    {
        $attributesString = '';
        $first            = true;
        foreach ($this->returnAttributes as $attribute) {

            // Append empty line at the beginning if it's not the first item on the list
            if ($first) {
                $first = false;
            } else {
                $attributesString .= "\n";
            }

            // If query is included in attributes set as a nested query
            if ($attribute instanceof Query) {
                $attribute->setAsNested();
            }

            // Append attribute to returned attributes list
            $attributesString .= $attribute;
        }

        return $attributesString;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        $queryFormat = static::$queryFormat;
        if (!$this->isNested) {
            $queryFormat = "{\n" . static::$queryFormat . "\n}";
        }
        $constraintsString = $this->constructConstraints();
        $attributesString  = $this->constructAttributes();

        return sprintf($queryFormat, $this->object, $constraintsString, $attributesString);
    }

    /**
     *
     */
    private function setAsNested()
    {
        $this->isNested = true;
    }

    /**
     * @param array $constraints
     *
     * @return Query
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @param array $returnAttributes
     *
     * @return Query
     */
    public function setReturnAttributes($returnAttributes)
    {
        $this->returnAttributes = $returnAttributes;

        return $this;
    }
}
