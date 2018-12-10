<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\first;
use PHPUnit\Framework\TestCase;

class FirstTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanGetFirstElement()
    {
        $this->assertEquals(1, first(self::$array));
        $this->assertEquals(1, first(self::$vector));

        // Not Sequences, code falls back to IteratorAggregate
        $this->assertEquals([1, 'key 0'], first(self::$hashMap));
        $this->assertEquals(1, first(self::$set));
    }
}
