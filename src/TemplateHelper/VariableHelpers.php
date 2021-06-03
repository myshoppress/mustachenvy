<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use MyShoppress\DevOp\MustacheEnvy\Compiler;
use function MyShoppress\DevOp\MustacheEnvy\castCallable;

class VariableHelpers implements ProviderInterface
{

    static private Compiler $compiler;

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
            '@' => castCallable(static::class.'::emptyString'),
            '$' => castCallable(static::class.'::dotEnvVariable'),
            'required' => castCallable(static::class.'::required'),
            'default' => castCallable(static::class.'::default'),
        ];
    }

    /**
     * @param mixed ...$args
     * @return mixed|null
     */
    static public function dotEnvVariable(...$args)
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

        if ( \strpos($resolvedValue, '{{') !== false ) {
            $renderer = self::$compiler->compile($resolvedValue);
            $resolvedValue = $renderer($data);
        }

        return $resolvedValue;
    }

    /**
     * @param mixed ...$args
     * @return mixed|string
     */
    static public function emptyString(...$args)
    {
        \array_pop($args);
        [$value] = $args;
        return $value ?? "";
    }

    /**
     * @param mixed ...$args
     * @return mixed
     */
    static public function default(...$args)
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
     * @return mixed
     */
    static public function required(...$args)
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