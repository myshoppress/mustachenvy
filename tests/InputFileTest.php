<?php

declare(strict_types = 1);

use MyShoppress\DevOp\MustacheEnvy\InputFileParser\Parser;
use MyShoppress\DevOp\MustacheEnvy\InputValues;
use PHPUnit\Framework\TestCase;

class InputFileTest extends TestCase
{

    public function testParsingInputFile(): void
    {
        $parser = new Parser;
        $values = $parser->parse(__DIR__.'/files/input.env');
        self::assertArrayHasKey('KEY2_VALUE2_SUBVALUE2', $values);
        $values = $parser->parse(__DIR__.'/files/input.yaml');
        self::assertEquals($values['key2']['value2']['subvalue2'] ?? '','hello there');
        $values = $parser->parse(__DIR__.'/files/input.json');
        self::assertEquals($values['key2']['value2']['subvalue2'] ?? '','hello there');
        self::expectException(\InvalidArgumentException::class);
        $parser->parse(__DIR__.'/files/invalid.format');
    }

    public function testInputValues(): void
    {
        $values = new InputValues;
        $values->addInputFile(__DIR__.'/files/input.env');
        self::assertEquals($values['KEY2_VALUE2_SUBVALUE2'] ?? '', 'hello there');
        $values->setValues(['KEY2_VALUE2_SUBVALUE2'=>'new world']);
        self::assertEquals($values['KEY2_VALUE2_SUBVALUE2'] ?? '', 'new world');
    }

}