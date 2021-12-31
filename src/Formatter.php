<?php

namespace Diff\Formatter;

use Diff\Core\DiffStatus;

function styleOutputFormatter(array $diff): string
{
    $recursiveFunction = function (array $diff, int $depth = 0) use (&$recursiveFunction) {
        $indent = str_repeat(' ', $depth * 2);

        $str = array_map(function (array $item) use ($depth, $indent, $recursiveFunction) {
            $key = stringifyArrayItem($item, 'key');
            $val1 = stringifyArrayItem($item, 'val1');
            $val2 = stringifyArrayItem($item, 'val2');
            $status = $item['status'] ?? null;
            $collection = $item['collection'] ?? null;

            return match ($status) {
                DiffStatus::Updated => "{$indent}- {$key}: {$val1}\n$indent+ {$key}: {$val2}\n",
                DiffStatus::Deleted => "{$indent}- {$key}: {$val1}\n",
                DiffStatus::Same => "{$indent}  {$key}: {$val1}\n",
                DiffStatus::Added => "{$indent}+ {$key}: {$val1}\n",
                DiffStatus::Collection => "{$indent}  ${key}: " . $recursiveFunction($collection, $depth + 1),
                default => throw new \Exception("Unsupported diff status: '{$status}'")
            };
        }, $diff);

        return "{\n" . implode($str) . "$indent}\n";
    };

    return $recursiveFunction($diff);
}

function stringifyArrayItem(array $array, string $key): string
{
    $value = $array[$key] ?? null;
    return trim(var_export($value, true), "'");
}