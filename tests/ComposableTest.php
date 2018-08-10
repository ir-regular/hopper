<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\apply;
use function IrRegular\Hopper\compose;
use function IrRegular\Hopper\partial;
use function IrRegular\Hopper\partial_first;
use function IrRegular\Hopper\partial_last;
use PHPUnit\Framework\TestCase;

class ComposableTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCompose()
    {
        $double = function ($x) { return 2 * $x; };
        $decrement = function ($x) { return $x - 1; };

        $f = compose($double, $decrement);

        $this->assertEquals(5, $f(3));
        $this->assertEquals(-1, $f(0));
    }

    public function testPartial()
    {
        $double = function ($x) { return 2 * $x; };
        // I could use the Hopper `map`; I want to show you can do this with the eager library version as well
        $doubleAll = partial('array_map', $double);

        $this->assertEquals([0, 2, 4, 6], $doubleAll([0, 1, 2, 3]));
    }

    public function testPartialFirst()
    {
        $mapOnRange = partial_first('\array_map', [0, 1, 2, 3]);

        $this->assertEquals([0, 2, 4, 6], $mapOnRange(function ($x) { return 2 * $x; }));
        $this->assertEquals([-1, 0, 1, 2], $mapOnRange(function ($x) { return $x - 1; }));
    }

    public function testPartialLast()
    {
        $repeatFourTimes = partial_last('\array_fill', 0, 4);

        $this->assertEquals(['x', 'x', 'x', 'x'], $repeatFourTimes('x'));
        $this->assertEquals([1, 1, 1, 1], $repeatFourTimes(1));
    }

    public function testApply()
    {
        $snakeCase = partial('implode', '_');

        $this->assertEquals('key_value', apply($snakeCase, 'key', 'value'));
    }
}
