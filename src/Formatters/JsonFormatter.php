<?php

namespace Diff\Formatters\Json;

function jsonOutputFormatter(array $diffTree): string
{
    return json_encode($diffTree);
}
