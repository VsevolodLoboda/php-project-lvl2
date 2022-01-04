<?php

namespace Diff\Tests;

use Diff\Core\Formatter;
use PHPUnit\Framework\TestCase;

use function Diff\Core\genDiff;

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
            Formatter::Stylish
        );

        $this->assertEquals(
            $diffString,
            file_get_contents($this->getFixturePath('file-diff.stylish'))
        );

        $diffString2 = genDiff(
            $this->getFixturePath('file1.yaml'),
            $this->getFixturePath('file2.yaml'),
            Formatter::Stylish
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
            Formatter::PlainText
        );

        $this->assertEquals(
            $diffString,
            file_get_contents($this->getFixturePath('file-diff.text'))
        );

        $diffString2 = genDiff(
            $this->getFixturePath('file1.yaml'),
            $this->getFixturePath('file2.yaml'),
            Formatter::PlainText
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
            genDiff($fixture1, $fixture2, Formatter::Stylish),
            "{\n}"
        );

        $this->assertEquals(
            genDiff($fixture1, $fixture2, Formatter::Json),
            '[]'
        );

        $this->assertEquals(
            genDiff($fixture1, $fixture2, Formatter::PlainText),
            ''
        );
    }

    protected function getFixturePath(string $fixtureName): string
    {
        return __DIR__ . '/fixtures/' . $fixtureName;
    }
}
