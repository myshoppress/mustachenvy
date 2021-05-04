<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy;

use Webmozart\Assert\Assert;

function castCallable(string $method): callable
{
    Assert::isCallable($method);
    return $method;
}