<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\get;
use function IrRegular\Hopper\get_in;
use function IrRegular\Hopper\is_key;
use function IrRegular\Hopper\keys;
use function IrRegular\Hopper\map;
use function IrRegular\Hopper\partial_first;
use function IrRegular\Hopper\values;
use PHPUnit\Framework\TestCase;

class IndexableTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanGetAllCollectionKeys()
    {
        $arrayIndices = range(0, 6);
        $hashMapKeys = array_map('self::encodeKey', $arrayIndices);

        $this->assertEquals($arrayIndices, keys(self::$array));
        $this->assertEquals($arrayIndices, keys(self::$vector));
        $this->assertEquals($hashMapKeys, keys(self::$hashMap));
        $this->assertEquals([1, 2, 4, 3], keys(self::$set));
    }

    public function testCanCheckCollectionContainsKey()
    {
        $this->assertTrue(is_key(self::$array, 0)); // array contains 0th element
        $this->assertTrue(is_key(self::$vector, 0)); // array contains 0th element
        $this->assertTrue(is_key(self::$set, 1)); // set contains element 1
        $this->assertTrue(is_key(self::$hashMap, self::encodeKey(1))); // hash-map contains key "key 1"

        $this->assertFalse(is_key(self::$array, 7)); // array contains less than 8 elements
        $this->assertFalse(is_key(self::$vector, 7)); // vector contains less than 8 elements
        $this->assertFalse(is_key(self::$set, 6)); // set does not contain element 6
        $this->assertFalse(is_key(self::$hashMap, self::encodeKey(7))); // hash-map does not contain key "key 7"
    }

    public function testCanRetrieveUsingKey()
    {
        $this->assertEquals(1, get(self::$array, 0)); // array contains 0th element
        $this->assertEquals(1, get(self::$vector, 0)); // array contains 0th element
        $this->assertEquals(1, get(self::$set, 1)); // set contains element 1
        $this->assertEquals(2, get(self::$hashMap, self::encodeKey(1))); // hash-map contains key "key 1"

        $default = 'x';

        $this->assertEquals($default, get(self::$array, 7, $default)); // array contains less than 8 elements
        $this->assertEquals($default, get(self::$vector, 7, $default)); // vector contains less than 8 elements
        $this->assertEquals($default, get(self::$set, 6, $default)); // set does not contain element 6
        $this->assertEquals($default, get(self::$hashMap, self::encodeKey(7), $default)); // hash-map does not contain key "key 7"
    }

    public function testCanGetInUsingPrecisePath()
    {
        $this->assertEquals('Toronto', get_in(self::$nestedArray, [2, 'address', 'city']));
    }

    public function testWhenPathFailsGetInReturnsDefault()
    {
        $default = 'Unknown';
        $this->assertEquals($default, get_in(self::$nestedArray, [3, 'address', 'city'], $default));
    }

    public function testCanGetNestedForEveryElement()
    {
        $getCity = partial_first('IrRegular\Hopper\get_in', ['address', 'city'], 'Unknown');

        $this->assertEquals(
            ['New York', 'London', 'Toronto', 'Unknown'],
            values(map($getCity, self::$nestedArray))
        );
    }
}
