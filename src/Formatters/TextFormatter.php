<?php

namespace Diff\Formatters\Text;

use Diff\Core\DiffStatus;

/**
 * @param array $diffTree
 * @return string
 * @throws \Exception
 */
function textOutputFormatter(array $diffTree): string
{
    /**
     * @param array $diffTree Diff tree structure
     * @param string $key Key name, for creating nested keys like "common.setting1"
     * @return string
     * @throws \Exception
     */
    $generateTextLog = function (array $diffTree, string $key = '') use (&$generateTextLog) {
        $result = array_map(function ($item) use ($generateTextLog, $key) {
            // Create nested key
            $nestedKey = $key . $item['key'];

            $template = match ($item['status']) {
                DiffStatus::Added => "Property '%s' was added with value: %s",
                DiffStatus::Deleted => "Property '%s' was removed",
                DiffStatus::Updated => "Property '%s' was updated. From %s to %s",
                DiffStatus::Collection => $generateTextLog($item['collection'], $nestedKey . "."),
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

    return $generateTextLog($diffTree);
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
        // Trim quotes for non string value
        return strtolower(trim(var_export($data, true), "'"));
    }

    return var_export($data, true);
}
