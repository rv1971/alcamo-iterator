<?php

namespace alcamo\iterator;

use PHPUnit\Framework\TestCase;

class FnmatchFilterIteratorTest extends TestCase
{
    /**
     * @dataProvider basicsProvider
     */
    public function testBasics(
        $iterator,
        $pattern,
        $flags,
        $expectedFlags,
        $expectedResultKeys
    ): void {
        if (!($iterator instanceof \Iterator)) {
            $iterator = new \ArrayIterator($iterator);
        }

        $filterIterator =
            new FnmatchFilterIterator($iterator, $pattern, $flags);

        $this->assertSame($iterator, $filterIterator->getInnerIterator());

        $this->assertSame($pattern, $filterIterator->getPattern());

        $this->assertSame($expectedFlags ?? $flags, $filterIterator->getFlags());

        $resultKeys = [];

        foreach ($filterIterator as $key => $value) {
            $resultKeys[] = $key;
        }

        sort($resultKeys);

        $this->assertSame($expectedResultKeys, $resultKeys);
    }

    public function basicsProvider()
    {
        return [
            [
                [ 'foo.txt', 'foo.json', 'bar.txt' ],
                '*.txt',
                null,
                0,
                [ 0, 2 ]
            ],
            [
                [ 'foo.txt' => 1, 'foo.json' => 2, 'bar.txt' => 3 ],
                'foo.*',
                FnmatchFilterIterator::FILTER_KEY,
                FnmatchFilterIterator::FILTER_KEY,
                [ 'foo.json', 'foo.txt' ]
            ],
            [
                new \FilesystemIterator(
                    __DIR__,
                    \FilesystemIterator::KEY_AS_FILENAME
                ),
                'foo.*',
                null,
                FnmatchFilterIterator::FILTER_KEY,
                [ 'foo.json', 'foo.txt' ]
            ],
            [
                new \FilesystemIterator(__DIR__),
                '*.txt',
                null,
                0,
                [
                    __DIR__ . DIRECTORY_SEPARATOR . 'bar.txt',
                    __DIR__ . DIRECTORY_SEPARATOR . 'foo.txt'
                ]
            ],
            [
                new \FilesystemIterator(
                    __DIR__,
                    \FilesystemIterator::CURRENT_AS_SELF
                ),
                '*.txt',
                null,
                0,
                [
                    __DIR__ . DIRECTORY_SEPARATOR . 'bar.txt',
                    __DIR__ . DIRECTORY_SEPARATOR . 'foo.txt'
                ]
            ]
        ];
    }
}
