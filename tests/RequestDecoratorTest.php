<?php

namespace GraphQL\Tests;

use GraphQL\RequestDecorator;
use Psr\Http\Message\RequestInterface;

/**
 * Class RequestDecoratorTest
 * @package GraphQL\Tests
 */
class RequestDecoratorTest extends TestCase
{
    /**
     * @var RequestDecorator
     */
    protected $decorator;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->decorator = new RequestDecorator();
        parent::setUp();
    }

    /**
     * @covers \GraphQL\RequestDecorator::decorate
     */
    public function testDecorateWithVersionHttpOption()
    {
        $stream = $this->helper->createMockStream();
        $request = $this->createStub(RequestInterface::class);
        $request->expects($this->once())->method('withProtocolVersion')->with(2)->willReturnSelf();
        $request->method('withBody')->willReturnSelf();
        $this->decorator->decorate($request, $stream, ['version' => 2]);
    }

    /**
     * @covers \GraphQL\RequestDecorator::decorate
     */
    public function testDecorateWithBody()
    {
        $stream = $this->helper->createMockStream();
        $request = $this->createStub(RequestInterface::class);
        $request->expects($this->once())->method('withBody')->with($stream)->willReturnSelf();
        $this->decorator->decorate($request, $stream);
    }

    /**
     * @covers \GraphQL\RequestDecorator::decorate
     */
    public function testDecorateWithHeaders()
    {
        $headers = [
            'header_key_1' => 'header_value_1',
            'header_key_2' => 'header_value_2',
            'header_key_3' => 'header_value_3',
        ];

        foreach ($headers as $name => $value) {
            $consecutiveArguments[] = [$name, $value];
        }

        $stream = $this->helper->createMockStream();
        $request = $this->createStub(RequestInterface::class);
        $request->expects($this->exactly(3))
            ->method('withHeader')
            ->withConsecutive(... $consecutiveArguments)
            ->willReturnSelf();

        $request->method('withBody')->willReturnSelf();
        $this->decorator->decorate($request, $stream, [], $headers);
    }
}
