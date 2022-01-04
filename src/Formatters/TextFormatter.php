<?php

namespace Diff\Formatters\Text;

use Diff\Core\DiffStatus;

function textOutputFormatter(array $diffTree): string
{
    $recursiveFunction = function (array $diffTree, string $key = '') use (&$recursiveFunction) {

        $result = array_map(function ($item) use ($recursiveFunction, $key) {
            $nestedKey = $key . $item['key'];

            $template = match ($item['status']) {
                DiffStatus::Added => "Property '%s' was added with value: %s",
                DiffStatus::Deleted => "Property '%s' was removed",
                DiffStatus::Updated => "Property '%s' was updated. From %s to %s",
                DiffStatus::Collection => $recursiveFunction($item['collection'], $nestedKey . "."),
                DiffStatus::Same => '',
                default => throw new \Exception("Unknown status: " . $item['status']->value)
            };

            return sprintf(
                $template,
                $nestedKey,
                stringify(array_key_exists('val1', $item) ? $item['val1'] : ''),
                stringify(array_key_exists('val2', $item) ? $item['val2'] : '')
            );
        }, $diffTree);

        return implode("\n", array_filter($result, fn($item) => !empty($item)));
    };

    return $recursiveFunction($diffTree);
}

function stringify(mixed $data): string
{
    if (is_array($data)) {
        return '[' . implode(', ', $data) . ']';
    }

    if (is_object($data)) {
        return "[complex value]";
    }

    if (is_bool($data) || is_null($data) || is_numeric($data)) {
        return strtolower(trim(var_export($data, true), "'"));
    }

    return trim(var_export($data, true));
}
