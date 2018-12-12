<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\compose;
use PHPUnit\Framework\TestCase;

class ComposeTest extends TestCase
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
}
