<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use LightnCandy\Runtime;
use MyShoppress\DevOp\MustacheEnvy\Compiler;
use Webmozart\Assert\Assert;
use function MyShoppress\DevOp\MustacheEnvy\castCallable;

class FileHelpers implements ProviderInterface
{

    use PHPFunctionsWrapperTrait;

    static private ?Compiler $compiler;

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            'file_content' => castCallable(static::class.'::fileContent'),
            'path_join' => castCallable(static::class.'::pathJoin'),
            'path_exists' => castCallable(static::class.'::pathExists'),
        ];
    }

    static public function setCompiler(Compiler $compiler): void
    {
        self::$compiler = $compiler;
    }

    /**
     * @param mixed ...$args
     */
    static public function fileContent(...$args): string
    {
        $opts = \array_pop($args);
        [$file] = $args;
        $hash = $opts['hash'] ?? [];
        $ignoreInvalidPath = $hash['ignoreInvalidPath'] ?? false;
        $compile = $hash['compile'] ?? false;
        $mergeVar = $hash['mergeVar'] ?? true;

        if ( $file === null || !\is_file($file) ) {
            if ( $ignoreInvalidPath )
                return '';
            else
                Assert::file($file);
        }

        $content = \file_get_contents($file);
        Assert::string($content);

        if ( isset(self::$compiler) && $compile )
        {
            self::$compiler->addPartialSearchPath(\dirname($file));
            $renderer = self::$compiler->compile($content);
            unset($hash['compile']);
            unset($hash['ignoreInvalidPath']);
            $vars = $mergeVar
                ? \array_merge($opts['_this'], $hash)
                : $hash;
            $content = $renderer($vars,[
                'debug' => Runtime::DEBUG_ERROR_EXCEPTION,
            ]);
        }

        return $content;
    }

    /**
     * @param mixed ...$args
     */
    static public function pathJoin(...$args): string
    {
        \array_pop($args);
        $path = \implode('/', $args);
        //get rid of // slashes
        $path = \str_replace('//','/', $path);
        return $path;
    }

    /**
     * joins multiple path parts together. it doesn't care of any of parts starts with leading /. It will get rid of it
     *
     * @param mixed ...$args
     */
    static public function pathExists(...$args): bool
    {
        \array_pop($args);
        $path = \implode('/', $args);
        //get rid of // slashes
        $path = \str_replace('//','/', $path);
        return \file_exists($path);
    }

}