<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\keys;
use function IrRegular\Hopper\to_array;
use PHPUnit\Framework\TestCase;

class KeysTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanGetAllCollectionKeys()
    {
        $arrayIndices = range(0, 6);
        $hashMapKeys = array_map('self::encodeKey', $arrayIndices);

        $this->assertEquals($arrayIndices, to_array(keys(self::$array)));
        $this->assertEquals($arrayIndices, to_array(keys(self::$vector)));
        $this->assertEquals($hashMapKeys, to_array(keys(self::$hashMap)));
        $this->assertEquals([0, 1, 2, 3], to_array(keys(self::$set)));
    }
}
