<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\pipe_first;
use PHPUnit\Framework\TestCase;

class PipeFirstTest extends TestCase
{
    use CollectionSetUpTrait;

    public function increment(int $x, int $by = 1): int
    {
        return $x + $by;
    }

    public function testPipeFirst()
    {
        // can compose callables

        $pipe = pipe_first(
            '\IrRegular\Hopper\identity',
            '\IrRegular\Hopper\identity'
        );

        $this->assertEquals(1, $pipe(1));

        // can also compose callables with args

        $pipe = pipe_first(
            [$this, 'increment'],
            [$this, 'increment'],
            [[$this, 'increment'], 2]
        );

        $this->assertEquals(5, $pipe(1));
    }
}
