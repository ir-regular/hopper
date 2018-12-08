<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\HashMap;

use IrRegular\Hopper\Ds\Lazy as LazyInterface;
use IrRegular\Hopper\Ds\Mappable;
use function IrRegular\Hopper\Language\convert_to_key;
use function IrRegular\Hopper\Language\is_valid_key;

class Lazy extends Eager implements LazyInterface
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

    // Collection

    public function isEmpty(): bool
    {
        return empty($this->array) && !$this->lazyTail->valid();
    }

    public function count(): int
    {
        $this->realise();
        return parent::count();
    }

    public function getValues(): iterable
    {
        $this->realise();
        return parent::getValues();
    }

    public function contains($value): bool
    {
        $this->realise();
        return parent::contains($value);
    }

    public function getIterator()
    {
        if ($this->realiseFirstElement()) {
            $item = array_slice($this->array, 0, 1);
            yield key($item) => current($item);

            $current = 0;

            while ($this->realiseElementAfter($current)) {
                $item = array_slice($this->array, ++$current, 1);
                yield key($item) => current($item);
            }
        }
    }

    // Indexed

    public function get($key, $default = null)
    {
        $safeKey = is_valid_key($key)
            ? $key
            : convert_to_key($key);

        $this->realiseUpTo($safeKey);

        return parent::get($key, $default);
    }

    public function isKey($key): bool
    {
        $safeKey = is_valid_key($key)
            ? $key
            : convert_to_key($key);

        $this->realiseUpTo($safeKey);

        return parent::isKey($key);
    }

    public function getKeys(): iterable
    {
        $this->realise();
        return parent::getKeys();
    }

    // Foldable

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

    // Mappable

    public function map(callable $closure): Mappable
    {
        $this->realise();
        return parent::map($closure);
    }

    // Lazy

    public function getGenerator(): \Generator
    {
        return $this->lazyTail;
    }

    public function lMap(callable $closure): LazyInterface
    {
        $generator = (function () use ($closure) {
            foreach ($this->getIterator() as $key => $item) {
                yield $key => $closure($item, $key);
            }
        })();

        return new Lazy($generator);
    }

    /**
     * Realise all remaining elements of generator.
     *
     * @return void
     */
    protected function realise(): void
    {
        $this->realiseUntil(function () {
            return false;
        });
    }

    /**
     * Ensure at least first N elements of generator have been realised (where N > 0).
     *
     * @param int $elementCount
     * @return void
     */
    protected function ensureRealisedAtLeast(int $elementCount): void
    {
        assert($elementCount > 0);
        $elementsToAdd = $elementCount - count($this->array);

        $this->realiseUntil(function () use ($elementsToAdd) {
            static $elementsAdded = 0;
            return ($elementsAdded++ === $elementsToAdd); // increment _after_; we don't want to stop on 0th call
        });
    }

    /**
     * Realise elements of generator until you encounter one with key equal to $safeLookupKey.
     *
     * @param string $safeLookupKey
     * @return void
     */
    protected function realiseUpTo(string $safeLookupKey): void
    {
        $this->realiseUntil(function ($safeKey) use ($safeLookupKey) {
            return ($safeKey !== null) && ($safeKey === $safeLookupKey);
        });
    }

    /**
     * Realise and cache elements of generator until $predicate($safeKey) returns true, or until generator runs out.
     *
     * @param callable $isDone
     * @return void
     */
    protected function realiseUntil(callable $isDone): void
    {
        if ($this->realiseFirstElement()) {
            $current = 0;
            // $safeKey = array_key_first($this->index); // PHP 7.3
            $safeKey = array_keys($this->index)[0];

            while (!$isDone($safeKey) && $this->realiseElementAfter($current)) {
                $item = array_slice($this->index, ++$current, 1);
                $safeKey = key($item);
            }
        }
    }

    /**
     * Ensure the first element of the generator is realised.
     *
     * @return bool Whether 0th element now exists (false means generator's finished.)
     */
    protected function realiseFirstElement(): bool
    {
        if (count($this->array) == 0) {
            if ($this->lazyTail->valid()) {
                $originalKey = $this->lazyTail->key();
                $safeKey = is_valid_key($originalKey)
                    ? $originalKey
                    : convert_to_key($originalKey);

                $this->index[$safeKey] = $originalKey;
                $this->array[$originalKey] = $this->lazyTail->current();
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Ensure the next element after $current is realised.
     *
     * Note that this assumes all elements in range (0;$current) have been realised.
     *
     * @param int $current
     * @return bool Whether $current+1 element now exists (false means generator's finished.)
     */
    protected function realiseElementAfter(int $current): bool
    {
        assert($current >= 0);

        if (count($this->array) == $current + 1) {
            $this->lazyTail->next();

            if ($this->lazyTail->valid()) {
                $originalKey = $this->lazyTail->key();
                $safeKey = is_valid_key($originalKey)
                    ? $originalKey
                    : convert_to_key($originalKey);

                $this->index[$safeKey] = $originalKey;
                $this->array[$safeKey] =  $this->lazyTail->current();
            } else {
                return false;
            }
        }

        return true;
    }
}
