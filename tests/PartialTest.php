<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\partial;
use PHPUnit\Framework\TestCase;

class PartialTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testPartial()
    {
        $double = function ($x) { return 2 * $x; };
        // I could use the Hopper `map`; I want to show you can do this with the eager library version as well
        $doubleAll = partial('array_map', $double);

        $this->assertEquals([0, 2, 4, 6], $doubleAll([0, 1, 2, 3]));
    }
}
