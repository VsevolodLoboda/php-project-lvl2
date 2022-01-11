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
function stylishFormatter(array $diffTree): string
{
    $generateStylishView = function (array $diff, int $depth = 1) use (&$generateStylishView) {
        $indent = getIndent($depth);

        $str = array_map(function (array $item) use ($depth, $indent, $generateStylishView) {
            $key = stringify($item['key'], $depth);
            $val1 = key_exists('val1', $item) ? stringify($item['val1'], $depth) : null;
            $val2 = key_exists('val2', $item) ? stringify($item['val2'], $depth) : null;
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

        $bracketIndent = getIndent($depth - 1);
        return "{\n" . implode($str) . $bracketIndent . ($depth === 1 ? '' : '  ') . "}\n";
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
        $indent = getIndent($depth);
        return "{\n" . stringifyObject($data, $depth + 1) . "\n$indent  }";
    }

    if (is_string($data)) {
        return $data;
    }

    return strtolower(trim(var_export($data, true), "'"));
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
        $indent = getIndent($depth);
        $value = stringify($obj->$key, $depth);
        return "$indent  $key: $value";
    }, $keys);

    return implode("\n", $lines);
}

/**
 * @param int $depth
 * @return string
 */
function getIndent(int $depth): string
{
    if ($depth === 0) {
        return '';
    }

    return str_repeat(' ', $depth * 4 - 2);
}
