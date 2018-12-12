<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\partial_first;
use PHPUnit\Framework\TestCase;

class PartialFirstTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testPartialFirst()
    {
        $mapOnRange = partial_first('\array_map', [0, 1, 2, 3]);

        $this->assertEquals([0, 2, 4, 6], $mapOnRange(function ($x) { return 2 * $x; }));
        $this->assertEquals([-1, 0, 1, 2], $mapOnRange(function ($x) { return $x - 1; }));
    }
}
