<?php

namespace Inpsyde\MultilingualPress2to3\Test\Unit\Handler;

use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Handler\RunHandlerListCapableTrait as TestSubject;
use Inpsyde\MultilingualPress2to3\Test\Helper\ComponentMockeryTrait;
use PHPUnit\Framework\MockObject\MockObject;
use Andrew\Proxy;
use PHPUnit\Framework\TestCase;

/**
 * Tests `RunHandlerListCapableTrait`.
 *
 * @package MultilingualPress2to3
 */
class RunHandlerListCapableTraitTest extends TestCase
{
    use ComponentMockeryTrait;

    /**
     * Creates a new instance of the test subject.
     *
     * @return MockObject|TestSubject The new instance.
     */
    protected function _createSubject($methods = [])
    {
        $subject = $this->getMockBuilder(TestSubject::class)
            ->setMethods($methods)
            ->getMockForTrait();

        return $subject;
    }

    /**
     * Tests that the subject can be creates successfully.
     */
    public function testCreation()
    {
        $subject = $this->_createSubject();
        $this->assertInternalType('object', $subject, 'A valid instance of the test subject could not be created');
    }

    /**
     * Tests `_runHandlers()`.
     *
     * It must invoke the `run()` method on all handlers it is given.
     */
    public function testRun()
    {
        $subject = $this->_createSubject();
        $handlerAmount = rand(2, 9);
        $handlers = $this->_createHandlers($handlerAmount);
        $proxy = $this->proxy($subject);
        /* @var $proxy TestSubject */

        foreach ($handlers as $handler) {
            $handler->expects($this->exactly(1))
                ->method('run');
        }

        $proxy->_runHandlerList($handlers);
    }

    /**
     * Creates a mock of a handler.
     *
     * @return MockObject|HandlerInterface The new handler mock.
     */
    protected function _createHandler()
    {
        $mock = $this->getMockBuilder(HandlerInterface::class)
            ->getMock();

        return $mock;
    }

    /**
     * Creates a list of handlers.
     *
     * @param int $amount The amount of handlers to create.
     *
     * @return HandlerInterface[]|MockObject[] The list of new handlers.
     */
    protected function _createHandlers(int $amount)
    {
        $handlers = [];

        for ($i = 0; $i < $amount-1; $i++) {
            $handlers[] = $this->_createHandler();
        }

        return $handlers;
    }
}
