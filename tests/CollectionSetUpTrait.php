<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use IrRegular\Hopper\Ds\HashMap;
use IrRegular\Hopper\Ds\Set;
use IrRegular\Hopper\Ds\Vector;
use function IrRegular\Hopper\hash_map;
use function IrRegular\Hopper\set;
use function IrRegular\Hopper\vector;

trait CollectionSetUpTrait
{
    /** @var array */
    private static $array;

    /** @var array */
    private static $stringIndexedArray;

    /** @var \Iterator */
    private static $iterator;

    /** @var array */
    private static $nestedArray;

    /** @var Vector */
    private static $vector;

    /** @var Set */
    private static $set;

    /** @var HashMap */
    private static $hashMap;

    public static function setUpBeforeClass()
    {
        self::$array = [1, 2, 1, 4, 3, 1, 4];

        $keys = array_map('self::encodeKey', array_keys(self::$array));
        self::$stringIndexedArray = array_combine($keys, self::$array);

        self::$vector = vector(self::$array);

        // note that $array contains duplicates of the first and last element
        self::$set = set(self::$array);

        self::$hashMap = hash_map(self::$stringIndexedArray);

        self::$nestedArray = [
            ['name' => 'John', 'address' => ['city' => 'New York']],
            ['name' => 'Jane', 'address' => ['city' => 'London']],
            ['name' => 'Sam', 'address' => ['city' => 'Toronto', 'country' => 'Canada']],
            ['name' => 'Alicia']
        ];

        self::$iterator = new \ArrayIterator(self::$array);
    }

    protected static function encodeKey($value): string
    {
        return "key $value";
    }

    /**
     * Helper function, returns a customised generator for tests.
     *
     * @param array $array
     * @return \Generator
     */
    protected function generator(array $array = []): \Generator
    {
        yield from $array;
    }
}
