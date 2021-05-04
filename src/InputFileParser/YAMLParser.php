<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\InputFileParser;

use Symfony\Component\Yaml\Yaml;

class YAMLParser implements ParserInterface
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

        return Yaml::parse($content);
    }

    public function support(string $file, string $type): bool
    {
        return $type === 'yaml' || $type === 'yml';
    }

}