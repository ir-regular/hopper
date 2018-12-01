<?php
declare(strict_types=1);

// Do not include sub-namespaces for now; Set class / set() function pair already exists for example.
\IrRegular\Hopper\Language\add_function_constant_polyfill_to_ns('IrRegular\Hopper', true);

// It'd be nice to use something like this. Unfortunately I can't see how to reflect on contents
// of the currently loaded namespace. (If someone calls `foo::function()`, I need to be able to look
// up function foo that's been imported into current namespace, without knowing its original namespace.
///**
// * Autoloader that creates a class with `::function()` proxying to function with the same name.
// *
// * @see FunctionConstantPolyfillTrait
// */
//spl_autoload_register(function (string $class): ?bool {
//        static $template = <<<CLASS
//namespace %ns_name%;
//{
//    class %fn_name%
//    {
//        use \IrRegular\Hopper\Language\FunctionConstantPolyfillTrait;
//    }
//}
//CLASS;
//
//    try {
//        $f = new \ReflectionFunction($class);
//        $customised = str_replace('%ns_name%', $f->getNamespaceName(), $template);
//        $customised = str_replace('%fn_name%', $f->getShortName(), $customised);
//        eval($customised);
//        return true;
//
//    } catch (\ReflectionException $e) {
//        return null;
//    }
//}, true, true);
