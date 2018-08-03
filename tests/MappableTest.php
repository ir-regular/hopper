<?php

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\compose;
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
            [2, 3, 2, 5, 4, 2, 5],
            iterator_to_array(map([$this, 'increment'], self::$array))
        );
    }

    public function testVectorIsMappable()
    {
        $this->assertEquals(
            [2, 3, 2, 5, 4, 2, 5],
            iterator_to_array(map([$this, 'increment'], self::$vector))
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
            iterator_to_array(map(
                compose(
                    'IrRegular\Hopper\second',
                    [$this, 'increment']
                ),
                self::$hashMap
            ))
        );
    }
}
