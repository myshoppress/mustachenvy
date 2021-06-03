<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

class CmdHelperTest extends TestCase
{

    public function testEchoCmd(): void
    {
        self::render("{{ cmd 'echo' \"hello\" }}");
        self::assertEquals("hello",self::$result);
    }

}
