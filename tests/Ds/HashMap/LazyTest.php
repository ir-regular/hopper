<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Ds\HashMap;

use IrRegular\Hopper\Ds\HashMap\Lazy;
use function IrRegular\Hopper\hash_map;
use function IrRegular\Hopper\keys;
use function IrRegular\Hopper\values;
use IrRegular\Hopper\Ds\HashMap\Lazy as LazyHashMap;
use IrRegular\Tests\Hopper\CollectionSetUpTrait;
use PHPUnit\Framework\TestCase;

class LazyTest extends TestCase
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
        $this->assertEquals(7, $map->count());
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
        $this->assertEquals(4, $hashMap->get('key 6'));
        // ...and still be able to access the previous value
        $this->assertEquals(1, $hashMap->get('key 0'));
    }

    public function testLMapIsLazy()
    {
        $g = $this->generator(['one' => 1, 'two' => 2, 'three' => 3]);
        $hashMap = hash_map($g);

        /** @var Lazy $result */
        $result = $hashMap->lMap([$this, 'increment']);

        $this->assertEquals(2, $result->get('one'));
        $this->assertEquals(3, $result->get('two'));

        $this->assertTrue($g->valid());
    }

    public function testLMapWorksOnRealisedLazyHashMap()
    {
        $hashMap = hash_map($this->generator(self::$stringIndexedArray));

        $hashMap->count(); // this'll realise the whole thing

        /** @var Lazy $result */
        $result = $hashMap->lMap([$this, 'increment']);

        $this->assertEquals(2, $result->get('key 0'));
        $this->assertEquals(3, $result->get('key 1'));
    }

    public function testLMapOnMapIndexedByObjects()
    {
        $o1 = (object) ['name' => 'Remy'];
        $o2 = (object) ['name' => 'Emile'];

        $keys = [$o1, $o2];
        $values = ['Chef', 'Support'];

        $eager = hash_map($values, $keys);

        /** @var Lazy $lazy */
        $lazy = $eager->lMap(function ($role, $rat) {
            return "{$rat->name} is a $role";
        });

        $this->assertEquals("Remy is a Chef", $lazy->get($o1));
        $this->assertEquals($keys, $lazy->getKeys());
    }
}
