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
                function ($carry, $value) {
                    return $carry + $value;
                },
                self::$array
            )
        );
    }

    public function testVectorIsFoldable()
    {
        $this->assertEquals(
            16,
            foldl1(
                function ($carry, $value) {
                    return $carry + $value;
                },
                self::$vector
            )
        );
    }
}
