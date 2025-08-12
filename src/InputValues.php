<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy;

use ArrayObject;
use MyShoppress\DevOp\MustacheEnvy\InputFileParser\Parser;

/**
 * Class InputValues
 *
 * @extends ArrayObject<string, mixed>
 */
final class InputValues extends ArrayObject
{

    private Parser $fileParser;

    public function __construct()
    {
        parent::__construct();

        $this->fileParser = new Parser;
    }

    /**
     * @param array<mixed> $newValues
     */
    public function setValues(array $newValues): void
    {
        $values = $this->getArrayCopy();
        $values = \array_merge($values, $newValues);
        $this->exchangeArray($values);
    }

    /**
     * @param array<string>|string $files
     */
    public function addInputFile(array|string $files): void
    {
        $values = $this->getArrayCopy();

        foreach((array)$files as $file) {
            $values = \array_merge($values, $this->fileParser->parse($file));
        }

        $this->exchangeArray($values);
    }

}