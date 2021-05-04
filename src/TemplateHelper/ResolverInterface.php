<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

interface ResolverInterface
{

    /**
     * @param array<string, mixed> $context
     */
    public function resolve(array $context, string $name): ?callable;

}