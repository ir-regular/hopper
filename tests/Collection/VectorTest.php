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
}
