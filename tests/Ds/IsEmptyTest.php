<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\hash_map;
use function IrRegular\Hopper\set;
use function IrRegular\Hopper\vector;
use function IrRegular\Hopper\is_empty;
use PHPUnit\Framework\TestCase;

class IsEmptyTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanTestEmptiness()
    {
        $this->assertTrue(is_empty([]));
        $this->assertTrue(is_empty(hash_map([])));
        $this->assertTrue(is_empty(set([])));
        $this->assertTrue(is_empty(vector([])));

        $this->assertFalse(is_empty(self::$array));
        $this->assertFalse(is_empty(self::$hashMap));
        $this->assertFalse(is_empty(self::$set));
        $this->assertFalse(is_empty(self::$vector));
    }
}
