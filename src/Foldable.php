<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

interface Foldable
{
    public function foldl(callable $closure, $initialValue);

    public function foldr(callable $closure, $initialValue);
}
