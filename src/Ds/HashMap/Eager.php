<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\HashMap;

use IrRegular\Hopper\Ds\Lazy as LazyInterface;
use IrRegular\Hopper\Ds\Mappable;
use IrRegular\Hopper\Ds\Set;
use function IrRegular\Hopper\Language\convert_to_key;
use function IrRegular\Hopper\Language\is_valid_key;
use function IrRegular\Hopper\set;

class Eager extends ArrayWrap
{
    /**
     * @var array
     */
    protected $index;

    public function __construct(array $collection, array $stringIndex)
    {
        parent::__construct($collection);

        $this->index = $stringIndex;
    }

    // Indexed

    public function get($key, $default = null)
    {
        if (!is_valid_key($key)) {
            $key = convert_to_key($key);
        }

        return parent::get($key, $default);
    }

    public function isKey($key): bool
    {
        if (!is_valid_key($key)) {
            $key = convert_to_key($key);
        }

        return parent::isKey($key);
    }

    public function getKeys(): Set
    {
        return set(array_values($this->index));
    }

    // Mappable

    public function map(callable $closure): Mappable
    {
        $collection = $this->getValueKeyPairList($closure);
        return new self($collection, $this->index);
    }

    public function lMap(callable $closure): LazyInterface
    {
        $generator = (function () use ($closure) {
            foreach ($this->getValueKeyPairList() as [$value, $key]) {
                yield [$closure($value, $key), $key];
            }
        })();

        return new Lazy($this->createMutable(), $generator, Lazy::FORMAT_VK);
    }

    /**
     * Returns an (eagerly generated) array of [value, key] pairs of original array.
     *
     * @param callable|null $callback Apply optional callback to the pairs.
     * @return array
     */
    protected function getValueKeyPairList(?callable $callback = null): array
    {
        return array_map($callback, $this->array, $this->index);
    }

    protected function createMutable()
    {
        return new class extends Eager {
            public function offsetSet($offset, $value)
            {
                if (!is_valid_key($offset)) {
                    $safeKey = convert_to_key($offset);
                } else {
                    $safeKey = $offset;
                }

                $this->index[$safeKey] = $offset;

                parent::offsetSet($safeKey, $value);
            }
        };
    }
}
