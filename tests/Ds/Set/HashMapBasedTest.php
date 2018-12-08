<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Ds\Set;

use function IrRegular\Hopper\set;
use IrRegular\Tests\Hopper\CollectionSetUpTrait;
use PHPUnit\Framework\TestCase;

class HashMapBasedTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testSetCanContainObjects()
    {
        $o1 = new \stdClass();

        $set = set([$o1]);
        $this->assertEquals(1, $set->count());
        $this->assertFalse($set->isEmpty());
        $this->assertTrue($set->contains($o1));
    }

    public function testSetRemovesDuplicates()
    {
        $o1 = new \stdClass();
        $o1->name = 'Jill';

        $o2 = new \stdClass();
        $o2->name = 'Jack';

        $set = set([$o1, $o2, $o1]);
        $this->assertEquals(2, $set->count());
    }

    public function testCanCreateSetFromIterator()
    {
        $iterator = new \ArrayIterator([1, 2, 3, 1, 2, 3]);
        $set = set($iterator);

        $this->assertEquals(3, $set->count());
        $this->assertTrue($set->contains(1));
        $this->assertTrue($set->contains(2));
        $this->assertTrue($set->contains(3));
    }

    public function testCanCreateSetFromNestedArray()
    {
        $set = set(self::$nestedArray);
        $this->assertEquals(4, $set->count());
        $this->assertTrue($set->contains(['name' => 'John', 'address' => ['city' => 'New York']]));
    }

    public function testSetCanStoreNumericStrings()
    {
        $set = set(['1', '2', '1']);

        $this->assertEquals(2, $set->count());

        // key type matters!
        $this->assertTrue($set->contains('1'));
        $this->assertFalse($set->contains(1));
        $this->assertTrue(['1', '2'] === $set->getValues());
    }
}
