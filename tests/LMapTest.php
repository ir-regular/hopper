<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\first;
use function IrRegular\Hopper\keys;
use function IrRegular\Hopper\lmap;
use function IrRegular\Hopper\second;
use function IrRegular\Hopper\values;
use PHPUnit\Framework\TestCase;

class LMapTest extends TestCase
{
    use CollectionSetUpTrait;

    public function increment(int $value): int
    {
        return $value + 1;
    }

    public function testArrayIsMappable()
    {
        $this->assertEquals(
            [2, 3, 2, 5, 4, 2, 5],
            values(lmap([$this, 'increment'], self::$array))
        );
    }

    public function testMappingOverStringIndexedArrayPreservesKeys()
    {
        $result = lmap('\IrRegular\Hopper\identity', self::$stringIndexedArray);

        $this->assertEquals(
            array_keys(self::$stringIndexedArray),
            keys($result)
        );
    }

    public function testVectorIsMappable()
    {
        $this->assertEquals(
            [2, 3, 2, 5, 4, 2, 5],
            values(lmap([$this, 'increment'], self::$vector))
        );
    }

    public function testHashMapIsMappable()
    {
        $this->assertEquals(
            [
                'key 0' => 2,
                'key 1' => 3,
                'key 2' => 2,
                'key 3' => 5,
                'key 4' => 4,
                'key 5' => 2,
                'key 6' => 5
            ],
            iterator_to_array(lmap([$this, 'increment'], self::$hashMap))
        );
    }

    public function testSetIsMappable()
    {
        // currently, falls back to iterator

        $result = values(lmap([$this, 'increment'], self::$set));
        $this->assertEquals([2, 3, 5, 4], $result);
    }

    public function testMapOnGeneratorIsLazy()
    {
        $generator = $this->generator(self::$array);

        $result = lmap([$this, 'increment'], $generator);
        // consume and increment the first element in generator
        $firstIncremented = first($result);

        // confirm we got the correct element (used to be 1, after increment is 2)
        $this->assertEquals(2, $firstIncremented);
        // ...and generator still has more elements
        $this->assertTrue($generator->valid());
        // ...and only the first element has been realised, no need to move the generator forward
        $this->assertEquals(1, $generator->current());

        // ...but after we retrieve the second element of sequence
        $this->assertEquals(3, second($result));
        // ...now the current element available is 2; only consume as many elements as neceesary!
        $this->assertEquals(2, $generator->current());
    }
}
