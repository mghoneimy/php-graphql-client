<?php

namespace GraphQL\Tests;

use GraphQL\Tests\Helper\TestHelper;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 * @package GraphQL\Tests
 */
class TestCase extends BaseTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var TestHelper
     */
    protected $helper;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->client = new Client();
        $this->helper = new TestHelper();
    }
}
