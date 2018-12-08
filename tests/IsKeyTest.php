<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\is_key;
use PHPUnit\Framework\TestCase;

class IsKeyTest extends TestCase
{
    use CollectionSetUpTrait;

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
}
