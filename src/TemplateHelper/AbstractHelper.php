<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

abstract class AbstractHelper implements ProviderInterface, ResolverInterface
{

    /**
     * @return array<string>
     */
    abstract public function getTokens(): array;

    abstract public function getHelper(): \Closure;

    /**
     * @param array<string,mixed> $context
     */
    public function resolve(array $context, string $name): ?callable
    {
        $tokens = $this->getTokens();

        if ( \count($tokens) === 0 ) {
            throw new \UnexpectedValueException(\sprintf("%s helper must have at least one token", self::class));
        }

        if ( \in_array($name, $tokens, true) ) {
            return $this->getHelper();
        }

        return null;
    }

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return \array_fill_keys($this->getTokens(), $this->getHelper());
    }

}