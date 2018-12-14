<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\foldl;
use function IrRegular\Hopper\partial;
use function IrRegular\Hopper\second;
use PHPUnit\Framework\TestCase;

class FoldlTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testHashMapIsFoldable()
    {
        // Sum every other element of `self::$hashMap`
        // It's way overcomplicated for what it does, but I wanted something that demoed use of keys.

        // Note I'm using foldl rather than foldl1 since the latter will result in weird behaviour:
        // as it uses first() to get the first value, it'd initialise with a [key,value] element.
        //
        // If you _really_ want to foldl1 over hash_map values, use `foldl1($f, to_array(values($hm)))`

        $this->assertEquals(
            9,
            foldl(
                function ($carry, $value, $key) {
                    return (second(explode(' ', $key)) % 2 == 0)
                        ? $carry + $value
                        : $carry;
                },
                0,
                self::$hashMap
            )
        );
    }

    public function testSetIsFoldable()
    {
        $result = foldl(
            function ($carry, $value) {
                return $carry + $value;
            },
            0,
            self::$set
        );

        $this->assertEquals(10, $result);
    }

    public function testIteratorIsFoldable()
    {
        $this->assertEquals(
            9,
            foldl(
                function ($carry, $value, $key) {
                    return $carry + ($key % 2 == 0 ? $value : 0);
                },
                0,
                self::$iterator
            )
        );
    }
}
