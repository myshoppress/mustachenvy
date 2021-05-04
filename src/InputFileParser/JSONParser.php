<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\InputFileParser;

class JSONParser implements ParserInterface
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

        return \json_decode($content,true, \JSON_THROW_ON_ERROR);
    }

    public function support(string $file, string $type): bool
    {
        return $type === 'json';
    }

}