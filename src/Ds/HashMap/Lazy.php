<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\HashMap;

use IrRegular\Hopper\Ds\HashMap;
use IrRegular\Hopper\Ds\Lazy as LazyInterface;
use IrRegular\Hopper\Ds\Mappable;
use IrRegular\Hopper\Ds\Sequence;
use IrRegular\Hopper\Ds\Set;
use IrRegular\Hopper\Ds\Vector;

class Lazy implements HashMap, LazyInterface
{
    /**
     * Generator assumed to `yield $key => $value` formatted data.
     *
     * Use FORMAT_PHP when processing generators created by non-Hopper code.
     */
    const FORMAT_PHP = 'php';

    /**
     * Generator assumed to `yield [$value, $key]` formatted data.
     */
    const FORMAT_VK = 'vk_pair';

    /**
     * @var \Generator
     */
    protected $lazyTail;

    /**
     * @var string
     */
    protected $generatorFormat;

    /**
     * @var HashMap
     */
    private $hashMap;

    /**
     * @var string[]
     */
    private $keyBuffer = [];

    public function __construct(HashMap $hashMap, \Generator $lazyTail, $format = self::FORMAT_PHP)
    {
        $this->lazyTail = $lazyTail;
        $this->generatorFormat = $format;
        $this->hashMap = $hashMap;
    }

    // Collection

    public function isEmpty(): bool
    {
        return $this->hashMap->isEmpty() && !$this->lazyTail->valid();
    }

    public function count(): int
    {
        $this->realise();
        return $this->hashMap->count();
    }

    public function getValues(): Sequence
    {
        $this->realise();
        return $this->hashMap->getValues();
    }

    public function contains($value): bool
    {
        $this->realise();
        return $this->hashMap->contains($value);
    }

    public function getIterator()
    {
        if (!$this->lazyTail->valid()) {
            yield from $this->hashMap->getIterator();
            return;
        }

        // if, however, lazyTail is not yet fully realised:

        if ($key = $this->realiseFirstElement()) {
            yield [$this->hashMap->get($key), $key];

            $current = 0;

            while ($key = $this->realiseElementAfter($current)) {
                yield [$this->hashMap->get($key), $key];
            }
        }
    }

    // Indexed

    public function get($key, $default = null)
    {
        $this->realiseUpTo($key);

        return $this->hashMap->get($key, $default);
    }

    public function isKey($key): bool
    {
        $this->realiseUpTo($key);

        return $this->hashMap->isKey($key);
    }

    public function getKeys(): Set
    {
        $this->realise();

        return $this->hashMap->getKeys();
    }

    // ArrayAccess, via Indexed

    public function offsetExists($offset)
    {
        return $this->isKey($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Hopper collections are not mutable');
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Hopper collections are not mutable');
    }

    // Foldable

    public function foldl(callable $closure, $initialValue)
    {
        // why not call realise() and parent::foldl() ?
        // I can likely save on memory by realising elements one by one, and then immediately squashing each into $carry

        $carry = $initialValue;

        foreach ($this->getIterator() as [$value, $key]) {
            $carry = $closure($carry, $value, $key);
        }

        return $carry;
    }

    public function foldr(callable $closure, $initialValue)
    {
        $this->realise();

        return $this->hashMap->foldr($closure, $initialValue);
    }

    // Mappable

    public function map(callable $closure): Mappable
    {
        $this->realise();
        return $this->hashMap->map($closure);
    }

    public function lMap(callable $closure): LazyInterface
    {
        $generator = (function () use ($closure) {
            foreach ($this->getIterator() as [$item, $key]) {
                yield [$closure($item, $key), $key];
            }
        })();

        // @TODO
        return new Lazy($generator, Lazy::FORMAT_VK);
    }

    // HashMap

    public function toVector(): Vector
    {
        return $this->hashMap->toVector();
    }

    // Lazy

    public function getGenerator(): \Generator
    {
        return $this->lazyTail;
    }

    /**
     * Realise all remaining elements of generator.
     *
     * @return void
     */
    protected function realise(): void
    {
        $this->realiseUntil(null);
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
        $elementsToAdd = $elementCount - $this->hashMap->count();

        $this->realiseUntil(function () use ($elementsToAdd) {
            static $elementsAdded = 0;
            return ($elementsAdded++ === $elementsToAdd); // increment _after_; we don't want to stop on 0th call
        });
    }

    /**
     * Realise elements of generator until you encounter one with key equal to $lookupKey.
     *
     * @param mixed $lookupKey
     * @return void
     */
    protected function realiseUpTo($lookupKey): void
    {
        $this->realiseUntil(function ($key) use ($lookupKey) {
            return ($key !== null) && ($key === $lookupKey);
        });
    }

    /**
     * Realise and cache elements of generator until $isDone($key) returns true, or until generator runs out.
     *
     * @param callable|null $isDone
     * @return void
     */
    protected function realiseUntil(?callable $isDone): void
    {
        $key = $this->realiseFirstElement();
        $current = 0;

        while ($key && (!$isDone || !$isDone($key))) {
            $key = $this->realiseElementAfter($current++);
        }

        $this->clearBuffer();
    }

    /**
     * Ensure the first element of the generator is realised.
     *
     * @return mixed Key of the first element, or null to indicate generator is finished.
     */
    protected function realiseFirstElement()
    {
        $safeKey = null;

        if ($this->hashMap->count() == 0) {
            if ($this->lazyTail->valid()) {
                $safeKey = $this->realiseCurrentPair();
            } else {
                $this->clearBuffer();
            }
        } elseif ($this->lazyTail->valid()) {
            $safeKey = $this->keyBuffer[0];
        }

        return $safeKey;
    }

    /**
     * Ensure the next element after $current is realised.
     *
     * Note that this assumes all elements in range (0;$current) have already been realised.
     *
     * @param int $current
     * @return mixed The just-realised element key (or null to indicate generator is finished).
     */
    protected function realiseElementAfter(int $current)
    {
        assert($current >= 0);

        $key = null;

        if ($this->hashMap->count() == $current + 1) {
            $this->lazyTail->next();

            if ($this->lazyTail->valid()) {
                $key = $this->realiseCurrentPair();
            } else {
                $this->clearBuffer();
            }
        } elseif ($this->lazyTail->valid()) {
            $key = $this->keyBuffer[$current + 1];
        }

        return $key;
    }

    /**
     * Realise next (key,value) pair and return the key.
     *
     * Uses generatorFormat to interpret generator results.
     * Equivalent to current() in that it does not advance the generator to next element.
     *
     * @return mixed
     */
    protected function realiseCurrentPair()
    {
        if ($this->generatorFormat == self::FORMAT_PHP) {
            $key = $this->lazyTail->key();
            $value = $this->lazyTail->current();
        } else {
            [$value, $key] = $this->lazyTail->current();
        }

        $this->hashMap[$key] = $value;

        $this->keyBuffer[] = $key;

        return $key;
    }

    /**
     * If lazyTail generator is exhausted, and all keys turned out to be safe, we can get rid of index.
     *
     * Performance optimisation.
     *
     * @return void
     */
    protected function clearBuffer(): void
    {
        assert(!$this->lazyTail->valid());

        $this->keyBuffer = [];
    }
}
