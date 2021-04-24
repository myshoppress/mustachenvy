<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate;

class OutputHandler
{

    static public function output(string $outputFile, string $data): void
    {
        $resource = \fopen($outputFile,'w');

        if ( $resource === false) {
            throw new \UnexpectedValueException(\sprintf("Unable to open the file %s for writing.", $outputFile));
        }

        \fwrite($resource, $data);
        \fclose($resource);
    }

}