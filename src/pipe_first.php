<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Returns a single-argument function that applies all first-partials to it in order.
 *
 * The value will be always injected as the _first_ argument of a partial.
 * Return value of the first partial will be injected into the second, and so on.
 *
 * Apply to collections when treating them as a single object.
 * Inspired by -> Clojure macro.
 *
 * @param mixed ...$partials
 * @return callable
 */
function pipe_first(...$partials): callable
{
    foreach ($partials as &$partial) {
        if (is_callable($partial)) {
            $partial = partial_first($partial);
        } else {
            $partial = partial_first(...$partial);
        }
    }

    return compose(...$partials);
}
