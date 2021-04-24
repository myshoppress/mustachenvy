<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate;

use Webmozart\Assert\Assert;

class InputParser
{

    /**
     * @param string|array<string> $inputs
     * @return array<string|int,mixed>
     */
    static public function parseEnv($inputs): array
    {
        $inputs = (array)$inputs;
        $lines = [];

        foreach($inputs as $input) {
            if ( \is_file($input) ) {
                $fileLines = \file($input,\FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);
                Assert::isArray($fileLines, "Unable to read and parse $input file");
                $lines = \array_merge($lines, $fileLines);

                continue;
            }

            $lines[] = $input;
        }

        $regx = '#(?P<key>\w+)[\s]*=[\s]*(?P<value>(?:[^"\'\s]+)|\'(?:[^\']*)\'|"(?:[^"]*)")#';
        $vars=[];
        
        foreach($lines as $line) {
            $matches = [];

            if ( \preg_match($regx, $line, $matches) === false ) {
                continue;
            }

            $vars[$matches['key']] = $matches['value'];
        }

        return $vars;
    }

}