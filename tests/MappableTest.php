<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\Collection\hash_map;
use function IrRegular\Hopper\Collection\vector;
use function IrRegular\Hopper\keys;
use function IrRegular\Hopper\map;
use PHPUnit\Framework\TestCase;

class MappableTest extends TestCase
{
    use CollectionSetUpTrait;

    public function increment(int $value): int
    {
        return $value + 1;
    }

    public function testArrayIsMappable()
    {
        $this->assertEquals(
            vector([2, 3, 2, 5, 4, 2, 5]),
            map([$this, 'increment'], self::$array)
        );
    }

    public function testMappingOverStringIndexedArrayPreservesKeys()
    {
        $result = map('\IrRegular\Hopper\identity', self::$stringIndexedArray);

        $this->assertEquals(
            array_keys(self::$stringIndexedArray),
            keys($result)
        );
    }

    public function testVectorIsMappable()
    {
        $this->assertEquals(
            vector([2, 3, 2, 5, 4, 2, 5]),
            map([$this, 'increment'], self::$vector)
        );
    }

    public function testHashMapIsMappable()
    {
        $this->assertEquals(
            hash_map([
                'key 0' => 2,
                'key 1' => 3,
                'key 2' => 2,
                'key 3' => 5,
                'key 4' => 4,
                'key 5' => 2,
                'key 6' => 5
            ]),
            map([$this, 'increment'], self::$hashMap)
        );
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testSetIsNotMappable()
    {
        // note that this only throws after the generator has been first accessed
        // thus the need for `iterator_to_array`
        map([$this, 'increment'], self::$set);
    }
}
