<?php

namespace GraphQl\Test;

use GraphQl\Base\Base;
use GraphQl\Interfaces\Intr1;
use GraphQl\Interfaces\Intr2;
use GraphQl\Base\Trait1;
use GraphQl\Base\Trait2;

class ClassWithEverything extends Base implements Intr1, Intr2
{
    use Trait1;
    use Trait2;

    const CONST_ONE = 1;
    const CONST_TWO = "";

    protected $propertyOne;
    protected $propertyTwo = "";

    public function dumpAll() {
        print 'dumping';
    }

    protected function internalStuff($i) {
        return ++$i;
    }
}