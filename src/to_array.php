<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\Collection;

function to_array(iterable $collection): array
{
    if (is_array($collection)) {
        return $collection;
    }

    if ($collection instanceof Collection) {
        // @TODO: in future, might use something more efficient to extract values directly
        $collection = $collection->getIterator();
    }

    return iterator_to_array($collection);
}
