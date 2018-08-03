<?php

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

function second(array $value)
{
    $key = array_keys($value)[1];
    return $value[$key];
}

function compose(callable ...$functions): callable
{
    return function ($x) use ($functions) {
        foreach ($functions as $f) {
            $x = $f($x);
        }
        return $x;
    };
}

function partial(callable $function, ...$operands): callable
{
    return function (...$moreOperands) use ($function, $operands) {
        return $function(...$operands, ...$moreOperands);
    };
}

function apply(callable $function, ...$operands)
{
    return $function($operands);
}


function inc(int $value): int
{
    return $value + 1;
}
