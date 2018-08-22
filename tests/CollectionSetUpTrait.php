<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use IrRegular\Hopper\Collection;

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

    /** @var Collection\Vector */
    private static $vector;

    /** @var Collection\Set */
    private static $set;

    /** @var Collection\HashMap */
    private static $hashMap;

    public static function setUpBeforeClass()
    {
        self::$array = [1, 2, 1, 4, 3, 1, 4];

        $keys = array_map('self::encodeKey', array_keys(self::$array));
        self::$stringIndexedArray = array_combine($keys, self::$array);

        self::$vector = Collection\vector(self::$array);

        // note that $array contains duplicates of the first and last element
        self::$set = Collection\set(self::$array);

        self::$hashMap = Collection\hash_map(self::$stringIndexedArray);

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
}
