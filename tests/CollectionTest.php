<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use IrRegular\Hopper\Collection\HashMap;
use IrRegular\Hopper\Collection\Set;
use IrRegular\Hopper\Collection\Vector;
use function IrRegular\Hopper\is_empty;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanTestEmptiness()
    {
        $this->assertTrue(is_empty([]));
        $this->assertTrue(is_empty(new HashMap([])));
        $this->assertTrue(is_empty(new Set([])));
        $this->assertTrue(is_empty(new Vector([])));

        $this->assertFalse(is_empty(self::$array));
        $this->assertFalse(is_empty(self::$hashMap));
        $this->assertFalse(is_empty(self::$set));
        $this->assertFalse(is_empty(self::$vector));
    }
}
