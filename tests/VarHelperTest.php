<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

class VarHelperTest extends TestCase
{

    public function testEmptyRequiredVar(): void
    {
        self::expectExceptionMersage('VAR1 can not be null');
        self::render("{{ required VAR1 'VAR1 can not be null' }}");
    }

    public function testDefaultValueReplacement(): void
    {
        self::assertEquals('VALUE2', self::render("{{ default VAR1 'VALUE2'}}"));
    }

    public function testEnvLookupFallback(): void
    {
        self::render('{{ $ "key1.key2.key3" }}', [
            'key1' => ['key2'=>['key3'=>'hello']],
        ]);
        self::assertEquals('hello', self::$result);
        self::render('{{ $ "key1.key2.key3" }}', [
            'KEY1_KEY2_KEY3' => 'hello',
        ]);
        self::assertEquals('hello', self::$result);
    }

    public function testEvaluatingTemplateFragment(): void
    {
        self::render('{{ $ "key1.key2"   }}', [
            'KEY3' => 4,
            'KEY1_KEY2' => '{{ add 1 4 }}',
        ]);
        self::assertEquals(5, self::$result);
    }

}
