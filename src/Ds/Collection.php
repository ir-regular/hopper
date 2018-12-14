<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

/**
 * Represents a collection that holds elements but does not specify the access method.
 *
 * Extends \IteratorAggregate only because I want the collections to fit the `iterable` slot.
 *
 * All hopper functions prefer interacting with appropriate interface methods from
 * `IrRegular\Hopper\Ds` interfaces, if possible.
 */
interface Collection extends \Countable, \IteratorAggregate
{
    public function isEmpty(): bool;

    public function contains($value): bool;

    public function getValues(): Sequence;
}
