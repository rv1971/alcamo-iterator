<?php

namespace alcamo\iterator;

use PHPUnit\Framework\TestCase;

class RecursiveDirectoryIteratorWithBlacklistTest extends TestCase
{
    /**
     * @dataProvider iterationProvider
     */
    public function testIteration(
        $directory,
        $flags,
        $dirnameBlacklist,
        $expectedData
    ): void {
        $iterator = new \RecursiveIteratorIterator(
            new RecursiveDirectoryIteratorWithBlacklist(
                $directory,
                $flags,
                $dirnameBlacklist
            ),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $this->assertSame($expectedData, iterator_to_array($iterator));
    }

    public function iterationProvider(): array
    {
        $ds = DIRECTORY_SEPARATOR;
        $dir = __DIR__ . "{$ds}recursive-glob-iterator-data";

        return [
            [
                $dir,
                RecursiveDirectoryIteratorWithBlacklist::KEY_AS_FILENAME
                    | RecursiveDirectoryIteratorWithBlacklist::CURRENT_AS_PATHNAME,
                null,
                [
                    '.' => "$dir{$ds}white{$ds}.",
                    '..' => "$dir{$ds}white{$ds}..",
                    'lorem.txt' => "$dir{$ds}black{$ds}lorem.txt",
                    'dolor.md' => "$dir{$ds}gray{$ds}dolor.md",
                    'consetetur.txt' => "$dir{$ds}white{$ds}consetetur.txt",
                    'sed.md' => "$dir{$ds}white{$ds}sed.md",
                    'ut.md' => "$dir{$ds}white{$ds}ut.md"
                ]
            ],
            [
                $dir,
                RecursiveDirectoryIteratorWithBlacklist::KEY_AS_FILENAME
                    | RecursiveDirectoryIteratorWithBlacklist::CURRENT_AS_PATHNAME,
                [ 'gray' ],
                [
                    '.' => "$dir{$ds}white{$ds}.",
                    '..' => "$dir{$ds}white{$ds}..",
                    'lorem.txt' => "$dir{$ds}black{$ds}lorem.txt",
                    'gray' => "$dir{$ds}gray",
                    'consetetur.txt' => "$dir{$ds}white{$ds}consetetur.txt",
                    'sed.md' => "$dir{$ds}white{$ds}sed.md",
                    'ut.md' => "$dir{$ds}white{$ds}ut.md"
                ]
            ],
            [
                $dir,
                RecursiveDirectoryIteratorWithBlacklist::KEY_AS_FILENAME
                    | RecursiveDirectoryIteratorWithBlacklist::CURRENT_AS_PATHNAME,
                [ 'gray', 'black' ],
                [
                    '.' => "$dir{$ds}white{$ds}.",
                    '..' => "$dir{$ds}white{$ds}..",
                    'black' => "$dir{$ds}black",
                    'gray' => "$dir{$ds}gray",
                    'consetetur.txt' => "$dir{$ds}white{$ds}consetetur.txt",
                    'sed.md' => "$dir{$ds}white{$ds}sed.md",
                    'ut.md' => "$dir{$ds}white{$ds}ut.md"
                ]
            ]
        ];
    }
}
