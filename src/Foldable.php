<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

interface Foldable
{
    public function foldl(callable $closure, $initialValue);

    public function foldr(callable $closure, $initialValue);
}

/**
 * Left-fold/reduce function, eager.
 *
 * @param callable $closure
 * @param mixed $initialValue
 * @param iterable $collection
 * @return mixed
 */
function foldl(callable $closure, $initialValue, iterable $collection)
{
    if ($collection instanceof Foldable) {
        return $collection->foldl($closure, $initialValue);
    } elseif (is_array($collection)) {
        return array_reduce($collection, $closure, $initialValue);
    } else {
        assert($collection instanceof \Traversable);
        $carry = $initialValue;
        foreach ($collection as $key => $element) {
            $carry = $closure($carry, $element, $key);
        }
        return $carry;
    }
}

/**
 * Left-fold/reduce function, uses the first element (or key-value pair) of $collection as initial value.
 *
 * @param callable $closure
 * @param iterable $collection
 * @return mixed
 */
function foldl1(callable $closure, iterable $collection)
{
    return foldl($closure, first($collection), rest($collection));
}

/**
 * Right-fold function, eager.
 *
 * @param callable $closure
 * @param mixed $initialValue
 * @param iterable $collection
 * @return mixed
 */
function foldr(callable $closure, $initialValue, iterable $collection)
{
    if ($collection instanceof Foldable) {
        return $collection->foldr($closure, $initialValue);
    } elseif (is_array($collection)) {
        return array_reduce(array_reverse($collection), $closure, $initialValue);
    } else {
        assert($collection instanceof \Traversable);

        $realisedCollection = [];

        foreach ($collection as $key => $element) {
            $realisedCollection[$key] = $element;
        }

        return array_reduce(array_reverse($realisedCollection), $closure, $initialValue);
    }
}

/**
 * Right-fold function, uses the last element (or key-value pair) of $collection as initial value.
 *
 * @param callable $closure
 * @param iterable $collection
 * @return mixed
 */
function foldr1(callable $closure, iterable $collection)
{
    if (!$collection instanceof Foldable && !is_array($collection)) {
        $realisedCollection = [];

        foreach ($collection as $key => $element) {
            $realisedCollection[$key] = $element;
        }
    }

    return foldr($closure, last($collection), rest($collection));
}
