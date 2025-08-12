<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use Symfony\Component\Yaml\Yaml;
use function MyShoppress\DevOp\MustacheEnvy\castCallable;

final class EmbededDataHelpers implements ProviderInterface
{

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            'json' => castCallable(self::class.'::json'),
            'yaml' => castCallable(self::class.'::yaml'),
        ];
    }

    /**
     * @throws \JsonException
     */
    static public function json(mixed ...$args): string|null
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
        return null;
    }

    static public function yaml(mixed ...$args): string|null
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
        return null;
    }

}
