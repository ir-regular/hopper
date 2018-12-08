<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\values;
use PHPUnit\Framework\TestCase;

class ValuesTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanTestValues()
    {
        $this->assertEquals(self::$array, values(self::$array));
        $this->assertEquals(self::$array, values(self::$vector));
        $this->assertEquals(self::$array, values(self::$hashMap));
        // Note that currently, set preserves the order in which elements were first inserted.
        // This is however an implementation detail and should not be relied upon.
        $this->assertEquals([1, 2, 4, 3], values(self::$set));
    }
}
