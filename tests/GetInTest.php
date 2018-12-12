<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\get_in;
use function IrRegular\Hopper\map;
use function IrRegular\Hopper\partial_first;
use PHPUnit\Framework\TestCase;

class GetInTest extends TestCase
{
    use CollectionSetUpTrait;

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
            map($getCity, self::$nestedArray)
        );
    }
}
