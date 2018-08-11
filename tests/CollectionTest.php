<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\Collection\hash_map;
use function IrRegular\Hopper\Collection\set;
use function IrRegular\Hopper\Collection\vector;
use function IrRegular\Hopper\is_empty;
use function IrRegular\Hopper\size;
use function IrRegular\Hopper\values;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanTestEmptiness()
    {
        $this->assertTrue(is_empty([]));
        $this->assertTrue(is_empty(hash_map([])));
        $this->assertTrue(is_empty(set([])));
        $this->assertTrue(is_empty(vector([])));

        $this->assertFalse(is_empty(self::$array));
        $this->assertFalse(is_empty(self::$hashMap));
        $this->assertFalse(is_empty(self::$set));
        $this->assertFalse(is_empty(self::$vector));
    }

    public function testCanTestElementCount()
    {
        $this->assertEquals(0, size([]));
        $this->assertEquals(0, size(hash_map([])));
        $this->assertEquals(0, size(set([])));
        $this->assertEquals(0, size(vector([])));

        $this->assertEquals(7, size(self::$array));
        $this->assertEquals(7, size(self::$hashMap));
        $this->assertEquals(7, size(self::$vector));
        // set removes duplicates
        $this->assertEquals(4, size(self::$set));
    }

    public function testCanTestValues()
    {
        $this->assertEquals(self::$array, values(self::$array));
        $this->assertEquals(self::$array, values(self::$vector));
        $this->assertEquals(self::$array, values(self::$hashMap));
        // Note that currently, set preserves the order in which elements were first inserted.
        // This is however an implementation detail and should not be relied upon.
        $this->assertEquals([1, 2, 4, 3], values(self::$set));
    }
}
