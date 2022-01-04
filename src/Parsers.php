<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

const SUPPORTED_FORMATS = [
    'yaml' => ['yaml', 'yml'],
    'json' => ['json']
];

/**
 * @param string $fileExtension
 * @param string $data
 * @return object
 * @throws Exception
 */
function parse(string $fileExtension, string $data): object
{
    return match (true) {
        in_array($fileExtension, SUPPORTED_FORMATS['json']) => parseJson($data),
        in_array($fileExtension, SUPPORTED_FORMATS['yaml']) => parseYaml($data),
        default => throw new Exception("Unable to find parser for file with extension '.$fileExtension'")
    };
}

/**
 * @param string $data
 * @return object
 */
function parseJson(string $data): object
{
    return json_decode($data);
}

/**
 * @param string $data
 * @return object
 */
function parseYaml(string $data): object
{
    return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
}
