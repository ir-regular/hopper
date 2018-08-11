<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Collection;

use IrRegular\Hopper\Collection\HashMap;
use PHPUnit\Framework\TestCase;

class HashMapTest extends TestCase
{
    public function testCanCreateHashMapFromIterator()
    {
        $iterator = new \ArrayIterator([
            'Mary' => 1,
            'Joe' => 2,
            'Jane' => 3,
            'Andy' => 1,
            'Greta' => 2,
            'Josh' => 3
        ]);
        $hashMap = new HashMap($iterator);

        $this->assertEquals(6, $hashMap->getCount());
        $this->assertTrue($hashMap->isKey('Mary'));
        $this->assertEquals(1, $hashMap->get('Mary'));
    }
}
