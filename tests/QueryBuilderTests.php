<?php

use PHPUnit\Framework\TestCase;

class BuilderMock extends League\Monga\Query\Builder
{
}

class QueryBuilderTests extends TestCase
{
    protected $builder;

    public function setUp()
    {
        $this->builder = new BuilderMock();
    }

    public function getProperty($property)
    {
        $reflection = new ReflectionObject($this->builder);
        $property = $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($this->builder);
    }

    public function testSafe()
    {
        $this->builder->safe();
        $this->assertTrue($this->getProperty('safe'));
        $this->builder->safe(false);
        $this->assertFalse($this->getProperty('safe'));
    }

    public function testFsync()
    {
        $this->builder->fsync();
        $this->assertTrue($this->getProperty('fsync'));
        $this->builder->fsync(false);
        $this->assertFalse($this->getProperty('fsync'));
    }

    public function testTimeout()
    {
        $this->assertNull($this->getProperty('timeout'));
        $this->builder->timeout(100);
        $this->assertEquals(100, $this->getProperty('timeout'));
    }

    public function testSetOption()
    {
        $this->builder->setOptions([
            'fsync' => true,
            'safe' => true,
        ]);

        $this->assertTrue($this->getProperty('fsync'));
        $this->assertTrue($this->getProperty('safe'));

        $this->builder->setOptions([
            'fsync' => false,
            'safe' => false,
        ]);

        $this->assertFalse($this->getProperty('fsync'));
        $this->assertFalse($this->getProperty('safe'));
    }

    public function testGetOptions()
    {
        $result = $this->builder->getOptions();

        $this->assertEquals([
            'w' => 0,
            'fsync' => false,
            'connectTimeoutMS' => MongoCursor::$timeout,
        ], $result);

        $result = $this->builder->timeout(100)->getOptions();

        $this->assertEquals([
            'w' => 0,
            'fsync' => false,
            'connectTimeoutMS' => 100,
        ], $result);
    }
}
