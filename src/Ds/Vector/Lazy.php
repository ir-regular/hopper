<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\Vector;

use IrRegular\Hopper\Ds\Lazy as LazyInterface;
use IrRegular\Hopper\Ds\Mappable;
use IrRegular\Hopper\Ds\Sequence;
use IrRegular\Hopper\Ds\Set;

/**
 *  Note how this class manually iterates over the generator.
 *
 * `iterator_to_array` and `foreach` attempt to rewind the iterator at the start.
 *
 * If you attempted to use them on $this->lazyTail after some other method partially
 * realised it, you'd get an error: "Cannot rewind a generator that was already run".
 * Thus, everything uses current(), next(), and valid() methods of Iterator.
 */
class Lazy extends Eager implements LazyInterface
{
    /**
     * @var \Generator
     */
    protected $lazyTail;

    public function __construct(\Generator $lazyTail)
    {
        parent::__construct([]);

        $this->lazyTail = $lazyTail;
    }

    // Collection

    public function isEmpty(): bool
    {
        return empty($this->array) && !$this->lazyTail->valid();
    }

    public function contains($value): bool
    {
        // @TODO change realise to a condition based on index OR value
        $this->realise();
        return parent::contains($value);
    }

    public function count(): int
    {
        $this->realise();
        return parent::count();
    }

    public function getValues(): Sequence
    {
        $this->realise();
        return parent::getValues();
    }

    public function getIterator()
    {
        if ($this->realiseFirstElement()) {
            yield $this->array[0];

            $current = 0;

            while ($this->realiseElementAfter($current)) {
                yield $this->array[++$current];
            }
        }
    }

    // Sequence

    public function first()
    {
        $this->realiseFirstElement();
        return parent::first();
    }

    public function last()
    {
        $this->realise();
        return parent::last();
    }

    public function rest(): Sequence
    {
        $generator = $this->getIterator();
        $generator->next(); // skip the first item

        return new self($generator);
    }

    // Indexed

    public function get($key, $default = null)
    {
        assert(is_int($key) && $key >= 0);

        $this->realise($key);

        return $this->array[$key] ?? $default;
    }

    public function isKey($key): bool
    {
        assert(is_int($key) && $key >= 0);

        $this->realise($key);

        return parent::isKey($key);
    }

    public function getKeys(): Set
    {
        $this->realise();
        return parent::getKeys();
    }

    // Foldable

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

    // Mappable

    public function map(callable $closure): Mappable
    {
        $this->realise();
        return parent::map($closure);
    }

    public function lMap(callable $closure): LazyInterface
    {
        $generator = (function () use ($closure) {
            foreach ($this->getIterator() as $value) {
                yield $closure($value);
            }
        })();

        return new Lazy($generator);
    }

    // Lazy

    public function getGenerator(): \Generator
    {
        return $this->lazyTail;
    }

    /**
     * Realise elements until and including $index
     *
     * If $index is null: realise all remaining elements.
     *
     * @param int|null $index
     * @return void
     */
    protected function realise(?int $index = null)
    {
        $this->realiseFirstElement();

        $current = 0;

        if (is_null($index)) {
            while ($this->realiseElementAfter($current)) {
                $current++;
            }
        } else {
            while ($current < $index && $this->realiseElementAfter($current)) {
                $current++;
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
        if (!array_key_exists(0, $this->array)) {
            if ($this->lazyTail->valid()) {
                $this->array[] = $this->lazyTail->current();
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

        if (!array_key_exists($current + 1, $this->array)) {
            $this->lazyTail->next();

            if ($this->lazyTail->valid()) {
                $this->array[] = $this->lazyTail->current();
            } else {
                return false;
            }
        }

        return true;
    }
}
