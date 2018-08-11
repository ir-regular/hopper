<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Collection;

use function IrRegular\Hopper\Collection\hash_map;
use PHPUnit\Framework\TestCase;

class HashMapTest extends TestCase
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

        $this->assertEquals(6, $hashMap->getCount());
        $this->assertTrue($hashMap->isKey('Mary'));
        $this->assertEquals(1, $hashMap->get('Mary'));
    }

    public function testHashMapWithStringKeys()
    {
        $hashMap = hash_map(['one' => 'ichi', 'two' => 'ni']);

        $this->assertFalse($hashMap->isKey(0));
        $this->assertTrue($hashMap->isKey('one'));
        $this->assertEquals(['one', 'two'], $hashMap->getKeys());
        $this->assertEquals(['ichi', 'ni'], $hashMap->getValues());
        $this->assertEquals([['one', 'ichi'], ['two', 'ni']], iterator_to_array($hashMap->getIterator()));
    }

    public function testHashMapWithNumericStringKeys()
    {
        $hashMap = hash_map(['one', 'two'], ['1', '2']);

        // would be nice if preserving types worked, but PHP will auto-convert the indices.

        $this->assertFalse($hashMap->isKey(1));
        $this->assertTrue($hashMap->isKey('1'));
        $this->assertTrue(['1', '2'] === $hashMap->getKeys());
        $this->assertEquals(['one', 'two'], $hashMap->getValues());
        $this->assertTrue([['1', 'one'], ['2', 'two']] === iterator_to_array($hashMap->getIterator()));
        $this->assertTrue(['1', 'one'] === $hashMap->first());
        $this->assertTrue(['2', 'two'] === $hashMap->last());
    }

    public function testHashMapWithIntegerKeys()
    {
        $hashMap = hash_map([1 => 'one', 2 => 'two']);

        $this->assertFalse($hashMap->isKey(0));
        $this->assertTrue($hashMap->isKey(1));
        $this->assertFalse($hashMap->isKey('1'));
        $this->assertTrue([1, 2] === $hashMap->getKeys());
        $this->assertEquals(['one', 'two'], $hashMap->getValues());
        $this->assertTrue([[1, 'one'], [2, 'two']] === iterator_to_array($hashMap->getIterator()));
        $this->assertTrue([1, 'one'] === $hashMap->first());
        $this->assertTrue([2, 'two'] === $hashMap->last());
    }
}
