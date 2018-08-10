<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Collection;

use IrRegular\Hopper\Collection\Set;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
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
        $o2 = new \stdClass();

        $set = new Set([$o1, $o2, $o1]);
        $this->assertEquals(2, $set->getCount());
    }
}
