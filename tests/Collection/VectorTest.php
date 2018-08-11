<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Collection;

use function IrRegular\Hopper\Collection\vector;
use PHPUnit\Framework\TestCase;

class VectorTest extends TestCase
{
    public function testCanCreateVectorFromIterator()
    {
        $iterator = new \ArrayIterator([1, 2, 3, 1, 2, 3]);
        $vector = vector($iterator);

        $this->assertEquals(6, $vector->getCount());
        $this->assertTrue($vector->isKey(0));
        $this->assertEquals(1, $vector->get(0));
    }

    public function testVectorErasesOldArrayIndex()
    {
        $vector = vector([1 => 'a', 2 => 'b', 5 => 'e']);

        $this->assertEquals(3, $vector->getCount());
        $this->assertEquals([0, 1, 2], $vector->getKeys());
        $this->assertEquals(['a', 'b', 'e'], $vector->getValues());
    }
}
