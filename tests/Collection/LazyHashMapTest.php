<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Collection;

use function IrRegular\Hopper\Collection\hash_map;
use IrRegular\Hopper\Collection\LazyHashMap;
use function IrRegular\Hopper\first;
use function IrRegular\Hopper\keys;
use function IrRegular\Hopper\second;
use function IrRegular\Hopper\values;
use IrRegular\Tests\Hopper\CollectionSetUpTrait;
use PHPUnit\Framework\TestCase;

class LazyHashMapTest extends TestCase
{
    use CollectionSetUpTrait;

    public function increment($x): int
    {
        return $x + 1;
    }

    public function testHashMapCreatesLazyHashMapFromGenerator()
    {
        $map = hash_map($this->generator());

        $this->assertInstanceOf(LazyHashMap::class, $map);
    }

    public function testLazyHashMapPreservesItems()
    {
        $map = hash_map($this->generator(self::$stringIndexedArray));

        $this->assertEquals(array_keys(self::$stringIndexedArray), keys($map));
        $this->assertEquals(array_values(self::$stringIndexedArray), values($map));
    }

    public function testGetCountRealisesEntireGenerator()
    {
        $generator = $this->generator(self::$stringIndexedArray);
        $map = hash_map($generator);
        $this->assertEquals(7, $map->getCount());
        $this->assertFalse($generator->valid());
    }

    public function testChecksIfGeneratorContainsElements()
    {
        $map = hash_map($this->generator());
        $this->assertTrue($map->isEmpty());

        $map = hash_map($this->generator(self::$stringIndexedArray));
        $this->assertFalse($map->isEmpty());
    }

    public function testGetIsLazy()
    {
        $generator = $this->generator(self::$stringIndexedArray);
        $map = hash_map($generator);

        $this->assertEquals(2, $map->get('key 1'));
        $this->assertTrue($generator->valid());
    }

    public function testMissingKeyHandledCorrectly()
    {
        $missingKey = 'nemo';
        $defaultValue = 'default';
        $hashMap = hash_map($this->generator());

        $this->assertFalse($hashMap->isKey($missingKey));
        $this->assertEquals($defaultValue, $hashMap->get($missingKey, $defaultValue));
    }

    public function testCachesGeneratorResults()
    {
        $hashMap = hash_map($this->generator(self::$stringIndexedArray));

        // we can advance generator to the last value...
        $this->assertEquals(['key 6', 4], $hashMap->last());
        // ...and still be able to access the previous value
        $this->assertEquals(['key 0', 1], $hashMap->first());
    }

    public function testFirstIsLazy()
    {
        $generator = $this->generator(self::$stringIndexedArray);
        $hashMap = hash_map($generator);

        $this->assertEquals(['key 0', 1], $hashMap->first());

        $this->assertTrue($generator->valid()); // and other elements still haven't been fetched
        $this->assertEquals('key 1', $generator->key());
    }

    public function testMapIsLazy()
    {
        $g = $this->generator(['one' => 1, 'two' => 2, 'three' => 3]);
        $hashMap = hash_map($g);
        $result = $hashMap->map([$this, 'increment']);

        $this->assertEquals(['one', 2], first($result));
        $this->assertEquals(['two', 3], second($result)); // @TODO this fails, interesting

        // $result never instantiates more elements of input generator
        // than necessary to generate results
        // @TODO (actually now it does and I don't know why)

        $this->assertTrue($g->valid());
    }

    public function testMapWorksOnRealisedLazyHashMap()
    {
        $hashMap = hash_map($this->generator(self::$stringIndexedArray));

        $hashMap->getCount(); // this'll realise the whole thing

        $result = $hashMap->map([$this, 'increment']);

        $this->assertEquals(['key 0', 2], first($result));
        $this->assertEquals(['key 1', 3], second($result)); // @TODO also fails
    }

    public function testRestReturnsLazyHashMap()
    {
        // ...and also that hashmap has its own, independent generator

        $hashMap = hash_map($this->generator(['one' => 1, 'two' => 2]));
        $rest = $hashMap->rest();

        $this->assertInstanceOf(LazyHashMap::class, $rest);
        $this->assertEquals([2], values($rest));
        $this->assertEquals([1, 2], $hashMap->getValues());
    }
}
