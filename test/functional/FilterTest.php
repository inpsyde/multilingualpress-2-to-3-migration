<?php


namespace Inpsyde\MultilingualPress2to3\Test\Functional;

use PHPUnit\Framework\TestCase;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

class FilterTest extends TestCase
{
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

    public function testFilter()
    {
        $filter = 'my_test_filter';

        add_filter($filter, function ($value) {
            return true;
        });

        $result = apply_filters($filter, false);
        $this->assertTrue($result);
    }
}