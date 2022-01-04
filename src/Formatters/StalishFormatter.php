<?php

namespace Differ\Formatters\Stylish;

use Exception;

use const Differ\Differ\DIFF_ADDED;
use const Differ\Differ\DIFF_COLLECTION;
use const Differ\Differ\DIFF_DELETED;
use const Differ\Differ\DIFF_SAME;
use const Differ\Differ\DIFF_UPDATED;

/**
 * @param array $diffTree
 * @return string
 * @throws Exception
 */
function stylishOutputFormatter(array $diffTree): string
{
    $generateStylishView = function (array $diff, int $depth = 1) use (&$generateStylishView) {
        $indent = getIntent($depth);

        $str = array_map(function (array $item) use ($depth, $indent, $generateStylishView) {
            $key = stringify($item['key'] ?? '', $depth);
            $val1 = stringify($item['val1'] ?? '', $depth);
            $val2 = stringify($item['val2'] ?? '', $depth);
            $collection = $item['collection'] ?? null;
            $status = $item['status'];

            return match ($status) {
                DIFF_UPDATED => "$indent- $key: $val1\n$indent+ $key: $val2\n",
                DIFF_DELETED => "$indent- $key: $val1\n",
                DIFF_SAME => "$indent  $key: $val1\n",
                DIFF_ADDED => "$indent+ $key: $val1\n",
                DIFF_COLLECTION => "$indent  $key: " . $generateStylishView($collection, $depth + 1),
                default => throw new Exception("Unsupported diff status: '$status'")
            };
        }, $diff);

        return "{\n" . implode($str) . "$indent}\n";
    };

    return trim($generateStylishView($diffTree), "\n");
}

/**
 * @param mixed $data
 * @param int $depth
 * @return string
 */
function stringify(mixed $data, int $depth = 1): string
{
    if (is_array($data)) {
        return '[' . implode(', ', $data) . ']';
    }

    if (is_object($data)) {
        $indent = getIntent($depth + 1);
        return "{\n" . stringifyObject($data, $depth + 1) . "\n$indent}";
    }

    return trim(var_export($data, true), "'");
}

/**
 * @param object $obj
 * @param int $depth
 * @return string
 */
function stringifyObject(object $obj, int $depth = 1): string
{
    $keys = array_keys(get_object_vars($obj));
    $lines = array_map(function ($key) use ($obj, $depth) {
        $indent = getIntent($depth);
        $value = stringify($obj->$key, $depth);
        return "$indent  $key: $value";
    }, $keys);

    return implode("\n", $lines);
}

/**
 * @param int $depth
 * @return string
 */
function getIntent(int $depth): string
{
    return str_repeat(' ', $depth * 2 - 2);
}
