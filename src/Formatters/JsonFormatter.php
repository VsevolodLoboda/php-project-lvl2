<?php

namespace Diff\Formatters;

function jsonOutputFormatter(array $diffTree): string
{
    return json_encode($diffTree);
}