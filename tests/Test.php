<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate\Tests;

use MyShoppress\DevOp\ConfTemplate\InputParser;
use MyShoppress\DevOp\ConfTemplate\Renderer;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{

    public function testTemplateHelperRequired(): void
    {
        $template = <<<'EOF'
{{ required 'VAR1' 'VAR2' 'VAR3' 'VAR4' }}
EOF;
        self::expectExceptionMessageMatches('/VAR3,VAR4 variable/');
        $renderer = new Renderer;
        $renderer->render($template,[
            'VAR1' => '1','VAR2'=>'2',
        ]);
    }

    public function testTemplateHelperJson(): void
    {
        $template = <<<'EOF'
{{#json 'List' }}
[ 
 {"id":1,"name":"John"},
 {"id":2,"name":"Jody"}
]
{{/json}}
{{#each List }}
Name is {{name}}
{{/each}}
{{#each (json "[1,2]") }}
Index {{@index}}
{{/each}}
EOF;
        $renderer = new Renderer;
        $output = $renderer->render($template, [

        ]);
        $assert = <<<'EOF'
Name is John
Name is Jody
Index 0
Index 1
EOF;
        self::assertStringContainsString($assert, $output);
    }

    public function testTempalteHelperOperands(): void
    {
        $template = <<<'EOF'
{{ strtoupper "should_be_uppercase" }}
{{ eq 1 1 }}
{{#if (eq 1 '1') }}
1 = 1
{{/if}}
{{ ?: (gte 1 1) "1 is >= 1" "1 is less than 1" }}
{{ default SOME_VAR "defaultValue" }}
{{ add 2 3 10 }} Items
EOF;
        $renderer = new Renderer;
        $output = $renderer->render($template, [

        ]);
        self::assertStringContainsString('SHOULD_BE_UPPERCASE', $output);
        self::assertStringContainsString('1 = 1', $output);
        self::assertStringContainsString('1 is >= 1', $output);
        self::assertStringContainsString('defaultValue', $output);
        self::assertStringContainsString('15 Items', $output);
    }

    public function testInputParser(): void
    {
        $parser = new InputParser;
        $parser->addInputFile(__DIR__.'/../examples/build.env');
        $values = $parser->getValues();
        self::assertEquals('level2', $values['NGINX_LOG_LEVEL']);
    }

    public function testInputParserFileWithHeader(): void
    {
        $parser = new InputParser;
        $parser->addInputFile(__DIR__.'/../examples/build.env?HEADER1');
        $values = $parser->getValues();
        self::assertEquals('level1', $values['NGINX_LOG_LEVEL']);
    }

    public function testInput(): void
    {
        $array = InputParser::parseKeyValuePairs(["KEY1=VALUE1", "KEY2=\"VALUE 2\""]);
        self::assertArrayHasKey('KEY1', $array);
        self::assertCount(2, $array);

    }

    public function testRenderer(): void
    {
        $template = <<<'EOF'
{{#if PART1}}PART1{{/if~}}
{{#if PART2}}PART2{{/if~}}
EOF;
        $renderer = new Renderer;
        $output = $renderer->render($template, ['PART1'=>false, 'PART2'=>'NOTEMPTY']);
        self::assertStringNotContainsString('PART1', $output);
        self::assertStringContainsString('PART2', $output);
    }

    public function testDynamicPartial(): void
    {
        $template = <<<'EOF'
{{#*inline 'myPartial'}}
YOU CAN SEE THIS
{{/inline}}
{{> myPartial}}
EOF;
        $renderer = new Renderer;
        $output = $renderer->render($template, ['VALUE'=>true]);
        self::assertStringContainsString('YOU CAN SEE THIS', $output);
    }

}
