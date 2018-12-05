<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Represents a collection that holds elements but does not specify the access method.
 */
interface Collection
{
    public function isEmpty(): bool;

    public function getCount(): int;

    public function getValues(): iterable;
}
