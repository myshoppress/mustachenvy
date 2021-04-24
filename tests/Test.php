<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate\Tests;

use MyShoppress\DevOp\ConfTemplate\InputParser;
use MyShoppress\DevOp\ConfTemplate\Renderer;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{

    public function testInput(): void
    {
        $array = InputParser::parseEnv("KEY1=123 Y=123");
        self::assertArrayHasKey('KEY1', $array);
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
