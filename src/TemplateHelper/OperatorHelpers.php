<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

abstract class OperatorHelpers implements ProviderInterface
{

    /**
     * @return array<string>
     */
    abstract public function getTokens(): array;

    abstract public function getHelper(): \Closure;

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return \array_fill_keys($this->getTokens(), $this->getHelper());
    }

}