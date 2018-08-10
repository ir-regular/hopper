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
        $this->assertFalse($set->isEmpty());
    }
}
