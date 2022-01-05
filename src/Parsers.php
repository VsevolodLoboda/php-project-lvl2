<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

const SUPPORTED_FORMATS = [
    'yaml' => ['yaml', 'yml'],
    'json' => ['json']
];

/**
 * @param string $path
 * @return object
 * @throws Exception
 */
function parseFile(string $path): object
{
    $ext = strtolower(extractExtension($path));

    return match (true) {
        in_array($ext, SUPPORTED_FORMATS['json'], true) => parseJson(readFile($path)),
        in_array($ext, SUPPORTED_FORMATS['yaml'], true) => parseYaml(readFile($path)),
        default => throw new Exception("Unable to find parser for file with extension '.$ext'")
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

/**
 * @param string $filePath
 * @return string
 * @throws Exception
 */
function extractExtension(string $filePath): string
{
    $path = pathinfo($filePath);
    return $path['extension'] ?? throw new Exception("File with unknown extension: $filePath");
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
