<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\partial_first;
use function IrRegular\Hopper\pipe_last;
use function IrRegular\Hopper\values;
use PHPUnit\Framework\TestCase;

class PipeLastTest extends TestCase
{
    use CollectionSetUpTrait;

    public function increment(int $x, int $by = 1): int
    {
        return $x + $by;
    }

    public function testPipeLast()
    {
        $values = [1, 2, 3];

        $pipe = pipe_last(
            ['\IrRegular\Hopper\map', [$this, 'increment']],
            ['\IrRegular\Hopper\map', partial_first([$this, 'increment'], 2)]
        );

        $this->assertEquals([4, 5, 6], values($pipe($values)));
    }
}
