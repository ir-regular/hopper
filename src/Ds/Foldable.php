<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

interface Foldable extends Collection
{
    public function foldl(callable $closure, $initialValue);

    public function foldr(callable $closure, $initialValue);
}
