<?php

// enum not supported yet: https://github.com/squizlabs/PHP_CodeSniffer/issues/3479
// @codingStandardsIgnoreFile

namespace Diff\Parser;

enum Type
{
    case Json;
    case Yaml;
}

function parserFactory(Type $type): callable
{
    return match ($type) {
        Type::Json => fn($data) => parseJson($data),
        Type::Yaml => fn($data) => parseYaml($data)
    };
}

function parseJson($data)
{
    return json_decode($data, true);
}

function parseYaml($data): string
{
    return (string)$data;
}
