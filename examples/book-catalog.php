<?php
declare(strict_types=1);

namespace IrRegular\Examples\Hopper\Json;

include_once __DIR__ . '/../vendor/autoload.php';
include_once 'io.php';

use function IrRegular\Hopper\map;
use function IrRegular\Hopper\partial_first;
use function IrRegular\Hopper\pipe_last;
use function IrRegular\Hopper\size;

// Prep data

$file = 'resources/catalog.books.json';
$url = 'https://raw.githubusercontent.com/ozlerhakan/mongodb-json-files/master/datasets/catalog.books.json';

if (!download_and_cache($url, $file)) {
    exit(-1);
}

$raw = read_lines($file);

// Have some fun with the data!

$books = map(partial_first('\json_decode', true), $raw);

printf("\nCatalog contains %d books.\n", size($books));

// why not just use '\array_merge'?
// because foldl when run over an iterator provides third argument, $key,
// and array_merge would treat it as a third array and attempt to merge it

$mergeTwo = function ($array1, $array2) {
    return array_merge($array1, $array2);
};

/*
 * It would be nice if the second argument could be, simply, '\IrRegular\Hopper\keys'
 *
 * But no!
 *
 * `pipe_last()` internally calls `is_callable()` on an array of two strings.
 * PHP thinks the first argument is a class, so it attempts to load the file
 * and check the class for its functions.
 *
 * Given my file paths follow PSR-4, it of course loads the file for the second time,
 * and promptly dies screaming that you cannot redeclare `map()`.
 *
 * Oops.
 *
 * So... the temporary solution is to make the second argument not a string.
 *
 * Better solution for Hopper functions coming in near future, but I expect people
 * will stub their toes against this occasionally.
 */

$bookAttributes = pipe_last(
    ['\IrRegular\Hopper\map', partial_first('\IrRegular\Hopper\keys')],
    ['\IrRegular\Hopper\foldl', $mergeTwo, []],
    '\IrRegular\Hopper\set',
    '\IrRegular\Hopper\values'
)($books);

printf("\nA book record can have the following attributes:\n");
var_export($bookAttributes);

$categories = pipe_last(
    ['\IrRegular\Hopper\map', partial_first('\IrRegular\Hopper\get', 'categories', [])],
    ['\IrRegular\Hopper\foldl', $mergeTwo, []],
    '\IrRegular\Hopper\set'
)($books);

printf("\nThere are %d unique book categories\n", size($categories));
