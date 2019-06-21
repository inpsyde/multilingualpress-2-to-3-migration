<?php

namespace Inpsyde\MultilingualPress2to3\Test\Unit\Event;

use Inpsyde\MultilingualPress2to3\Event\WpHookingTrait as TestSubject;
use Inpsyde\MultilingualPress2to3\Test\Helper\ComponentMockeryTrait;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\MockObject\MockObject;
use Andrew\Proxy;
use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Actions;
use Brain\Monkey\Filters;

/**
 * Tests `WpHookingTrait`.
 *
 * @package MultilingualPress2to3
 */
class WpHookingTraitTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use ComponentMockeryTrait;

    protected function setUp()
    {
        Monkey\setUp();
        parent::setUp();
    }

    protected function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }

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
     * Tests `_addAction()`.
     *
     * It must use the WP `add_action()` method to hook the handler with correct priority.
     */
    public function testAddAction()
    {
        $subject = $this->_createSubject();
        $proxy = $this->proxy($subject);
        /* @var $proxy TestSubject */
        $name = uniqid('action');
        $handler = function () {};
        $priority = rand(1, 99);
        $acceptedArgs = rand(1, 9);

        Actions\expectAdded($name)
            ->times(1)
            ->with($handler, $priority, $acceptedArgs);

        $proxy->_addAction($name, $handler, $priority, $acceptedArgs);
    }

    /**
     * Tests `_addFilter()`.
     *
     * It must use the WP `add_filter()` method to hook the handler with correct priority.
     */
    public function testAddFilter()
    {
        $subject = $this->_createSubject();
        $proxy = $this->proxy($subject);
        /* @var $proxy TestSubject */
        $name = uniqid('action');
        $handler = function () {};
        $priority = rand(1, 99);
        $acceptedArgs = rand(1, 9);

        Filters\expectAdded($name)
            ->times(1)
            ->with($handler, $priority, $acceptedArgs);

        $proxy->_addFilter($name, $handler, $priority, $acceptedArgs);
    }
}
