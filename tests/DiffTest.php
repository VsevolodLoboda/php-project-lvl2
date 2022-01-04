<?php

namespace Diff\Tests;

use PHPUnit\Framework\TestCase;

use function Diff\Core\genDiff;
use const Diff\Core\JSON_FORMATTER;
use const Diff\Core\PLAIN_FORMATTER;
use const Diff\Core\STYLISH_FORMATTER;

class DiffTest extends TestCase
{
    public function testStructureJson(): void
    {
        $diffString = genDiff(
            $this->getFixturePath('file1.json'),
            $this->getFixturePath('file2.json'),
        );

        $this->assertEquals(
            $diffString,
            file_get_contents($this->getFixturePath('file-diff.json'))
        );
    }

    public function testStructureStylish(): void
    {
        $diffString = genDiff(
            $this->getFixturePath('file1.json'),
            $this->getFixturePath('file2.json'),
            STYLISH_FORMATTER
        );

        $this->assertEquals(
            $diffString,
            file_get_contents($this->getFixturePath('file-diff.stylish'))
        );

        $diffString2 = genDiff(
            $this->getFixturePath('file1.yaml'),
            $this->getFixturePath('file2.yaml'),
            STYLISH_FORMATTER
        );

        $this->assertEquals(
            $diffString2,
            file_get_contents($this->getFixturePath('file-diff.stylish'))
        );
    }

    public function testStructureText(): void
    {
        $diffString = genDiff(
            $this->getFixturePath('file1.json'),
            $this->getFixturePath('file2.json'),
            PLAIN_FORMATTER
        );

        $this->assertEquals(
            $diffString,
            file_get_contents($this->getFixturePath('file-diff.text'))
        );

        $diffString2 = genDiff(
            $this->getFixturePath('file1.yaml'),
            $this->getFixturePath('file2.yaml'),
            PLAIN_FORMATTER
        );

        $this->assertEquals(
            $diffString2,
            file_get_contents($this->getFixturePath('file-diff.text'))
        );
    }

    public function testEmptyFiles(): void
    {
        $fixture1 = $this->getFixturePath('empty1.json');
        $fixture2 = $this->getFixturePath('empty2.json');

        $this->assertEquals(
            genDiff($fixture1, $fixture2, STYLISH_FORMATTER),
            "{\n}"
        );

        $this->assertEquals(
            genDiff($fixture1, $fixture2, JSON_FORMATTER),
            '[]'
        );

        $this->assertEquals(
            genDiff($fixture1, $fixture2, PLAIN_FORMATTER),
            ''
        );
    }

    protected function getFixturePath(string $fixtureName): string
    {
        return __DIR__ . '/fixtures/' . $fixtureName;
    }
}
