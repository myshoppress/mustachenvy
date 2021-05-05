<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Partial;

interface ResolverInterface
{

    public function addSearchPath(string $path): void;

    public function resolvePartial(string $name): ?string;

}