<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

use MyShoppress\DevOp\MustacheEnvy\TemplateEngine;
use PHPUnit\Framework\TestCase;

class TemplateHelperTest extends TestCase
{

    public function testPHPFunctionTemplateHelper(): void
    {
        $template = new TemplateEngine;
        $result = $template->render('{{strtolower "HELLO" }}');
        self::assertEquals($result,"hello");
        $result = $template->render('{{ucfirst "hello" }}');
        self::assertEquals($result,"Hello");
    }

    public function testArithmeticHelper(): void
    {
        $template = new TemplateEngine;
        $result = $template->render('{{add 1 1 }}');
        self::assertEquals(2, $result);
        $result = $template->render('{{sub 10 8 (mul 2 3) }}');
        self::assertEquals(-4, $result);
    }

    public function testTemplateHelperRequiredException(): void
    {
        $template = "{{ required VAR1 'VAR1 can not be null' }}";
        self::expectDeprecationMessage('VAR1 can not be null');
        $renderer = new TemplateEngine;
        $renderer->render($template,['VAR'=>'']);
    }

    public function testTemplateHelperRequiredReturnValue(): void
    {
        $renderer = new TemplateEngine;
        $result = $renderer->render('{{ required VAR1 }}',['VAR1'=>'hello']);
        self::assertEquals($result,'hello');
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
        $renderer = new TemplateEngine;
        $output = $renderer->render($template);
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
{{#if (eq "A" "A") }}
2 = 2
{{/if}}
{{#if (eq 1 '1') }}
1 = 1
{{/if}}
{{ ?: (gte 1 1) "1 is >= 1" "1 is less than 1" }}
{{ default SOME_VAR "defaultValue" }}
{{ add 2 3 10 }} Items
EOF;
        $renderer = new TemplateEngine;
        $output = $renderer->render($template);
        self::assertStringContainsString('SHOULD_BE_UPPERCASE', $output);
        self::assertStringContainsString('1 = 1', $output);
        self::assertStringContainsString('2 = 2', $output);
        self::assertStringContainsString('1 is >= 1', $output);
        self::assertStringContainsString('defaultValue', $output);
        self::assertStringContainsString('15 Items', $output);
    }

    public function testRenderer(): void
    {
        $template = <<<'EOF'
{{#if PART1}}PART1{{/if~}}
{{#if PART2}}PART2{{/if~}}
EOF;
        $renderer = new TemplateEngine;
        $output = $renderer->render($template, ['PART1'=>false, 'PART2'=>'NOTEMPTY']);
        self::assertStringNotContainsString('PART1', $output);
        self::assertStringContainsString('PART2', $output);
    }

    public function testDynamicPartial(): void
    {
        $template = <<<'EOF'
{{#*inline 'myPartial'}}
YOU CAN SEE THIS
{{#if (not true) }}
BUT NOT THIS
{{/if}}
{{/inline}}
{{> myPartial}}
EOF;
        $renderer = new TemplateEngine;
        $output = $renderer->render($template, ['VALUE'=>true]);
        self::assertStringContainsString('YOU CAN SEE THIS', $output);
        self::assertStringNotContainsString('BUT NOT THIS', $output);
    }

    public function testImportTemplateAsPartial(): void
    {
        $template = <<<'EOF'
{{> "/app/tests/files/template1.hbs" }}

{{> "tests/files/template3.hbs" }}
EOF;
        $assert = <<<'EOF'
Template 1
Template 2
Template 3
Template 2
EOF;
        $renderer = new TemplateEngine;
        $result = $renderer->render($template);
        self::assertEquals($assert, $result);
    }

    public function testEnvValueLookup(): void
    {
        $engine = new TemplateEngine;
        $result = $engine->render('{{ $ "key1.key2.key3" }}',[
            'key1' => ['key2'=>['key3'=>'hello']],
        ]);
        self::assertEquals($result, 'hello');
        $result = $engine->render('{{ $ "key1.key2.key3" }}',[
            'KEY1_KEY2_KEY3' => 'hello',
        ]);
        self::assertEquals($result, 'hello');
    }

}
