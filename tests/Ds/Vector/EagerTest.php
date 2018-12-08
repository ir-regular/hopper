<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Ds\Vector;

use function IrRegular\Hopper\hash_map;
use function IrRegular\Hopper\set;
use function IrRegular\Hopper\vector;
use PHPUnit\Framework\TestCase;

class EagerTest extends TestCase
{
    public function testCanCreateVectorFromIterator()
    {
        $iterator = new \ArrayIterator([1, 2, 3, 1, 2, 3]);
        $vector = vector($iterator);

        $this->assertEquals(6, $vector->count());
        $this->assertTrue($vector->isKey(0));
        $this->assertEquals(1, $vector->get(0));
    }

    public function testVectorErasesOldArrayIndex()
    {
        $vector = vector([1 => 'a', 2 => 'b', 5 => 'e']);

        $this->assertEquals(3, $vector->count());
        $this->assertEquals([0, 1, 2], $vector->getKeys());
        $this->assertEquals(['a', 'b', 'e'], $vector->getValues());
    }

    public function testCanCreateVectorFromHashMap()
    {
        $hashMap = hash_map([1, 2], ['one', 'two']);
        $vector = vector($hashMap);
        $this->assertEquals([['one', 1], ['two', 2]], $vector->getValues());
    }

    public function testCanCreateVectorFromSet()
    {
        $set = set([1, 2, 1]);
        $vector = vector($set);
        $this->assertEquals([1, 2], $vector->getValues());
    }
}
