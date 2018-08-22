<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\ListAccessible;

class LazyVector extends Vector
{
    /**
     * @var \Iterator
     */
    protected $lazyTail;

    public function __construct(\Generator $lazyTail)
    {
        parent::__construct([]);

        $this->lazyTail = $lazyTail;
    }

    public function foldl(callable $closure, $initialValue)
    {
        $this->realise();
        return parent::foldl($closure, $initialValue);
    }

    public function foldr(callable $closure, $initialValue)
    {
        $this->realise();
        return parent::foldr($closure, $initialValue);
    }

    public function map(callable $closure): \Generator
    {
        foreach ($this->getIterator() as $value) {
            yield $closure($value);
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
        $this->realiseUpTo(0);
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

        return new LazyVector($generator);
    }

    public function get($key, $default = null)
    {
        assert(is_int($key));

        $this->realiseUpTo($key);
        return parent::get($key, $default);
    }

    public function isKey($key): bool
    {
        assert(is_int($key));

        $this->realiseUpTo($key);
        return parent::isKey($key);
    }

    public function getKeys(): iterable
    {
        $this->realise();
        return parent::getKeys();
    }

    public function getIterator()
    {
        // Note that I'm not using a foreach loop here, or calling $this->lazyTail->next().
        // After this method `yield`s, someone might increment the generator using other methods.
        // `realiseUpTo()` will only increment the generator if the element hasn't been realised yet.
        //
        // Furthermore, `realiseUpTo()` will return 1 if found, 0 if no more elements found
        // (either from array or generator), so it works as a loop break condition.

        $index = 0;

        while (array_key_exists($index, $this->array) || $this->realiseUpTo($index)) {
            yield $this->array[$index];
            $index++;
        }
    }

    protected function realise(): int
    {
        $elementsAdded = 0;

        // If you try to use `iterator_to_array` in here, and something's already touched
        //  $this->lazyTail, you'll get "Cannot rewind a generator that was already run"

        while ($this->lazyTail->valid()) {
            $this->array[] = $this->lazyTail->current();
            $this->lazyTail->next();
            $elementsAdded++;
        }

        return $elementsAdded;
    }

    protected function realiseUpTo(int $index): int
    {
        $elementsAdded = 0;

        if (!array_key_exists($index, $this->array) && $this->lazyTail->valid()) {
            $elementsToAdd = ($index + 1) - count($this->array);

            // Note: we have to constantly check if the generator is still valid;
            // it is entirely possible that it has less than $elementsToAdd left.

            for ($i = 0; ($i < $elementsToAdd) && $this->lazyTail->valid(); $i++) {
                $this->array[] = $this->lazyTail->current();
                $this->lazyTail->next();
                $elementsAdded++;
            }
        }

        return $elementsAdded;
    }
}
