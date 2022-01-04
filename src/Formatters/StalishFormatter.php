<?php

namespace Diff\Formatters\Stylish;

use Diff\Core\DiffStatus;
use Exception;

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
                DiffStatus::Updated => "$indent- $key: $val1\n$indent+ $key: $val2\n",
                DiffStatus::Deleted => "$indent- $key: $val1\n",
                DiffStatus::Same => "$indent  $key: $val1\n",
                DiffStatus::Added => "$indent+ $key: $val1\n",
                DiffStatus::Collection => "$indent  $key: " . $generateStylishView($collection, $depth + 1),
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
