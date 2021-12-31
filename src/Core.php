<?php

namespace Diff\Core;

use PHP_CodeSniffer\Reports\Diff;
use function Diff\Parser\parserFactory;
use Diff\Parser\Type;
use function Diff\Formatter\styleOutputFormatter;

enum DiffStatus: string
{
    case Added = 'added';
    case Deleted = 'deleted';
    case Updated = 'updated';
    case Same = 'same';
    case Collection = 'collection';
}

function genDiff(string $filePath1, string $filePath2)
{
    $ext1 = pathinfo($filePath1)['extension'];
    $ext2 = pathinfo($filePath2)['extension'];

    $getType = function (string $ext) {
        return match ($ext) {
            'json' => Type::Json,
            'yaml', 'yml' => Type::Yaml
        };
    };

    return styleOutputFormatter(createDiffTree(
        parserFactory($getType($ext1))(file_get_contents($filePath1)),
        parserFactory($getType($ext2))(file_get_contents($filePath2)),
    ));
}

function createDiffTree(array $structure1, array $structure2): array
{
    $keys = array_unique(
        array_merge(
            array_keys($structure1),
            array_keys($structure2)
        )
    );

    sort($keys);

    return array_map(function ($key) use ($structure1, $structure2) {
        $val1 = $structure1[$key] ?? null;
        $val2 = $structure2[$key] ?? null;

        if (!key_exists($key, $structure1)) {
            return [
                'key' => $key,
                'val1' => $val2,
                'status' => DiffStatus::Added
            ];
        }

        if (!key_exists($key, $structure2)) {
            return [
                'key' => $key,
                'val1' => $val1,
                'status' => DiffStatus::Deleted
            ];
        }

        if (is_array($val1) && is_array($val2)) {
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
