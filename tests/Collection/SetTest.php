<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Collection;

use IrRegular\Hopper\Collection\Set;
use IrRegular\Tests\Hopper\CollectionSetUpTrait;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testSetCanContainObjects()
    {
        $o1 = new \stdClass();

        $set = new Set([$o1]);
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

        $set = new Set([$o1, $o2, $o1]);
        $this->assertEquals(2, $set->getCount());
    }

    public function testCanCreateSetFromIterator()
    {
        $iterator = new \ArrayIterator([1, 2, 3, 1, 2, 3]);
        $set = new Set($iterator);

        $this->assertEquals(3, $set->getCount());
        $this->assertTrue($set->isKey(1));
        $this->assertTrue($set->isKey(2));
        $this->assertTrue($set->isKey(3));
    }

    public function testCanCreateSetFromNestedArray()
    {
        $set = new Set(self::$nestedArray);
        $this->assertEquals(4, $set->getCount());
        $this->assertTrue($set->isKey(['name' => 'John', 'address' => ['city' => 'New York']]));
    }

    public function testSetKeysEqualSetValues()
    {
        $this->assertEquals(self::$set->getKeys(), self::$set->getValues());
    }
}
