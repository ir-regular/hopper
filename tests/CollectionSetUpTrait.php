<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use IrRegular\Hopper\Collection;

/**
 * @TODO iterator
 */
trait CollectionSetUpTrait
{
    /** @var array */
    private static $array;

    /** @var Collection\Vector */
    private static $vector;

    /** @var Collection\Set */
    private static $set;

    /** @var Collection\HashMap */
    private static $hashMap;

    public static function setUpBeforeClass()
    {
        self::$array = [1, 2, 1, 4, 3, 1, 4];

        self::$vector = new Collection\Vector(self::$array);

        // note that $array contains duplicates of the first and last element
        self::$set = new Collection\Set(self::$array);

        $keys = array_map('self::encodeKey', array_keys(self::$array));
        self::$hashMap = new Collection\HashMap(array_combine($keys, self::$array));
    }

    protected static function encodeKey($value): string
    {
        return "key $value";
    }
}
