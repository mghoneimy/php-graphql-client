<?php

namespace GraphQL\Test;

use GraphQL\Query;
use GraphQL\Client;

trait TraitWithEverything
{
    protected $propOne;
    protected $propTwo = true;

    public function getProperties() {
        return [$this->propOne, $this->propTwo];
    }

    public function clearProperties() {
        $this->propOne = 1;
        $this->propTwo = 2;
    }
}