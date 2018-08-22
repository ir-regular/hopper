<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Surprise! There is no Composable interface.
 *
 * This file contains higher-order functions that do not operate on collections; composition and partial application.
 *
 * That _does_ mean you need to import the functions in a different way - see `composer.json` of this library
 * for how to get them to autoload.
 */

/**
 * Returns the same value it was passed.
 *
 * Attn: pass-by-value, not reference; copying will occur.
 *
 * @param mixed $x
 * @return mixed
 */
function identity($x)
{
    return $x;
}

/**
 * Returns a function that transforms its input by applying each of $functions in turn.
 *
 * @param callable ...$functions
 * @return callable
 */
function compose(callable ...$functions): callable
{
    return function (...$operands) use ($functions) {
        $result = $operands;

        foreach ($functions as $f) {
            $result = $f(...$operands);
            $operands = [$result];
        }

        return $result;
    };
}

/**
 * Returns a function with a number of arguments pre-filled.
 *
 * If $function accepts N arguments, and you provide M $operands,
 * the returned function will require (N-M) arguments.
 *
 * @param callable $function
 * @param mixed ...$operands
 * @return callable
 */
function partial(callable $function, ...$operands): callable
{
    return function (...$moreOperands) use ($function, $operands) {
        return $function(...$operands, ...$moreOperands);
    };
}

/**
 * Returns a single-argument function, assuming the provided argument should be injected first to $function.
 *
 * For inspiration, see -> threading macro in Clojure.
 * Functions that require this pattern deal with a colletion as a single element (example: get_in.)
 *
 * @param callable $function
 * @param mixed ...$operands
 * @return callable
 */
function partial_first(callable $function, ...$operands): callable
{
    return function ($x) use ($function, $operands) {
        return $function($x, ...$operands);
    };
}

/**
 * Returns a single-argument function, assuming the provided argument should be injected last to $function.
 *
 * For inspiration, see ->> threading macro in Clojure.
 * Functions that require this pattern deal with all elements of a collection at once (example: map.)
 *
 * @param callable $function
 * @param mixed ...$operands
 * @return callable
 */
function partial_last(callable $function, ...$operands): callable
{
    return function ($x) use ($function, $operands) {
        $operands[] = $x;
        return $function(...$operands);
    };
}

/**
 * Sometimes, a library function expects an array input, but the delivery mechanism enforces
 * that values will be passed in separate arguments. So that's when you use apply, basically.
 *
 * @param callable $function
 * @param mixed ...$operands
 * @return mixed
 */
function apply(callable $function, ...$operands)
{
    return $function($operands);
}
