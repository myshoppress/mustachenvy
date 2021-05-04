<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use function MyShoppress\DevOp\MustacheEnvy\castCallable;

class VariableHelpers implements ProviderInterface
{

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            'required' => castCallable(static::class.'::required'),
            'default' => castCallable(static::class.'::default'),
        ];
    }

    /**
     * @param mixed ...$args
     */
    public function default(...$args): string
    {
        \array_pop($args);

        //find first truthy value
        foreach($args as $value) {
            if ( (bool)$value ) {
                return $value;
            }
        }

        return '';
    }

    /**
     * @param mixed ...$args
     * @throws \ErrorException
     */
    public function required(...$args): void
    {
        $opts = \array_pop($args);
        $hash = $opts['hash'] ?? [];
        $strict = !isset($hash['strict']) || $hash['strict'] !== false;
        $missingVars = [];

        foreach($args as $arg) {
            if ( isset($opts['_this'][$arg]) && ($opts['_this'][$arg] || !$strict) ) {
                continue;
            }

            $missingVars[] = $arg;
        }

        if ( \count($missingVars) > 0 ) {
            throw new \ErrorException(\sprintf("%s variable(s) are missing.", \implode(',', $missingVars)));
        }
    }

}