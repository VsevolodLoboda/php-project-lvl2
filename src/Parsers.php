<?php

namespace Diff\Parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

const SUPPORTED_FORMATS = [
    'yaml' => ['yaml', 'yml'],
    'json' => ['json']
];

function getParserInstance(string $fileExtension): callable
{
    return match (true) {
        in_array($fileExtension, SUPPORTED_FORMATS['json']) => fn($data) => parseJson($data),
        in_array($fileExtension, SUPPORTED_FORMATS['yaml']) => fn($data) => parseYaml($data),
        default => throw new Exception("Unable to find parser for file with extension '.{$fileExtension}'. ")
    };
}

function parseJson(string $data): array
{
    return json_decode($data, true);
}

function parseYaml(string $data): array
{
    return Yaml::parse($data);
}
