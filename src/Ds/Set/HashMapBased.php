<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\Set;

use function IrRegular\Hopper\hash_map;
use function IrRegular\Hopper\size;
use IrRegular\Hopper\Ds\HashMap;
use IrRegular\Hopper\Ds\Lazy;
use IrRegular\Hopper\Ds\Mappable;
use IrRegular\Hopper\Ds\Set as SetInterface;
use IrRegular\Hopper\Ds\Vector;
use IrRegular\Hopper\Ds\Vector\Eager;

class HashMapBased implements SetInterface
{
    /**
     * @var HashMap
     */
    protected $hashMap;

    public function __construct(iterable $collection)
    {
        $count = size($collection);
        $values = array_fill(0, $count, true);

        $this->hashMap = hash_map($values, $collection);
    }

    // Collection

    public function isEmpty(): bool
    {
        return $this->hashMap->isEmpty();
    }

    public function count(): int
    {
        return $this->hashMap->count();
    }

    public function contains($value): bool
    {
        return $this->hashMap->isKey($value);
    }

    public function getValues(): iterable
    {
        return $this->hashMap->getKeys();
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->hashMap->getKeys());
    }

    // Foldable

    public function foldl(callable $closure, $initialValue)
    {
        return array_reduce($this->hashMap->getKeys(), $closure, $initialValue);
    }

    public function foldr(callable $closure, $initialValue)
    {
        return array_reduce(array_reverse($this->hashMap->getKeys()), $closure, $initialValue);
    }

    // Mappable

    public function map(callable $closure): Mappable
    {
        $newValues = array_map($closure, $this->hashMap->getKeys());

        return new self($newValues);
    }

    public function lMap(callable $closure): Lazy
    {
        $generator = (function () use ($closure) {
            foreach ($this->getIterator() as $element) {
                yield $closure($element);
            }
        })();

        return new Vector\Lazy($generator);
    }

    // Set

    public function toVector(): Vector
    {
        return new Eager($this->hashMap->getKeys());
    }
}
