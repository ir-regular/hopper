<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

interface Lazy extends \Traversable
{
    public function getGenerator(): \Generator;

    public function lMap(callable $closure): Lazy;
}
