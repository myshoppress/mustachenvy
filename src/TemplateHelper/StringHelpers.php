<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use function MyShoppress\DevOp\MustacheEnvy\castCallable;

class StringHelpers implements ProviderInterface
{

    use PHPFunctionsWrapperTrait;

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        $strMethods = [
            'strlen','-strlen','strpos','-strpos','str_replace',
            'ucwords','strtoupper','strtolower','ucfirst','str_repeat',
            'substr','preg_match','preg_replace','-preg_match',
        ];
        $helpers = self::wrapPHPFunctions(...$strMethods);
        $helpers = \array_merge($helpers, [
            'concat' => castCallable(static::class.'::concat'),
        ]);
        return $helpers;
    }

    /**
     * @param mixed ...$args
     */
    static public function concat(...$args): string
    {
        $opts = \array_pop($args);
        $glue = $opts['hash']['separator'] ?? '';
        return \implode($glue, $args);
    }

}
