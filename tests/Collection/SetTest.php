<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Collection;

use function IrRegular\Hopper\Collection\set;
use IrRegular\Tests\Hopper\CollectionSetUpTrait;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testSetCanContainObjects()
    {
        $o1 = new \stdClass();

        $set = set([$o1]);
        $this->assertEquals(1, $set->getCount());
        $this->assertFalse($set->isEmpty());
        $this->assertTrue($set->isKey($o1));
    }

    public function testSetRemovesDuplicates()
    {
        $o1 = new \stdClass();
        $o1->name = 'Jill';

        $o2 = new \stdClass();
        $o2->name = 'Jack';

        $set = set([$o1, $o2, $o1]);
        $this->assertEquals(2, $set->getCount());
    }

    public function testCanCreateSetFromIterator()
    {
        $iterator = new \ArrayIterator([1, 2, 3, 1, 2, 3]);
        $set = set($iterator);

        $this->assertEquals(3, $set->getCount());
        $this->assertTrue($set->isKey(1));
        $this->assertTrue($set->isKey(2));
        $this->assertTrue($set->isKey(3));
    }

    public function testCanCreateSetFromNestedArray()
    {
        $set = set(self::$nestedArray);
        $this->assertEquals(4, $set->getCount());
        $this->assertTrue($set->isKey(['name' => 'John', 'address' => ['city' => 'New York']]));
    }

    public function testSetKeysEqualSetValues()
    {
        $this->assertEquals(self::$set->getKeys(), self::$set->getValues());
    }
}
