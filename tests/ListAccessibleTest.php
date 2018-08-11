<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\Collection\vector;
use function IrRegular\Hopper\Collection\hash_map;
use function IrRegular\Hopper\first;
use function IrRegular\Hopper\last;
use function IrRegular\Hopper\rest;
use function IrRegular\Hopper\second;
use PHPUnit\Framework\TestCase;

class ListAccessibleTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanGetFirstElement()
    {
        $this->assertEquals(1, first(self::$array));
        $this->assertEquals(1, first(self::$vector));
        $this->assertEquals(['key 0', 1], first(self::$hashMap));
    }

    public function testCanGetSecondElement()
    {
        $this->assertEquals(2, second(self::$array));
        $this->assertEquals(2, second(self::$vector));
        $this->assertEquals(['key 1', 2], second(self::$hashMap));
    }

    public function testCanGetLastElement()
    {
        $this->assertEquals(4, last(self::$array));
        $this->assertEquals(4, last(self::$vector));
        $this->assertEquals(['key 6', 4], last(self::$hashMap));
    }

    public function testCanGetRestOfElements()
    {
        $this->assertEquals(
            [2, 1, 4, 3, 1, 4],
            rest(self::$array)
        );

        $this->assertEquals(
            vector([2, 1, 4, 3, 1, 4]),
            rest(self::$vector)
        );

        $this->assertEquals(
            hash_map([
                'key 1' => 2,
                'key 2' => 1,
                'key 3' => 4,
                'key 4' => 3,
                'key 5' => 1,
                'key 6' => 4,
            ]),
            rest(self::$hashMap)
        );
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCannotGetFirstElementOfSet()
    {
        first(self::$set);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCannotGetLastElementOfSet()
    {
        last(self::$set);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCannotGetAllButFirstElementsOfSet()
    {
        rest(self::$set);
    }
}
