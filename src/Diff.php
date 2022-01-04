<?php

namespace Differ\Differ;

use Exception;

use function Functional\sort;
use function Differ\Formatters\Json\jsonOutputFormatter;
use function Differ\Formatters\Stylish\stylishOutputFormatter;
use function Differ\Formatters\Text\textOutputFormatter;
use function Differ\Parser\parse;

// TODO: Replace to enum
const JSON_FORMATTER = 'json';
const STYLISH_FORMATTER = 'stylish';
const PLAIN_FORMATTER = 'plain';

// TODO: Replace to enum
const DIFF_ADDED = 'added';
const DIFF_DELETED = 'deleted';
const DIFF_UPDATED = 'updated';
const DIFF_SAME = 'same';
const DIFF_COLLECTION = 'collection';

/**
 * @param string $filePath1
 * @param string $filePath2
 * @param string $formatter
 * @return string
 * @throws Exception
 */
function genDiff(string $filePath1, string $filePath2, string $formatter = 'stylish'): string
{
    $ext1 = strtolower(extractExtension($filePath2));
    $ext2 = strtolower(extractExtension($filePath2));

    $diffTree = createDiffTree(
        parse($ext1, readFile($filePath1)),
        parse($ext2, readFile($filePath2)),
    );

    return match ($formatter) {
        JSON_FORMATTER => jsonOutputFormatter($diffTree),
        STYLISH_FORMATTER => stylishOutputFormatter($diffTree),
        PLAIN_FORMATTER => textOutputFormatter($diffTree),
        default => throw new Exception("Unknown formatter: $formatter")
    };
}

/**
 * @param object $structure1
 * @param object $structure2
 * @return array
 */
function createDiffTree(object $structure1, object $structure2): array
{
    $keys = array_unique(
        array_merge(
            array_keys(get_object_vars($structure1)),
            array_keys(get_object_vars($structure2))
        )
    );

    $sortedKeys = sort($keys, fn($item1, $item2) => strcmp($item1, $item2));

    return array_map(function ($key) use ($structure1, $structure2) {
        $val1 = $structure1->$key ?? null;
        $val2 = $structure2->$key ?? null;

        if (!property_exists($structure1, $key)) {
            return [
                'key' => $key,
                'val1' => $val2,
                'status' => DIFF_ADDED
            ];
        }

        if (!property_exists($structure2, $key)) {
            return [
                'key' => $key,
                'val1' => $val1,
                'status' => DIFF_DELETED
            ];
        }

        // If both are nested structures - compare them
        if (is_object($val1) && is_object($val2)) {
            return [
                'key' => $key,
                'status' => DIFF_COLLECTION,
                'collection' => createDiffTree($val1, $val2)
            ];
        }

        if ($val1 !== $val2) {
            return [
                'key' => $key,
                'val1' => $val1,
                'val2' => $val2,
                'status' => DIFF_UPDATED
            ];
        }

        return [
            'key' => $key,
            'val1' => $val1,
            'status' => DIFF_SAME
        ];
    }, $sortedKeys);
}

/**
 * @param string $filePath
 * @return string
 * @throws Exception
 */
function readFile(string $filePath): string
{
    if (!file_exists($filePath)) {
        throw new Exception("File '$filePath' doesn't exists");
    }

    return file_get_contents($filePath);
}

/**
 * @param string $filePath
 * @return string
 * @throws Exception
 */
function extractExtension(string $filePath): string
{
    $path = pathinfo($filePath);
    return $path['extension'] ?? throw new Exception("File with unknown extension: $filePath");
}
