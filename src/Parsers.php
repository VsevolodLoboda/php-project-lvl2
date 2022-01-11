<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

const SUPPORTED_FORMATS = [
    'yaml' => ['yaml', 'yml'],
    'json' => ['json']
];

/**
 * @param string $data
 * @param string $format
 * @return object
 * @throws Exception
 */
function parseFile(string $data, string $format): object
{
    $ext = strtolower($format);
    return match (true) {
        in_array($ext, SUPPORTED_FORMATS['json'], true) => parseJson($data),
        in_array($ext, SUPPORTED_FORMATS['yaml'], true) => parseYaml($data),
        default => throw new Exception("Unable to find parser for format '$ext'")
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
