<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\HashMap;

use IrRegular\Hopper\Ds\Lazy as LazyInterface;
use IrRegular\Hopper\Ds\Mappable;
use IrRegular\Hopper\Ds\Sequence;
use IrRegular\Hopper\Ds\Set;
use function IrRegular\Hopper\Language\convert_to_key;
use function IrRegular\Hopper\Language\is_valid_key;

class Lazy extends Eager implements LazyInterface
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

    public function __construct(\Generator $lazyTail, $format = self::FORMAT_PHP)
    {
        parent::__construct([], []);

        $this->lazyTail = $lazyTail;
        $this->generatorFormat = $format;
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

    public function getValues(): Sequence
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
        if (!$this->lazyTail->valid()) {
            yield from parent::getIterator();
            return;
        }

        // if, however, lazyTail is not yet fully realised:

        if ($safeKey = $this->realiseFirstElement()) {
            yield [$this->array[$safeKey], $this->index[$safeKey] ?? $safeKey];

            $current = 0;

            while ($safeKey = $this->realiseElementAfter($current)) {
                yield [$this->array[$safeKey], $this->index[$safeKey] ?? $safeKey];
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

    public function getKeys(): Set
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

        foreach ($this->getIterator() as [$value, $key]) {
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

    public function lMap(callable $closure): LazyInterface
    {
        $generator = (function () use ($closure) {
            foreach ($this->getIterator() as [$item, $key]) {
                yield [$closure($item, $key), $key];
            }
        })();

        return new Lazy($generator, Lazy::FORMAT_VK);
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
     * Realise and cache elements of generator until $isDone($safeKey) returns true, or until generator runs out.
     *
     * @param callable|null $isDone
     * @return void
     */
    protected function realiseUntil(?callable $isDone): void
    {
        $safeKey = $this->realiseFirstElement();
        $current = 0;

        while ($safeKey && (!$isDone || !$isDone($safeKey))) {
            $safeKey = $this->realiseElementAfter($current++);
        }

        $this->clearIndexIfAllKeysValid();
    }

    /**
     * Ensure the first element of the generator is realised.
     *
     * @return string|null Safe key of the first element, or null to indicate generator is finished.
     */
    protected function realiseFirstElement(): ?string
    {
        $safeKey = null;

        if (count($this->array) == 0) {
            if ($this->lazyTail->valid()) {
                $safeKey = $this->realiseCurrentPair();
            } else {
                $this->clearIndexIfAllKeysValid();
            }
        } elseif ($this->lazyTail->valid()) {
            $safeKey = array_keys($this->array)[0];
        }

        return $safeKey;
    }

    /**
     * Ensure the next element after $current is realised.
     *
     * Note that this assumes all elements in range (0;$current) have already been realised.
     *
     * @param int $current
     * @return string|null The just-realised element key (or null to indicate generator is finished).
     */
    protected function realiseElementAfter(int $current): ?string
    {
        assert($current >= 0);

        $safeKey = null;

        if (count($this->array) == $current + 1) {
            $this->lazyTail->next();

            if ($this->lazyTail->valid()) {
                $safeKey = $this->realiseCurrentPair();
            } else {
                $this->clearIndexIfAllKeysValid();
            }
        } elseif ($this->lazyTail->valid()) {
            $safeKey = $safeKey = array_keys($this->array)[$current + 1];
        }

        return $safeKey;
    }

    /**
     * Realise next (key,value) pair and return the key in safe-key (non-numeric string) form.
     *
     * Uses generatorFormat to interpret generator results.
     * Equivalent to current() in that it does not advance the generator to next element.
     *
     * @return string
     */
    protected function realiseCurrentPair(): string
    {
        if ($this->generatorFormat == self::FORMAT_PHP) {
            $key = $this->lazyTail->key();
            $value = $this->lazyTail->current();
        } else {
            [$value, $key] = $this->lazyTail->current();
        }

        $safeKey = is_valid_key($key) ? $key  : convert_to_key($key);

        $this->index[$safeKey] = $key;
        $this->array[$safeKey] = $value;

        return $safeKey;
    }

    /**
     * If lazyTail generator is exhausted, and all keys turned out to be safe, we can get rid of index.
     *
     * Performance optimisation.
     *
     * @return void
     */
    protected function clearIndexIfAllKeysValid(): void
    {
        if (is_null($this->index) || $this->lazyTail->valid()) {
            return;
        }

        $allKeysValid = true;

        foreach ($this->index as $safeKey => $unsafeKey) {
            if ($safeKey !== $unsafeKey) {
                $allKeysValid = false;
                break;
            }
        }

        if ($allKeysValid) {
            // this causes $this->areAllKeysValid() to start returning true
            $this->index = null;
        }
    }
}
