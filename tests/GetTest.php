<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\get;
use PHPUnit\Framework\TestCase;

class GetTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanRetrieveUsingKey()
    {
        $this->assertEquals(1, get(self::$array, 0)); // array contains 0th element
        $this->assertEquals(1, get(self::$vector, 0)); // array contains 0th element
        $this->assertEquals(2, get(self::$hashMap, self::encodeKey(1))); // hash-map contains key "key 1"

        // @TODO: Set is not Indexed, so get currently grabs its iterator
        // and returns whatever element is at index 1.
        // This may not be the best solution for future, but eh :)
        $this->assertEquals(2, get(self::$set, 1));

        $default = 'x';

        $this->assertEquals($default, get(self::$array, 7, $default)); // array contains less than 8 elements
        $this->assertEquals($default, get(self::$vector, 7, $default)); // vector contains less than 8 elements
        $this->assertEquals($default, get(self::$set, 6, $default)); // set does not contain element 6
        // hash-map does not contain key "key 7"
        $this->assertEquals($default, get(self::$hashMap, self::encodeKey(7), $default));
    }
}
