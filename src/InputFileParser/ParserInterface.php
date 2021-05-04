<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\InputFileParser;

interface ParserInterface
{

    /**
     * @return array<mixed>
     */
    public function parse(string $file): array;

    public function support(string $file, string $type): bool;

}