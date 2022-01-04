<?php

// @codingStandardsIgnoreFile
// Enum type not supported yet: https://github.com/squizlabs/PHP_CodeSniffer/issues/3479

namespace Diff\Core;

use Exception;

use function Diff\Formatters\Json\jsonOutputFormatter;
use function Diff\Formatters\Stylish\stylishOutputFormatter;
use function Diff\Formatters\Text\textOutputFormatter;
use function Diff\Parser\parse;

enum Formatter: string
{
    case Json = 'json';
    case Stylish = 'stylish';
    case PlainText = 'plain';
}

enum DiffStatus: string
{
    case Added = 'added';
    case Deleted = 'deleted';
    case Updated = 'updated';
    case Same = 'same';
    case Collection = 'collection';
}

/**
 * @throws Exception
 */
function genDiff(string $filePath1, string $filePath2, Formatter $formatter = Formatter::Json): string
{
    $ext1 = strtolower(pathinfo($filePath1)['extension']);
    $ext2 = strtolower(pathinfo($filePath2)['extension']);

    $diffTree = createDiffTree(
        parse($ext1, file_get_contents($filePath1)),
        parse($ext2, file_get_contents($filePath2)),
    );

    return match ($formatter) {
        Formatter::Json => jsonOutputFormatter($diffTree),
        Formatter::Stylish => stylishOutputFormatter($diffTree),
        Formatter::PlainText => textOutputFormatter($diffTree)
    };
}

function createDiffTree(object $structure1, object $structure2): array
{
    $keys = array_unique(
        array_merge(
            array_keys(get_object_vars($structure1)),
            array_keys(get_object_vars($structure2))
        )
    );

    sort($keys);

    return array_map(function ($key) use ($structure1, $structure2) {
        $val1 = $structure1->$key ?? null;
        $val2 = $structure2->$key ?? null;

        if (!property_exists($structure1, $key)) {
            return [
                'key' => $key,
                'val1' => $val2,
                'status' => DiffStatus::Added
            ];
        }

        if (!property_exists($structure2, $key)) {
            return [
                'key' => $key,
                'val1' => $val1,
                'status' => DiffStatus::Deleted
            ];
        }

        if (is_object($val1) && is_object($val2)) {
            return [
                'key' => $key,
                'status' => DiffStatus::Collection,
                'collection' => createDiffTree($val1, $val2)
            ];
        }

        if ($val1 !== $val2) {
            return [
                'key' => $key,
                'val1' => $val1,
                'val2' => $val2,
                'status' => DiffStatus::Updated
            ];
        }

        return [
            'key' => $key,
            'val1' => $val1,
            'status' => DiffStatus::Same
        ];
    }, $keys);
}
