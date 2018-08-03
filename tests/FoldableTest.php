<?php

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\foldl;
use function IrRegular\Hopper\foldl1;
use function IrRegular\Hopper\second;
use PHPUnit\Framework\TestCase;

class FoldableTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testArrayIsFoldable()
    {
        $this->assertEquals(
            16,
            foldl1(
                // `array_sum` expects an array input, so we need to "pack" the arguments with `apply`
                partial('IrRegular\Tests\Hopper\apply', 'array_sum'),
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
                partial('IrRegular\Tests\Hopper\apply', 'array_sum'),
                self::$vector
            )
        );
    }

    public function testHashMapIsFoldable()
    {
        // Sum every other element of `self::$hashMap`
        // It's way overcomplicated for what it does, but I wanted something that demoed use of keys.

        $this->assertEquals(
            9,
            foldl(
                function ($carry, $item) {
                    [$key, $value] = $item;

                    return (second(explode(' ', $key)) % 2 == 0)
                        ? $carry + $value
                        : $carry;
                },
                0,
                self::$hashMap
            )
        );
    }
}
