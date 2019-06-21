<?php

namespace Inpsyde\MultilingualPress2to3\Test\Functional;

use Inpsyde\MultilingualPress\Framework\Cache\Exception\Exception;
use Inpsyde\MultilingualPress2to3\IntegrationHandler as TestSubject;
use Inpsyde\MultilingualPress2to3\Test\Helper\ComponentMockeryTrait;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;
use Throwable;

class IntegrationHandlerTest extends TestCase
{
    use ComponentMockeryTrait;

    protected function setUp()
    {
        parent::setUp();
        setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
        tearDown();
    }

    /**
     * Creates a new instance of the test subject.
     *
     * @return MockObject|TestSubject The new instance.
     * @throws Exception If problem creating.
     * @throws Throwable If problem running.
     */
    protected function _createSubject($methods = [], $dependencies = [])
    {
        $subject = $this->createMockBuilder(TestSubject::class, $methods, $dependencies)
            ->getMock();

        return $subject;
    }

    public function testPreventSharedTableDeletion()
    {
        {
            $names = $this->createArray(rand(3, 9), function ($i) {
                return uniqid("table-$i-name");
            });
            $removedIndex = 1;
            $filter = 'multilingualpress.deleted_tables';
            $settings = $this->createContainer([
                'shared_table_names' => [
                    $names[$removedIndex],
                ],
                'filter_deleted_tables' => $filter,
            ]);
            $subject = $this->_createSubject(null, [$settings]);
            $_subject = $this->proxy($subject);
        }

        {
            $_subject->_preventSharedTableDeletion();
            $expected = $names;
            array_splice($expected, $removedIndex, 1);

            $this->assertEquals($expected, apply_filters($filter, $names));
        }
    }
}
