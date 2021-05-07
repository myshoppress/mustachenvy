<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use LightnCandy\Runtime;
use MyShoppress\DevOp\MustacheEnvy\Compiler;
use SplStack;
use Webmozart\Assert\Assert;
use function MyShoppress\DevOp\MustacheEnvy\castCallable;

class FileHelpers implements ProviderInterface
{

    use PHPFunctionsWrapperTrait;

    static private Compiler $compiler;

    /**
     * @var SplStack<string>
     */
    static private SplStack $paths;

    public function __construct(Compiler $compiler, ?string $currentPath = null)
    {
        self::$compiler = $compiler;
        self::$paths = new SplStack;
        self::$paths->push($currentPath ?? \getcwd());
    }

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            'import' => castCallable(static::class.'::importFile'),
            'path_join' => castCallable(static::class.'::pathJoin'),
            'path_exists' => castCallable(static::class.'::pathExists'),
        ];
    }

    /**
     * @param mixed ...$args
     */
    static public function importFile(...$args): string
    {
        $opts = \array_pop($args);
        [$file] = $args;
        $hash = $opts['hash'] ?? [];
        $errorOnInvalid = $hash['error_on_invalid'] ?? !isset($opts['fn']);
        $compileContent = isset(self::$compiler) && ($hash['compile'] ?? true);
        $mergeVar = $hash['merge_vars'] ?? true;
        $path = self::getPath($file ?? '');

        if ( $path === null || !\is_file($path) ) {
            if ( $errorOnInvalid === false )
                //if path is invalid then either return the block or empty
                return isset($opts['fn'])
                    ? $opts['fn']()
                    : '';

            throw new \InvalidArgumentException(\sprintf("%s is not a file", $file));
        }

        $content = \file_get_contents($path);
        Assert::string($content);

        if ( $compileContent )
        {
            self::$paths->push(\dirname($path));
            $renderer = self::$compiler->compile($content);
            self::$paths->pop();
            unset($hash['compile'], $hash['error_on_invalid'], $hash['merge_vars']);
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
    static public function pathJoin(...$args): ?string
    {
        \array_pop($args);
        $path = \implode('/', $args);
        //get rid of // slashes
        $path = \preg_replace('/\/+/','/', $path);
        return $path;
    }

    /**
     * joins multiple path parts together. it doesn't care of any of parts starts with leading /. It will get rid of it
     *
     * @param mixed ...$args
     * @return string|bool
     */
    static public function pathExists(...$args)
    {
        $opts = \array_pop($args);
        $path = \implode('/', $args);
        //get rid of // slashes
        $path = \preg_replace('/\/+/','/', $path);
        $path = self::getPath($path ?? '');
        $result = $path !== null;
        return $result && isset($opts['fn'])
            ? $opts['fn']()
            : $result;
    }

    private static function getPath(string $path): ?string
    {
        //check if path exist.
        if ( \is_file($path) ) {
            return $path;
        }

        //check if path exists relative to the last path inserted into stack
        $base = self::$paths->top();
        $newPath = $base.'/'.$path;
        return \file_exists($newPath)
            ? $newPath
            : null;
    }

}