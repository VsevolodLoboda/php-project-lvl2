<?php

namespace Differ\Formatters;

use Exception;

use function Differ\Formatters\Json\jsonFormatter;
use function Differ\Formatters\Stylish\stylishFormatter;
use function Differ\Formatters\Text\textFormatter;

// TODO: Replace to enum
const JSON_FORMATTER = 'json';
const STYLISH_FORMATTER = 'stylish';
const PLAIN_FORMATTER = 'plain';

/**
 * @param array $diffTree
 * @param string $formatter
 * @return string
 * @throws Exception
 */
function format(array $diffTree, string $formatter): string
{
    return match ($formatter) {
        JSON_FORMATTER => jsonFormatter($diffTree),
        STYLISH_FORMATTER => stylishFormatter($diffTree),
        PLAIN_FORMATTER => textFormatter($diffTree),
        default => throw new Exception("Unknown formatter: $formatter")
    };
}
