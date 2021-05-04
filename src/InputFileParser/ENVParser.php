<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\InputFileParser;

use M1\Env\Parser;

class ENVParser implements ParserInterface
{

    /**
     * @return array<mixed>
     */
    public function parse(string $file): array
    {
        $content = \file_get_contents($file);

        if ( $content === false) {
            throw new \UnexpectedValueException(\sprintf("Unable to read file %s", $file));
        }

        return Parser::parse($content);
    }

    public function support(string $file, string $type): bool
    {
        return $type === 'env';
    }

}