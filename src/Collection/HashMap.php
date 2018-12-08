<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\Collection;
use IrRegular\Hopper\Foldable;
use IrRegular\Hopper\Indexable;
use IrRegular\Hopper\Lazy;
use IrRegular\Hopper\Sequence;
use IrRegular\Hopper\Mappable;
use function IrRegular\Hopper\size;

class HashMap implements Collection, Sequence, Indexable, Mappable, Foldable
{
    /**
     * @var array
     */
    protected $index = [];

    /**
     * @var array
     */
    protected $array = [];

    public function __construct(array $collection, array $stringIndex)
    {
        $this->array = $collection;
        $this->index = $stringIndex;
    }

    public function foldl(callable $closure, $initialValue)
    {
        return array_reduce(
            $this->getValueKeyPairList(),
            function ($carry, $pair) use ($closure) {
                return $closure($carry, ...$pair);
            },
            $initialValue
        );
    }

    public function foldr(callable $closure, $initialValue)
    {
        return array_reduce(
            array_reverse($this->getValueKeyPairList()),
            function ($carry, $pair) use ($closure) {
                return $closure($carry, ...$pair);
            },
            $initialValue
        );
    }

    public function map(callable $closure): Lazy
    {
        $generator = (function () use ($closure) {
            foreach ($this->getValueKeyPairList() as $pair) {
                yield $pair[1] => $closure(...$pair);
            }
        })();

        return new LazyHashMap($generator);
    }

    public function isEmpty(): bool
    {
        return empty($this->array);
    }

    public function getCount(): int
    {
        return count($this->array);
    }

    public function getValues(): iterable
    {
        return array_values($this->array);
    }

    public function first()
    {
        assert(!empty($this->array));

        $safeKey = array_keys($this->index)[0];
        // $safeKey = array_key_first($this->index); // PHP 7.3
        $key = $this->index[$safeKey];
        return [$key, $this->array[$key]];
    }

    public function last()
    {
        assert(!empty($this->array));

        $safeKey = array_keys($this->index)[count($this->index) - 1];
        // $key = array_key_last($this->index); // PHP 7.3
        $key = $this->index[$safeKey];
        return [$key, $this->array[$key]];
    }

    public function rest(): Sequence
    {
        // Amazingly, array_slice _does_ work on arrays with string keys. IKR?!

        $rest = new HashMap(
            array_slice($this->array, 1),
            array_slice($this->index, 1)
        );

        return $rest;
    }

    public function get($key, $default = null)
    {
        if (!is_valid_hash_map_key($key)) {
            $safeKey = convert_to_valid_hash_map_key($key);
            $key = $this->index[$safeKey] ?? null;
        }

        return $this->array[$key] ?? $default;
    }

    public function isKey($key): bool
    {
        if (!is_valid_hash_map_key($key)) {
            $key = convert_to_valid_hash_map_key($key);
            return array_key_exists($key, $this->index);
        } else {
            return array_key_exists($key, $this->array);
        }
    }

    public function getKeys(): iterable
    {
        return array_values($this->index);
    }

    /**
     * Returns an (eagerly generated) array of [value, key] pairs of original array.
     *
     * @return array
     */
    protected function getValueKeyPairList(): array
    {
        return array_map(null, $this->array, $this->index);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }

    public function toVector(): Vector
    {
        return new Vector(array_map(null, $this->index, $this->array));
    }
}

/**
 * If you use numerical strings as indices, PHP will convert them to ints
 * before handing it over to this function. So, if you really want to have keys like '1', '2', ...
 * you need to supply the keys in the optional $keys argument
 *
 * @param iterable $collection
 * @param iterable|null $keys Keys of the correct, un-cast-by-PHP type.
 * @return HashMap
 */
function hash_map(iterable $collection, iterable $keys = null)
{
    if ($collection instanceof \Generator) {
        return new LazyHashMap($collection);
    }

    if ($collection instanceof \Traversable) {
        $collection = iterator_to_array($collection, true);
    }

    if (empty($keys)) {
        $keys = array_keys($collection);
    } else {
        assert(size($keys) == size($collection));
    }

    // ensure you can perform operations on string keys
    // (but also preserve the original keys with appropriate types)

    $values = [];
    $stringIndex = [];

    foreach ($collection as $value) {
        $originalKey = current($keys);
        $safeKey = is_valid_hash_map_key($originalKey)
            ? $originalKey
            : convert_to_valid_hash_map_key($originalKey);

        $stringIndex[$safeKey] = $originalKey;
        // why not just supply $collection instead of rebuilding it as $values?
        // because you need to be able to reindex using $keys
        $values[$originalKey] = $value;
        next($keys);
    }

    return new HashMap($values, $stringIndex);
}
