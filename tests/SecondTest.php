<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\second;
use PHPUnit\Framework\TestCase;

class SecondTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanGetSecondElement()
    {
        $this->assertEquals(2, second(self::$array));
        $this->assertEquals(2, second(self::$vector));

        // @TODO: not sequences, falls back to iterator
        $this->assertEquals(2, second(self::$hashMap));
        $this->assertEquals(2, second(self::$set));
    }
}
