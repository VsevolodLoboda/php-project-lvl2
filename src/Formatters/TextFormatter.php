<?php

namespace Diff\Formatters;

function textOutputFormatter(array $diffTree): string
{
    return json_encode($diffTree);
}
