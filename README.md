# Hopper

Clojure-inspired library for processing deeply-nested data structures in PHP.

## Use Hopper if you are...

...reformatting deeply nested structures retrieved from JSON- or XML-speaking API in memory.

...tired of writing for loops and getting indices wrong

...wishing all the `array_*` functions had a consistent interface

...wishing that you could use array-processing functions on `iterable`s
without constant `iterator_to_array`
- perhaps a library that you use provides `iterable`s
- or perhaps you're dealing with a _lot_ of data and you'd like to
    process it lazily instead of storing _everything at once_ in memory

## How to include Hopper in your code

Add `ir-regular/hopper` as a Composer dependency:

`composer require ir-regular/hopper`

If you use one or more of the Collection classes, all the necessary functions will autoload along with it. If you don't
happen to use Collection classes, you'll need to [explicitly autoload specific files](https://getcomposer.org/doc/04-schema.md#files).

Then, `use` the functions so that they become available in your namespace. For example:

`use function IrRegular\Hopper\map;`

### Examples

- [Extracting information out of a JSON book catalogue](https://github.com/ir-regular/hopper/blob/master/examples/book-catalog.php)

## Design choices for the curious

### Functions and their placement

This is a functional library _because I said so_. (Also because it's meant to work with plain arrays.)

Sadly, PHP doesn't support function autoloading at the time of writing ([an RFC exists](https://wiki.php.net/rfc/function_autoloading), but didn't make it into 7.1).
[PSR-4](https://www.php-fig.org/psr/psr-4/), logically enough, doesn't cover it either.

All functions are therefore defined in a file that also contains a relevant interface
used by Collection classes. If you use a Collection class, the file gets autoloaded and therefore functions
become available. 

### Namespacing library functions

When using a namespaced function as a callable string, PHP requires you to provide
a fully qualified function name, even if you `use function`. Such is life.

This is inconvenient when composing functions. I'm thinking of ways to allow short strings,
specifically for library functions. Treat this as a feature under development.
 
### Collection classes

Originally I wanted to have the library work on plain PHP arrays only. I also wanted to make it explicit whether you
were dealing with array-as-list or array-as-hashmap. I could either create a multitude of functions mirroring PHP's
tendency to have `array_function` and `array_function_keys`, or somehow indicate the 'type' of the array.

Collection classes are therefore, wrappers that serve as type flags.

The interfaces are merely conveniences that allowed me to chunk up the array-processing function logic
into smaller segments.

### Code development

Code style conforms to [PSR-2](https://www.php-fig.org/psr/psr-2/)
and [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md)
with some exceptions that I use to increase readability of control structures
with complicated conditions.

Run `composer test` to run unit tests with a condensed report format.

Run `composer ci` to run all available checks:
- code style
- static analysis
- unit tests, in testdox (human-readable/literate) format 
