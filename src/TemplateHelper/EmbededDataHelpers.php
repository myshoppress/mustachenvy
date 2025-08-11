<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use Symfony\Component\Yaml\Yaml;
use function MyShoppress\DevOp\MustacheEnvy\castCallable;

class EmbededDataHelpers implements ProviderInterface
{

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            'json' => castCallable(static::class.'::json'),
            'yaml' => castCallable(static::class.'::yaml'),
        ];
    }

    /**
     * @param mixed ...$args
     * @return string|void
     * @throws \JsonException
     */
    static public function json(...$args)
    {
        $opts = \array_pop($args);

        //block - store in a variable passed
        if ( !isset($opts['fn']) ) {
            $json = \array_shift($args);
            return \json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
        }

        if ( !isset($args[0]) ) {
            throw new \RuntimeException(\sprintf("%s requires a variable to store the result", $opts['name']));
        }

        $varName = $args[0];
        $json = \json_decode($opts['fn'](), true, 512, \JSON_THROW_ON_ERROR);
        unset($opts['fn']);
        $opts['_this'][$varName] = $json;
    }

    /**
     * @param mixed ...$args
     * @return string|void
     */
    static public function yaml(...$args)
    {
        $opts = \array_pop($args);

        //block - store in a variable passed
        if ( !isset($opts['fn']) ) {
            $yaml = \array_shift($args);
            return Yaml::parse($yaml);
        }

        if ( !isset($args[0]) ) {
            throw new \RuntimeException(\sprintf("%s requires a variable to store the result", $opts['name']));
        }

        $varName = $args[0];
        $yaml = Yaml::parse($opts['fn']());
        $opts['_this'][$varName] = $yaml;
    }

}
