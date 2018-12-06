<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 *Returns a single-argument function that applies all last-partials to it in order.
 *
 * The value will be always injected as the _last_ argument of a partial.
 * Return value of the first partial will be injected into the second, and so on.
 *
 * Apply to collections when treating them as a collection of objects.
 *
 * @param mixed ...$partials
 * @return callable
 */
function pipe_last(...$partials): callable
{
    foreach ($partials as &$partial) {
        if (is_callable($partial)) {
            $partial = partial_last($partial);
        } else {
            $partial = partial_last(...$partial);
        }
    }

    return compose(...$partials);
}
