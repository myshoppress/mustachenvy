<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use Webmozart\Assert\Assert;
use function MyShoppress\DevOp\MustacheEnvy\castCallable;

trait PHPFunctionsWrapperTrait
{

    /**
     * @param mixed ...$args
     * @return mixed
     */
    static public function callPHPFunction(...$args)
    {
        $opts = \array_pop($args);
        $result = \call_user_func_array($opts['name'], $args);
        $result = (bool)$result && isset($opts['fn'])
            ? $opts['fn']()
            : $result;
        return $result;
    }

    /**
     * @return array<string, callable>
     */
    static protected function wrapPHPFunctions(string ...$functions): array
    {
        Assert::allIsCallable($functions);
        return \array_fill_keys($functions, castCallable(static::class.'::callPHPFunction'));
    }

}