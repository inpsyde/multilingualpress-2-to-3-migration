<?php

namespace Inpsyde\MultilingualPress2to3\Test\Unit\Handler;

use Dhii\I18n\FormatTranslatorInterface;
use Inpsyde\MultilingualPress2to3\Handler\ControllerTrait as TestSubject;
use Mockery\Container;
use PHPUnit\Framework\MockObject\MockObject;
use Andrew\Proxy;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * Tests `HandlerTrait`.
 *
 * @package MultilingualPress2to3
 */
class ControllerTraitTest extends TestCase
{
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
     * Tests that the `getJsUrl()` method works correctly.
     *
     * It must return a URL string which contains the base URL and the relative path.
     */
    public function testGetJsUrl()
    {
        $subject = $this->_createSubject(['_getConfig']);
        $key = 'base_url';
        $value = uniqid('_base_url-');
        $path = uniqid('_path-');
        $_subject = $this->_getProxy($subject);
        /* @var $_subject TestSubject */

        $subject->expects($this->exactly(1))
            ->method('_getConfig')
            ->with($this->equalTo($key))
            ->will($this->returnValue($value));

        $result = $_subject->getJsUrl($path);
        $this->assertContains($path, $result, 'JS URL returned does not contain the path');
        $this->assertContains($value, $result, 'JS URL returned does not contain the base URL');
    }

    /**
     * Tests that the `getCssUrl()` method works correctly.
     *
     * It must return a URL string which contains the base URL and the relative path.
     */
    public function testGetCssUrl()
    {
        $subject = $this->_createSubject(['_getConfig']);
        $key = 'base_url';
        $value = uniqid('_base_url-');
        $path = uniqid('_path-');
        $_subject = $this->_getProxy($subject);
        /* @var $_subject TestSubject */

        $subject->expects($this->exactly(1))
            ->method('_getConfig')
            ->with($this->equalTo($key))
            ->will($this->returnValue($value));

        $result = $_subject->getCssUrl($path);
        $this->assertContains($path, $result, 'CSS URL returned does not contain the path');
        $this->assertContains($value, $result, 'CSS URL returned does not contain the base URL');
    }

    /**
     * Tests that the `_getTranslator()` method works correctly.
     *
     * It must return a `FormatTranslatorInterface` instance.
     */
    public function testGetTranslator()
    {
        $subject = $this->_createSubject(['_getConfig']);
        $key = 'translator';
        $value = $this->_createTranslator();
        $_subject = $this->_getProxy($subject);
        /* @var $_subject TestSubject */

        $subject->expects($this->exactly(1))
            ->method('_getConfig')
            ->with($this->equalTo($key))
            ->will($this->returnValue($value));

        $result = $_subject->_getTranslator();
        $this->assertSame($value, $result, 'Wrong translator returned');
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

    /**
     * Creates a mock of a format translator.
     *
     * @return MockObject|FormatTranslatorInterface The new translator mock.
     */
    protected function _createTranslator()
    {
        $mock = $this->getMockBuilder(FormatTranslatorInterface::class)
            ->getMock();

        return $mock;
    }
}
