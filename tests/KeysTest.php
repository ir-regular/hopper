<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\keys;
use PHPUnit\Framework\TestCase;

class KeysTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanGetAllCollectionKeys()
    {
        $arrayIndices = range(0, 6);
        $hashMapKeys = array_map('self::encodeKey', $arrayIndices);

        $this->assertEquals($arrayIndices, keys(self::$array));
        $this->assertEquals($arrayIndices, keys(self::$vector));
        $this->assertEquals($hashMapKeys, keys(self::$hashMap));
        // @TODO: set is not Indexed, currently this falls back to iterator
        $this->assertEquals([0, 1, 2, 3], keys(self::$set));
    }
}
