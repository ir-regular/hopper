<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Extends \IteratorAggregate because I want the collections to fit the `iterable` slot,
 * but that's currently underdeveloped because all the hopper functions prefer interacting
 * with appropriate interface methods from `IrRegular\Hopper` interfaces.
 */
interface Sequence extends \IteratorAggregate
{
    public function first();

    public function last();

    public function rest(): Sequence;
}
