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

    public function testTemplateHelperRequiredStrict(): void
    {
        $template = "{{ required 'VAR1' 'VAR2' 'VAR3' 'VAR4' }}";
        self::expectExceptionMessageMatches('/ VAR3,VAR4 variable\(s\) are missing/');
        $values = [
            'VAR1' => '1','VAR2'=>'2','VAR3'=>'',
        ];
        $renderer = new TemplateEngine;
        $renderer->render($template,$values);
        $template = "{{ required 'VAR1' 'VAR2' 'VAR3' 'VAR4' strict=false }}";
        self::expectExceptionMessageMatches('/VAR4 variable/');
        $renderer->render($template, $values);
    }

    public function testTemplateHelperRequiredNotStrict(): void
    {
        $values = [
            'VAR1' => '1','VAR2'=>'2','VAR3'=>'',
        ];
        $renderer = new TemplateEngine;
        $template = "{{ required 'VAR1' 'VAR2' 'VAR3' 'VAR4' strict=false }}";
        self::expectExceptionMessageMatches('/ VAR4 variable\(s\) are missing/');
        $renderer->render($template, $values);
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
//    public function testInputParserOrder(): void
//    {
//        $parser = new InputParser;
//        $parser->addInputFile(__DIR__.'/../examples/build.env');
//        $parser->addEnvValues(['NGINX_LOG_LEVEL'=>'level3']);
//        $parser->addKeyValuePairs(['NGINX_LOG_LEVEL=level4']);
//        $values = $parser->getValues();
//        self::assertEquals('level2', $values['NGINX_LOG_LEVEL']);
//        $parser->setOrder('fke');
//        $values = $parser->getValues();
//        self::assertEquals('level3', $values['NGINX_LOG_LEVEL']);
//        $parser->setOrder('fek');
//        $values = $parser->getValues();
//        self::assertEquals('level4', $values['NGINX_LOG_LEVEL']);
//        self::expectException(\InvalidArgumentException::class);
//        $parser->setOrder('efx');
//    }
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

}
