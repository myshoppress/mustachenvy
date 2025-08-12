<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use function MyShoppress\DevOp\MustacheEnvy\castCallable;

final class TernaryOperatorHelper implements ProviderInterface
{

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return ['?:' => castCallable(self::class.'::ternary')];
    }

    static public function ternary(mixed ...$args): mixed
    {
        \array_pop($args);

        if ( \count($args) !== 3) {
            throw new \UnexpectedValueException("ternary operation requires 1 boolean argument and 2 result arguments");
        }

        [$cond, $ifTrue, $ifFalse] = $args;
        return (bool)$cond
            ? $ifTrue
            : $ifFalse;
    }

}