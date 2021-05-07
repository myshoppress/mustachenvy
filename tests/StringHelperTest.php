<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

class StringHelperTest extends TestCase
{

    public function testSubstr(): void
    {
        self::assertEquals("ello", self::render('{{substr "hello" 1}}'));
    }

    public function testStrLen(): void
    {
        self::assertEquals(5, self::render('{{strlen "hello" }}'));
    }

    public function testStrPos(): void
    {
        self::assertEquals(2, self::render('{{strpos "hello" "l" }}'));
    }

    public function testStrReplace(): void
    {
        self::assertEquals("lello", self::render('{{str_replace "h" "l" "hello"}}'));
    }

    public function testRegMatch(): void
    {
        self::assertEquals(
            "i can see this",
            self::render('{{#preg_match "/w[a-z].(l|a)dx?/" "world"}}i can see this{{/preg_match}}'),
        );
    }

    public function testNegatingEmpty(): void
    {
        self::assertEquals("this is not empty",self::render('{{#-strlen "" }}this is not empty{{/-strlen}}'));
    }

    public function testPregReplace(): void
    {
        self::assertEquals("$23", self::render('{{preg_replace "/[a-z]+(\d+)/" "$\1" "abc23"}}'));
    }

    public function testStringConcat(): void
    {
        self::render('{{ concat "A" "B" separator="," }}');
        self::assertEquals(self::$result,"A,B");
    }

}
