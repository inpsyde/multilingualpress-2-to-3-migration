<?php

namespace Inpsyde\MultilingualPress2to3\Test\Func\Handler;

use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface as TestSubject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests `HandlerInterface`.
 *
 * @package MultilingualPress2to3
 */
class HandlerInterfaceTest extends TestCase
{
    /**
     * Creates a new instance of the test subject.
     *
     * @return MockObject|TestSubject The new instance.
     */
    protected function _createSubject()
    {
        $subject = $this->getMockBuilder(TestSubject::class)
            ->getMock();

        return $subject;
    }

    /**
     * Tests that the subject can be creates successfully.
     */
    public function testCreation()
    {
        $subject = $this->_createSubject();
        $this->assertInstanceOf(TestSubject::class, $subject, 'Subject does not implement required interface');
        $this->assertTrue(method_exists($subject, 'run'), 'Subject does not declare required method');
    }
}
