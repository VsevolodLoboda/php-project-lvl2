<?php

namespace Diff\Formatter;

use Diff\Core\DiffStatus;
use Exception;

function getIntent(int $depth)
{
    return str_repeat(' ', $depth * 2 - 2);
}

/**
 * @throws Exception
 */
function stylishOutputFormatter(array $diffTree): string
{
    $recursiveFunction = function (array $diff, int $depth = 1) use (&$recursiveFunction) {
        $indent = getIntent($depth);

        $str = array_map(function (array $item) use ($depth, $indent, $recursiveFunction) {
            $key = stringify($item['key'] ?? '', $depth);
            $val1 = stringify($item['val1'] ?? '', $depth);
            $val2 = stringify($item['val2'] ?? '', $depth);
            $status = $item['status'] ?? null;
            $collection = $item['collection'] ?? null;

            return match ($status) {
                DiffStatus::Updated => "$indent- $key: $val1\n$indent+ $key: $val2\n",
                DiffStatus::Deleted => "$indent- $key: $val1\n",
                DiffStatus::Same => "$indent  $key: $val1\n",
                DiffStatus::Added => "$indent+ $key: $val1\n",
                DiffStatus::Collection => "$indent  $key: " . $recursiveFunction($collection, $depth + 1),
                default => throw new Exception("Unsupported diff status: '$status'")
            };
        }, $diff);

        return "{\n" . implode($str) . "$indent}\n";
    };

    return $recursiveFunction($diffTree);
}

function stringify(mixed $data, int $depth = 1): string
{
    if (is_array($data)) {
        return '[' . implode(', ', $data) . ']';
    }

    if (is_object($data)) {
        $indent = getIntent($depth + 1);
        return "{\n" . stringifyObject($data, $depth + 1) . "\n{$indent}}";
    }

    return trim(var_export($data, true), "'");
}

function stringifyObject(object $obj, int $depth = 1)
{
    $keys = array_keys(get_object_vars($obj));
    $lines = array_map(function ($key) use ($obj, $depth) {
        $indent = getIntent($depth);
        $value = stringify($obj->$key, $depth);

        return "$indent  {$key}: {$value}";
    }, $keys);

    return implode("\n", $lines);
}


function jsonOutputFormatter(array $diffTree): string
{
    return json_encode($diffTree);
}

function textOutputFormatter(array $diffTree): string
{
    return json_encode($diffTree);
}
