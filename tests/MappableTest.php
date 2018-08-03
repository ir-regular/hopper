<?php

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\map;
use PHPUnit\Framework\TestCase;

class MappableTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testArrayIsMappable()
    {
        $this->assertEquals(
            [2, 3, 2, 5, 4, 2, 5],
            iterator_to_array(map('IrRegular\Tests\Hopper\inc', self::$array))
        );
    }

    public function testVectorIsMappable()
    {
        $this->assertEquals(
            [2, 3, 2, 5, 4, 2, 5],
            iterator_to_array(map('IrRegular\Tests\Hopper\inc', self::$vector))
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
            // why yes, I'm showing off; don't worry, these fns will eventually be upgraded to proper library fns
            iterator_to_array(map(
                compose(
                    'IrRegular\Tests\Hopper\second',
                    'IrRegular\Tests\Hopper\inc'
                ),
                self::$hashMap
            ))
        );
    }
}
