<?php

namespace Diff\Tests;

use Diff\Core\Formatter;
use PHPUnit\Framework\TestCase;

use function Diff\Core\createDiffTree;
use function Diff\Core\genDiff;

class DiffTest extends TestCase
{
    public function testFlatStructure(): void
    {
        $structure1 = json_decode(
            file_get_contents($this->getFixturePath('file1.1.json')),
            true
        );
        $structure2 = json_decode(
            file_get_contents($this->getFixturePath('file1.2.json')),
            true
        );

        $diffTree = createDiffTree($structure1, $structure2);

        $this->assertEquals(
            json_encode($diffTree),
            file_get_contents($this->getFixturePath('diff1_raw.json'))
        );
    }

    public function testFlatStructureStylized(): void
    {
        $diffString = genDiff(
            $this->getFixturePath('file1.1.json'),
            $this->getFixturePath('file1.2.json'),
            Formatter::Stylized
        );

        $this->assertEquals(
            $diffString,
            file_get_contents($this->getFixturePath('diff1.stylized'))
        );
    }

    private function getFixturePath(string $fixtureName): string
    {
        return __DIR__ . '/fixtures/' . $fixtureName;
    }
}
