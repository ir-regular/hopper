<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\Collection;
use IrRegular\Hopper\Collection\HashMap\Lazy;
use function IrRegular\Hopper\Collection\HashMap\convert_to_key;
use function IrRegular\Hopper\Collection\HashMap\is_valid_key;
use IrRegular\Hopper\Ds\Vector;
use IrRegular\Hopper\Ds\Vector\Eager;
use IrRegular\Hopper\Foldable;
use IrRegular\Hopper\Indexed;
use IrRegular\Hopper\Lazy as LazyInterface;
use IrRegular\Hopper\Sequence;
use IrRegular\Hopper\Mappable;
use function IrRegular\Hopper\Collection\size;

class HashMap implements Collection, Indexed, Foldable, Mappable
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

    public function map(callable $closure): LazyInterface
    {
        $generator = (function () use ($closure) {
            foreach ($this->getValueKeyPairList() as $pair) {
                yield $pair[1] => $closure(...$pair);
            }
        })();

        return new Lazy($generator);
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
        if (!is_valid_key($key)) {
            $safeKey = convert_to_key($key);
            $key = $this->index[$safeKey] ?? null;
        }

        return $this->array[$key] ?? $default;
    }

    public function isKey($key): bool
    {
        if (!is_valid_key($key)) {
            $key = convert_to_key($key);
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
        return new Eager(array_map(null, $this->index, $this->array));
    }
}
