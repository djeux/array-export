<?php

declare(strict_types=1);

namespace Djeux\ArrayExport\Tests;

use Djeux\ArrayExport\Export;
use PHPUnit\Framework\TestCase;

class ExportTest extends TestCase
{
    public function testBasicExport(): void
    {
        $export = new Export();

        $this->assertSame(
            "[\n    'key_one' => 'key_two',\n]",
            $export->export(['key_one' => 'key_two'])
        );
    }

    public function testChaining(): void
    {
        $this->assertSame(
            "[\n    'key_one' => 'key_two',\n]",
            Export::make()->export(['key_one' => 'key_two'])
        );
    }

    public function testMultipleDifferentScalarValueTypes(): void
    {
        $arr = [
            1 => 'One',
            'Two' => 'Three',
            '4' => 5,
        ];

        $this->assertSame(
            "[\n    1 => 'One',\n    'Two' => 'Three',\n    4 => 5,\n]",
            Export::make()->export($arr)
        );
    }

    public function testNonAssociativeArray(): void
    {
        $arr = [
            'one', 'two', 'three', 'four',
        ];

        $this->assertSame(
            "[\n    'one',\n    'two',\n    'three',\n    'four',\n]",
            Export::make()->export($arr)
        );
    }

    public function testComplexArrays(): void
    {
        $arr = [
            1 => 'One',
            'Two' => 'Three',
            'nested' => [
                'four' => 'five',
                6 => 'Seven',
                'another_nested' => [
                    'more_nested' => [
                        'empty_nested' => []
                    ],
                    'just_a_value',
                ]
            ]
        ];

        $this->assertSame(
            "[\n    1 => 'One',\n    'Two' => 'Three',\n    ".
            "'nested' => [\n        'four' => 'five',\n        6 => 'Seven',\n        'another_nested' => [\n" .
            "            'more_nested' => [\n                'empty_nested' => [],\n            ],\n" .
            "            0 => 'just_a_value',\n        ],\n    ],\n]",
            Export::make()->export($arr)
        );
    }

    public function testStringsRequiringEscaping(): void
    {
        $array = [
            'one' => 'This is \'a more difficult \'string "to print"',
        ];

        $this->assertSame(
            "[\n    'one' => 'This is \'a more difficult \'string \"to print\"',\n]",
            Export::make()->export($array)
        );
    }

    public function testMultilineStringValue(): void
    {
        $array = [
            'string' => "This is \n a multiline \n string",
            'stringTwo' => 'This is \n not a multiline \n string',
        ];

        $this->assertSame(
            "[\n    'string' => 'This is \n a multiline \n string',\n    'stringTwo' => 'This is \\n not a multiline \\n string',\n]",
            Export::make()->export($array)
        );
    }

    public function testArrayWithHtmlData()
    {
        $array = [
            'html' => '<a href="url">This is a proper string</a>',
            'html2' => "<a href=\"url\">This is a proper string</a>"
        ];

        $this->assertSame(
            "[\n    'html' => '<a href=\"url\">This is a proper string</a>',\n    'html2' => '<a href=\"url\">This is a proper string</a>',\n]",
            Export::make()->export($array)
        );
    }
}