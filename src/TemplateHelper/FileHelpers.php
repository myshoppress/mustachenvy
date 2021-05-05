<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use function MyShoppress\DevOp\MustacheEnvy\castCallable;

class FileHelpers implements ProviderInterface
{
    use PHPFunctionsWrapperTrait;

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            'path_exists' => castCallable(static::class.'::pathExists')
        ];
    }

    /**
     * joins multiple path parts together. it doesn't care of any of parts starts with leading /. It will get rid of it
     *
     * @param mixed ...$args
     * @return mixed
     */
    static public function pathExists(...$args)
    {
        array_pop($args);
        $path = implode('/', $args);
        //get rid of // slashes
        $path = str_replace('//','/', $path);
        return file_exists($path);
    }

}