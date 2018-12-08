<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\vector;
use function IrRegular\Hopper\rest;
use PHPUnit\Framework\TestCase;

class RestTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanGetRestOfElements()
    {
        $this->assertEquals(
            [2, 1, 4, 3, 1, 4],
            rest(self::$array)
        );

        $this->assertEquals(
            vector([2, 1, 4, 3, 1, 4]),
            rest(self::$vector)
        );

        // @TODO: this is not really what should happen
        // but then neither HashMap nor Set are Sequences
        // Sort out exceptions.

        $this->assertEquals(
            [
                'key 1' => 2,
                'key 2' => 1,
                'key 3' => 4,
                'key 4' => 3,
                'key 5' => 1,
                'key 6' => 4,
            ],
            rest(self::$hashMap)
        );

        $this->assertEquals([2, 4, 3], rest(self::$set));
    }
}
