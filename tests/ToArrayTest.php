<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\to_array;
use PHPUnit\Framework\TestCase;

class ToArrayTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testArrayReturnedAsIs()
    {
        $this->assertEquals(self::$array, to_array(self::$array));
        $this->assertEquals(self::$nestedArray, to_array(self::$nestedArray));
    }

    public function testIteratorConvertedToArray()
    {
        $this->assertEquals(self::$array, to_array(self::$iterator));
    }

    public function testGeneratorConvertedToArray()
    {
        $g = self::generator(self::$array);
        $this->assertEquals(self::$array, to_array($g));
    }

    public function testVectorConvertedToArray()
    {
        $this->assertEquals(self::$array, to_array(self::$vector));
    }

    public function testSetConvertedToArray()
    {
        $result = to_array(self::$set);

        foreach (self::$array as $value) {
            $this->assertTrue(in_array($value, $result));
        }
    }

    public function testHashMapConvertedToArray()
    {
        $result = to_array(self::$hashMap);

        foreach (self::$stringIndexedArray as $key => $value) {
            $this->assertTrue(in_array([$value, $key], $result));
        }
    }
}
