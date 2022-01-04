<?php

namespace Differ\Formatters\Text;

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
function textOutputFormatter(array $diffTree): string
{
    /**
     * @param array $diffTree Diff tree structure
     * @param string $key Key name, for creating nested keys like "common.setting1"
     * @return string
     * @throws Exception
     */
    $generateTextLog = function (array $diffTree, string $key = '') use (&$generateTextLog) {
        $result = array_map(function ($item) use ($generateTextLog, $key) {
            // Create nested key
            $nestedKey = $key . $item['key'];

            $template = match ($item['status']) {
                DIFF_ADDED => "Property '%s' was added with value: %s",
                DIFF_DELETED => "Property '%s' was removed",
                DIFF_UPDATED => "Property '%s' was updated. From %s to %s",
                DIFF_COLLECTION => $generateTextLog($item['collection'], $nestedKey . "."),
                DIFF_SAME => null,
                default => throw new \Exception("Unknown status: " . $item['status']->value)
            };

            return !is_null($template) ? sprintf(
                $template,
                $nestedKey,
                stringify(array_key_exists('val1', $item) ? $item['val1'] : ''),
                stringify(array_key_exists('val2', $item) ? $item['val2'] : '')
            ) : null;
        }, $diffTree);

        return implode("\n", array_filter($result, fn($item) => !is_null($item)));
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
