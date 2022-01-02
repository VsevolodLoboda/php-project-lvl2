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

function parseJson(string $data): object
{
    return json_decode($data);
}

function parseYaml(string $data): object
{
    return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
}
