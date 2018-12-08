<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\partial_last;
use PHPUnit\Framework\TestCase;

class PartialLastTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testPartialLast()
    {
        $repeatFourTimes = partial_last('\array_fill', 0, 4);

        $this->assertEquals(['x', 'x', 'x', 'x'], $repeatFourTimes('x'));
        $this->assertEquals([1, 1, 1, 1], $repeatFourTimes(1));
    }
}
