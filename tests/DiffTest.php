<?php

namespace Diff\Tests;

use Diff\Core\Formatter;
use PHPUnit\Framework\TestCase;

use function Diff\Core\createDiffTree;
use function Diff\Core\genDiff;

class DiffTest extends TestCase
{
    public function testStructureJson(): void
    {
        $diffString = genDiff(
            $this->getFixturePath('file1.1.json'),
            $this->getFixturePath('file1.2.json'),
        );

        $this->assertEquals(
            $diffString,
            file_get_contents($this->getFixturePath('diff1.json'))
        );
    }

    public function testStructureStylish(): void
    {
        $diffString = genDiff(
            $this->getFixturePath('file1.1.json'),
            $this->getFixturePath('file1.2.json'),
            Formatter::Stylish
        );

        $this->assertEquals(
            $diffString,
            file_get_contents($this->getFixturePath('diff1.stylish'))
        );

        $diffString2 = genDiff(
            $this->getFixturePath('file1.1.yaml'),
            $this->getFixturePath('file1.2.yaml'),
            Formatter::Stylish
        );

        $this->assertEquals(
            $diffString2,
            file_get_contents($this->getFixturePath('diff1.stylish'))
        );
    }

    public function testStructureText(): void
    {
        $diffString = genDiff(
            $this->getFixturePath('file1.1.json'),
            $this->getFixturePath('file1.2.json'),
            Formatter::PlainText
        );

        $this->assertEquals(
            $diffString,
            file_get_contents($this->getFixturePath('diff1.text'))
        );

        $diffString2 = genDiff(
            $this->getFixturePath('file1.1.yaml'),
            $this->getFixturePath('file1.2.yaml'),
            Formatter::PlainText
        );

        $this->assertEquals(
            $diffString2,
            file_get_contents($this->getFixturePath('diff1.text'))
        );
    }

    protected function getFixturePath(string $fixtureName): string
    {
        return __DIR__ . '/fixtures/' . $fixtureName;
    }
}
