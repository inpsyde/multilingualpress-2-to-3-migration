<?php

namespace Inpsyde\MultilingualPress2to3\Test\Unit\Event;

use Inpsyde\MultilingualPress2to3\Event\WpTriggerCapableTrait as TestSubject;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\MockObject\MockObject;
use Andrew\Proxy;
use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Actions;

/**
 * Tests `WpTriggerCapableTrait`.
 *
 * @package MultilingualPress2to3
 */
class WpTriggerCapableTraitTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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
     * Tests `_trigger()`.
     *
     * It must use the WP `do_action()` function to trigger the action with the correct data.
     */
    public function testTrigger()
    {
        $subject = $this->_createSubject();
        $_subject = $this->_getProxy($subject);
        /* @var $_subject TestSubject */
        $name = uniqid('action');
        $data = [uniqid('key') => uniqid('value')];
        $expectedData = (object) $data;
        $expectedData->event_name = $name;
        Actions\expectDone($name)
            ->times(1)
            ->with(\Mockery::on(function ($value) use ($expectedData) {
                return $value == $expectedData;
            }));

        $result = $_subject->_trigger($name, $data);
        $this->assertEquals((array) $expectedData, $result);
    }

    /**
     * Creates a proxy for the given object.
     *
     * This allows access to that object's protected members as if they were public.
     *
     * @param object $object The object to create a proxy for.
     * @return Proxy The proxy.
     */
    protected function _getProxy($object) {
        return new Proxy($object);
    }
}
