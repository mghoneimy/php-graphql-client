<?php

trait TraitWithMultipleMethods
{
    public function testTheTrait() {
        $this->innerTest();
        die();
    }

    private function innerTest() {
        print "test!";
        return 0;
    }
}