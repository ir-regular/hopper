<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Ds\HashMap;

use function IrRegular\Hopper\hash_map;
use PHPUnit\Framework\TestCase;

class EagerTest extends TestCase
{
    public function testCanCreateHashMapFromIterator()
    {
        $iterator = new \ArrayIterator([
            'Mary' => 1,
            'Joe' => 2,
            'Jane' => 3,
            'Andy' => 1,
            'Greta' => 2,
            'Josh' => 3
        ]);
        $hashMap = hash_map($iterator);

        $this->assertEquals(6, $hashMap->count());
        $this->assertTrue($hashMap->isKey('Mary'));
        $this->assertEquals(1, $hashMap->get('Mary'));
    }

    public function testHashMapWithStringKeys()
    {
        $array = ['one' => 'ichi', 'two' => 'ni'];
        $hashMap = hash_map($array);

        $this->assertFalse($hashMap->isKey(0));
        $this->assertTrue($hashMap->isKey('one'));
        $this->assertEquals(array_keys($array), $hashMap->getKeys());
        $this->assertEquals(array_values($array), $hashMap->getValues());
        $this->assertEquals($array, iterator_to_array($hashMap->getIterator()));
    }

    public function testHashMapWithNumericStringKeys()
    {
        $values = ['one', 'two'];
        $keys = ['1', '2'];
        $hashMap = hash_map($values, $keys);

        // would be nice if preserving types worked, but PHP will auto-convert the indices.

        $this->assertFalse($hashMap->isKey(1));
        $this->assertTrue($hashMap->isKey('1'));
        $this->assertTrue($keys === $hashMap->getKeys());
        $this->assertEquals($values, $hashMap->getValues());
        $this->assertTrue([['1', 'one'], ['2', 'two']] === $hashMap->toVector()->getValues());
    }

    public function testHashMapWithIntegerKeys()
    {
        $hashMap = hash_map([1 => 'one', 2 => 'two']);

        $this->assertFalse($hashMap->isKey(0));
        $this->assertTrue($hashMap->isKey(1));
        $this->assertFalse($hashMap->isKey('1'));
        $this->assertTrue([1, 2] === $hashMap->getKeys());
        $this->assertEquals(['one', 'two'], $hashMap->getValues());
        $this->assertTrue([[1, 'one'], [2, 'two']] === $hashMap->toVector()->getValues());
    }
}
