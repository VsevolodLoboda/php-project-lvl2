<?php

namespace Differ\Formatters\Json;

/**
 * @param array $diffTree
 * @return string
 */
function jsonFormatter(array $diffTree): string
{
    return json_encode($diffTree);
}
