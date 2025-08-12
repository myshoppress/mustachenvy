<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\InputFileParser;

final class Parser implements ParserInterface
{

    /**
     * @var array<ParserInterface>
     */
    private array $parsers = [];

    public function __construct()
    {
        $this
            ->addParser(new YAMLParser)
            ->addParser(new JSONParser)
            ->addParser(new ENVParser);
    }

    public function addParser(ParserInterface $inputParser): self
    {
        $this->parsers[] = $inputParser;
        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function parse(string $file): array
    {
        $type = \pathinfo($file, \PATHINFO_EXTENSION);

        foreach ($this->parsers as $parser) {
            if ($parser->support($file, $type)) {
                return $parser->parse($file);
            }
        }

        throw new \InvalidArgumentException(\sprintf("Unable to locate a parser for file %s", $file));
    }

    public function support(string $file, string $type): bool
    {
        foreach($this->parsers as $parser) {
            if ( $parser->support($file, $type) ) {
                return true;
            }
        }

        return false;
    }

}