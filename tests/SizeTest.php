<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\hash_map;
use function IrRegular\Hopper\set;
use function IrRegular\Hopper\vector;
use function IrRegular\Hopper\size;
use PHPUnit\Framework\TestCase;

class SizeTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanTestElementCount()
    {
        $this->assertEquals(0, size([]));
        $this->assertEquals(0, size(hash_map([])));
        $this->assertEquals(0, size(set([])));
        $this->assertEquals(0, size(vector([])));

        $this->assertEquals(7, size(self::$array));
        $this->assertEquals(7, size(self::$hashMap));
        $this->assertEquals(7, size(self::$vector));
        // set removes duplicates
        $this->assertEquals(4, size(self::$set));
    }
}
