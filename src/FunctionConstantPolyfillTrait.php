<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Even after you `use function`, only the fully qualified function name will be recognised as callable.
 * It would be nice to have `foobar::function` trait analogous to `FooBar::class` but, alas.
 * This trait allows you to simulate the behaviour.
 *
 * First, create a class in the same namespace and with the same name as the function.
 * You will then be able to both `use function \Foo\Bar\Util\bar;` and `use \Foo\Bar\Util\bar;` in the same file,
 * and then depending on your needs either call `bar()` directly or use `bar::function()` as a callback
 * instead of `'\Foo\Bar\Util\bar'`.
 *
 * To allow partial application, define a constructor with a number of optional parameters and save them in fields.
 * Then override `__invokable()` and prepend `$args` with the saved values.
 */
trait FunctionConstantPolyfillTrait
{
    public static function function(...$args)
    {
        return new self(...$args);
    }

    public function __invoke(...$args)
    {
        $functionName = self::class;
        return $functionName(...$args);
    }
}
