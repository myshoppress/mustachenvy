<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Partial;

class PartialResolver implements ResolverInterface
{

    /**
     * @var array<string>
     */
    private array $templateSearchPaths = [];

    public function addSearchPath(string $path): void
    {
        \array_unshift($this->templateSearchPaths, $path);
        $this->templateSearchPaths = \array_unique($this->templateSearchPaths);
    }

    public function resolvePartial(string $name): ?string
    {
        $result = null;

        if ( \is_file($name) ) {
            $this->addSearchPath(\dirname($name));
            $result = \file_get_contents($name);
        }

        foreach($this->templateSearchPaths as $path) {
            $path = \realpath($path.'/'.$name);

            if ( $path === false || !\is_file($path) ) {
                continue;
            }

            $this->addSearchPath(\dirname($path));
            $result = \file_get_contents($path);
        }

        if ( $result === false ) {
            $result = null;
        }

        return $result;
    }

}