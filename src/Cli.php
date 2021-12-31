<?php

namespace Diff\Cli;

use Docopt;

const CLI_DOCUMENTATION = <<< DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]
DOC;

function run()
{
    $args = Docopt::handle(CLI_DOCUMENTATION, ['version' => '1.0']);
}
