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

        // Neither HashMap nor Set are Sequences;
        // the code falls back to their IteratorAggregate impl

        $this->assertEquals(
            [
                [2, 'key 1'],
                [1, 'key 2'],
                [4, 'key 3'],
                [3, 'key 4'],
                [1, 'key 5'],
                [4, 'key 6'],
            ],
            rest(self::$hashMap)
        );

        $this->assertEquals([2, 4, 3], rest(self::$set));
    }
}
