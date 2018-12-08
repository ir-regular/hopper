<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\foldl1;
use function IrRegular\Hopper\partial;
use PHPUnit\Framework\TestCase;

class Foldl1Test extends TestCase
{
    use CollectionSetUpTrait;

    public function testArrayIsFoldable()
    {
        $this->assertEquals(
            16,
            foldl1(
            // `array_sum` expects an array input, so we need to "pack" the arguments with `apply`
                partial('IrRegular\Hopper\apply', 'array_sum'),
                self::$array
            )
        );
    }

    public function testVectorIsFoldable()
    {
        $this->assertEquals(
            16,
            foldl1(
            // `array_sum` expects an array input, so we need to "pack" the arguments with `apply`
                partial('IrRegular\Hopper\apply', 'array_sum'),
                self::$vector
            )
        );
    }
}
