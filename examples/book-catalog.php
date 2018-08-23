<?php
declare(strict_types=1);

namespace IrRegular\Examples\Hopper\Json;

require_once __DIR__ . '/../vendor/autoload.php';

use function IrRegular\Hopper\Collection\vector;
use function IrRegular\Hopper\compose;
use function IrRegular\Hopper\map;
use function IrRegular\Hopper\partial;
use function IrRegular\Hopper\partial_first;
use function IrRegular\Hopper\size;

$file = 'resources/catalog.books.json';
$url = 'https://raw.githubusercontent.com/ozlerhakan/mongodb-json-files/master/datasets/catalog.books.json';

if (!is_file($file)) {
    $fp = fopen($file, "w");
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, false);

    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}

$raw = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (empty($raw)) {
    exit(-1);
}

$books = map(partial_first('\json_decode', true), $raw);

// Note that I used `map()`, which currently returns a generator.
// If you don't apply `\iterator_to_array()`, or realise the generator in some other way (here: through `vector`),
// $books can be used only once. This is not ideal, and therefore not a final solution.

$books = vector($books);

// Note that if using a namespaced function as a callable string, PHP requires you to provide
// a fully qualified function name, even if you `use function`.
// I'm undecided between leaving it and allowing short strings.
// Fully qualified names have the advantage of PHPStorm (and likely other IDEs) understanding
// what function they actually refer to.

$pluckCategories = compose(
    partial('\IrRegular\Hopper\map', partial_first('\IrRegular\Hopper\get', 'categories', [])),
    // why not just '\array_merge'?
    // because foldl run over an iterator provides third argument, $key, and array_merge would attempt to merge it
    partial('\IrRegular\Hopper\foldl', function ($carry, $value) {
        return array_merge($carry, $value);
    }, []),
    partial('\IrRegular\Hopper\Collection\set')
);

$categories = $pluckCategories($books);

printf("Catalog contains %d books.\n", size($books));
printf("There are %d unique book categories\n", size($categories));
