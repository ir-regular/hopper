<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\last;
use PHPUnit\Framework\TestCase;

class LastTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testCanGetLastElement()
    {
        $this->assertEquals(4, last(self::$array));
        $this->assertEquals(4, last(self::$vector));

        // @TODO: Not sequences, takes the last element of fallback iterator
        $this->assertEquals([4, 'key 6'], last(self::$hashMap));
        // it's 3, not 4, because the last 4 is a duplicate and thus not added
        $this->assertEquals(3, last(self::$set));
    }
}
