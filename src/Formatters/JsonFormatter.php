<?php

namespace Diff\Formatters\Json;

/**
 * @param array $diffTree
 * @return string
 */
function jsonOutputFormatter(array $diffTree): string
{
    return json_encode($diffTree);
}
