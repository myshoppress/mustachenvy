<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

final class OperatorHelperTest extends TestCase
{

    public function testArithmeticHelper(): void
    {
        self::assertEquals(2, self::render('{{add 1 1 }}'));
        self::assertEquals(-4, self::render('{{sub 10 8 (mul 2 3) }}'));
    }

    public function testInlineLogicHelper(): void
    {
        self::assertEmpty(self::render('{{not 1}}'));
        self::assertEmpty(self::render('{{and 1 false}}'));
        self::assertNotEmpty(self::render('{{and 1 "a"}}'));
        self::assertEmpty(self::render('{{or 0 ""}}'));
        self::assertEmpty(self::render('{{xor 1 1}}'));
        self::assertEquals(self::render('{{#and 1 "1"}}hello{{/and}}'),'hello');
    }

    public function testInlineCmpHelper(): void
    {
        self::assertEmpty(self::render('{{eq 1 "1"}}'));
        self::assertNotEmpty(self::render('{{gt "b" "a"}}'));
        self::assertNotEmpty(self::render('{{gte 10 10}}'));
        self::assertEquals(self::render('{{#neq 1 "1"}}hello{{/neq}}'),'hello');
    }

}
