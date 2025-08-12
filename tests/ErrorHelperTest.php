<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

final class ErrorHelperTest extends TestCase
{

    public function testError(): void
    {
        self::expectExceptionMessage("You should see this error");
        self::render('{{error "You should see this error"}}');
    }

}
