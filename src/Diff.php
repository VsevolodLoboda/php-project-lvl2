<?php

namespace Differ\Differ;

use Exception;

use function Functional\sort;
use function Differ\Parser\parseFile;
use function Differ\Formatters\format;

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
    $diffTree = createDiffTree(
        parseFile(readFile($filePath1), extractExtension($filePath1)),
        parseFile(readFile($filePath2), extractExtension($filePath1)),
    );

    return format($diffTree, $formatter);
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
function extractExtension(string $filePath): string
{
    $path = pathinfo($filePath);
    return $path['extension'] ?? throw new Exception("Undefined file extension: $filePath");
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
