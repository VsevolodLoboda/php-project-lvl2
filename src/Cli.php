<?php

namespace Diff\Cli;

use Ahc\Cli\Input\Command;
use Exception;

use function Diff\Core\genDiff;

use const Diff\Core\JSON_FORMATTER;
use const Diff\Core\PLAIN_FORMATTER;
use const Diff\Core\STYLISH_FORMATTER;
use const Diff\Parser\SUPPORTED_FORMATS;

/**
 * Run console app
 * @throws Exception
 */
function run()
{
    $command = new Command('gendiff', 'Check the difference between json/yaml files');
    $command->arguments('<firstFile> <secondFile>')
        ->option('-f|--format', 'Output format')
        ->parse($_SERVER['argv']);

    try {
        $file1 = $command->firstFile;
        $file2 = $command->secondFile;
        $format = $command->format;

        validateFilename($file1);
        validateFilename($file2);

        $result = genDiff($file1, $file2, match ($format) {
            'plain' => PLAIN_FORMATTER,
            'json' => JSON_FORMATTER,
            default => STYLISH_FORMATTER
        });

        print_r("$result\n");
    } catch (Exception $e) {
        if ($command->verbosity) {
            throw $e;
        }
        print_r(
            'Application error: '
            . $e->getMessage()
            . str_repeat("\n", 2)
        );
    }
}

/**
 * Validate file path
 *
 * @param string $fileName
 * @throws Exception
 */
function validateFilename(string $fileName): void
{
    if (!file_exists($fileName)) {
        throw new Exception("File '$fileName' doesn't exists");
    }

    // Collapse all supported extension in flat array
    $allowedExtension = array_reduce(SUPPORTED_FORMATS, function ($acc, $item) {
        return array_merge($acc ?? [], $item);
    });

    if (!in_array((pathinfo($fileName)['extension'] ?? null), $allowedExtension)) {
        throw new Exception("Unsupported file extension");
    }
}
