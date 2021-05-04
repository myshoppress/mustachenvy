<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use function MyShoppress\DevOp\MustacheEnvy\castCallable;

class PHPFunctionHelpers implements ProviderInterface
{

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        $strMethods = [
            'strlen','str_word_count','strpos','str_replace','ucwords','strtoupper','strtolower','ucfirst','str_repeat',
            'substr','trim','strrev','strcmp',
        ];
        return \array_fill_keys($strMethods, castCallable(static::class.'::nativePHPMethod'));
    }

    /**
     * @param mixed ...$args
     * @return false|mixed
     */
    static public function nativePHPMethod(...$args)
    {
        $opts = \array_pop($args);
        return \call_user_func_array($opts['name'], $args);
    }

}