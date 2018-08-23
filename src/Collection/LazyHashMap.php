<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\ListAccessible;

class LazyHashMap extends HashMap
{
    /**
     * @var \Generator
     */
    protected $lazyTail;

    public function __construct(\Generator $lazyTail)
    {
        parent::__construct([], []);

        $this->lazyTail = $lazyTail;
    }

    public function foldl(callable $closure, $initialValue)
    {
        // why not call realise() and parent::foldl() ?
        // I can likely save on memory by realising elements one by one, and then immediately squashing each into $carry

        $carry = $initialValue;

        foreach ($this->getIterator() as $key => $value) {
            $carry = $closure($carry, $value, $key);
        }

        return $carry;
    }

    public function foldr(callable $closure, $initialValue)
    {
        $this->realise();
        return parent::foldr($closure, $initialValue);
    }

    public function map(callable $closure): \Generator
    {
        foreach ($this->getIterator() as $key => $item) {
            yield $key => $closure($item, $key);
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->array) && !$this->lazyTail->valid();
    }

    public function getCount(): int
    {
        $this->realise();
        return parent::getCount();
    }

    public function getValues(): iterable
    {
        $this->realise();
        return parent::getValues();
    }

    public function first()
    {
        $this->ensureRealisedAtLeast(1);
        return parent::first();
    }

    public function last()
    {
        $this->realise();
        return parent::last();
    }

    public function rest(): ListAccessible
    {
        $generator = $this->getIterator();
        $generator->next(); // skip the first item

        return new LazyHashMap($generator);
    }

    public function get($key, $default = null)
    {
        $safeKey = is_valid_hash_map_key($key)
            ? $key
            : convert_to_valid_hash_map_key($key);

        $this->realiseUpTo($safeKey);

        return parent::get($key, $default);
    }

    public function isKey($key): bool
    {
        $safeKey = is_valid_hash_map_key($key)
            ? $key
            : convert_to_valid_hash_map_key($key);

        $this->realiseUpTo($safeKey);

        return parent::isKey($key);
    }

    public function getKeys(): iterable
    {
        $this->realise();
        return parent::getKeys();
    }

    public function getIterator()
    {
        $elementCount = 1;

        // Why did I use this loop condition, rather than just foreach or something?
        // It's because this is a generator. By yielding, we're gonna lose control over the state of
        // $this->lazyTail and $this->array and some other code might advance them in the meantime.
        // So we always need to re-check how many elements $this->array has after we regain control.

        while ((count($this->array) > $elementCount) // => the element has been already realised
            || ($this->ensureRealisedAtLeast($elementCount) > 0) // => or we realised at least 1 element
        ) {
            $item = array_slice($this->array, $elementCount - 1, 1);
            yield key($item) => current($item);
            $elementCount++;
        }
    }

    /**
     * Realise all remaining elements of generator.
     *
     * @return int How many elements have been realised during this call.
     */
    protected function realise(): int
    {
        return $this->realiseUntil(function () {
            return false;
        });
    }

    /**
     * Ensure at least first N elements of generator have been realised (where N > 0).
     *
     * @param int $elementCount
     * @return int How many elements have been realised during this call.
     */
    protected function ensureRealisedAtLeast(int $elementCount): int
    {
        assert($elementCount > 0);
        $elementsToAdd = $elementCount - count($this->array);

        return $this->realiseUntil(function () use ($elementsToAdd) {
            static $elementsAdded = 0;
            return ($elementsAdded++ === $elementsToAdd); // increment _after_; we don't want to stop on 0th call
        });
    }

    /**
     * Realise elements of generator until you encounter one with key equal to $safeLookupKey.
     *
     * @param string $safeLookupKey
     * @return int How many elements have been realised during this call.
     */
    protected function realiseUpTo(string $safeLookupKey): int
    {
        return $this->realiseUntil(function ($safeKey) use ($safeLookupKey) {
            return ($safeKey !== null) && ($safeKey === $safeLookupKey);
        });
    }

    /**
     * Realise and cache elements of generator until $predicate($safeKey) returns true, or until generator runs out.
     *
     * @param callable $isDone
     * @return int How many elements have been realised during this call.
     */
    protected function realiseUntil(callable $isDone): int
    {
        $safeKey = null;
        $elementsAdded = 0;

        $values = [];
        $stringIndex = [];

        while ($this->lazyTail->valid() && !$isDone($safeKey)) {
            $originalKey = $this->lazyTail->key();

            $safeKey = is_valid_hash_map_key($originalKey)
                ? $originalKey
                : convert_to_valid_hash_map_key($originalKey);

            $stringIndex[$safeKey] = $originalKey;
            $values[$originalKey] = $this->lazyTail->current();

            $this->lazyTail->next();
            $elementsAdded++;
        }

        $this->array = array_merge($this->array, $values);
        $this->index = array_merge($this->index, $stringIndex);

        return $elementsAdded;
    }
}
