<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use MyShoppress\DevOp\MustacheEnvy\Compiler;
use function MyShoppress\DevOp\MustacheEnvy\castCallable;

final class VariableHelpers implements ProviderInterface
{

    private static Compiler $compiler;

    public function __construct(Compiler $compiler)
    {
        self::$compiler = $compiler;
    }

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            '$' => castCallable(self::class.'::dotEnvVariable'),
            '@' => castCallable(self::class.'::emptyString'),
            'default' => castCallable(self::class.'::default'),
            'required' => castCallable(self::class.'::required'),
        ];
    }

    static public function dotEnvVariable(mixed ...$args): mixed
    {
        $opt = \array_pop($args);
        [$varName, $defaultValue] = $args + [null, null];

        if ( $varName === '') {
            throw new \InvalidArgumentException("Variable name can not be empty");
        }

        $data = $opt['_this'] ?? [];
        $envName = \strtoupper(\str_replace('.','_', $varName));
        $envValue = $data[$envName] ?? null;
        //now lets check if $t_context contains a data structure representing the dot notation
        $keys = \explode('.', $varName);

        foreach($keys as $k) {
            $dotValue = $data[$k] ?? null;
            $data = $data[$k] ?? [];
        }

        $resolvedValue = $dotValue ?? $envValue ?? $defaultValue;

        if ( \strpos((string)$resolvedValue, '{{') !== false ) {
            $renderer = self::$compiler->compile($resolvedValue);
            $resolvedValue = $renderer($data);
        }

        return $resolvedValue;
    }

    static public function emptyString(mixed ...$args): mixed
    {
        \array_pop($args);
        [$value] = $args;
        return $value ?? "";
    }

    static public function default(mixed ...$args): mixed
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
     * @throws \ErrorException
     */
    static public function required(mixed ...$args): mixed
    {
        \array_pop($args);
        [$value, $errorMessage] = $args;
        $errorMessage ??= "Value can not be null";

        if ( $value === null || $value === '' ) {
            throw new \ErrorException($errorMessage);
        }

        return $value;
    }

}
