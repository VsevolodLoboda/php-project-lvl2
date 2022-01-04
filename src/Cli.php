<?php

namespace Diff\Cli;

use Ahc\Cli\Input\Command;
use Diff\Core\Formatter;
use Exception;

use function Diff\Core\genDiff;

use const Diff\Parser\SUPPORTED_FORMATS;

function run()
{
    $command = new Command('gendiff', 'Check the difference between json/yaml files');
    $command->arguments('<firstFile> <secondFile>')
        ->option('-f|--format', 'Output format')
        ->parse($_SERVER['argv']);

    validateFilename($command->firstFile);
    validateFilename($command->secondFile);

    $result = genDiff($command->firstFile, $command->secondFile, match ($command->format) {
        'text' => Formatter::PlainText,
        'json' => Formatter::Json,
        default => Formatter::Stylish
    });

    print_r("$result\n");
}

function validateFilename(string $fileName): void
{
    $allowedExtension = array_reduce(SUPPORTED_FORMATS, function ($acc, $item) {
        return array_merge($acc ?? [], $item);
    });

    if (!in_array((pathinfo($fileName)['extension'] ?? null), $allowedExtension)) {
        throw new Exception("Unsupported file extension");
    }

    if (!file_exists($fileName)) {
        throw new Exception("File '$fileName' doesn't exists");
    }
}
