<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate;

use Webmozart\Assert\Assert;

class InputParser
{

    /**
     * @var array<string,scalar>
     */
    private array $values = [];

    /**
     * @param array<string,scalar> $values
     * @return $this
     */
    public function addValues(array $values): self 
    {
        $this->values = \array_merge($this->values, $values);

        return $this;
    }

    /**
     * @param array<string> $keyValuePairs
     */
    public function addKeyValuePairs(array $keyValuePairs): self
    {
        $this->values = \array_merge($this->values, self::parseKeyValuePairs($keyValuePairs));

        return $this;
    }

    public function addInputFile(string $inputFile): self
    {
        $this->values = \array_merge($this->values, self::parseFile($inputFile));

        return $this;
    }

    /**
     * @return array<string|scalar>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Returns the values from in format key1=value1 pair. The returned is collapsed to 1d array
     * before being returned.
     *
     * @param string $inputFile input file. If the file ends with ?<section> then it will o
     * @return array<string|scalar>
     */
    static public function parseFile(string $inputFile): array
    {
        $parts = \parse_url($inputFile);

        if ( !\is_array($parts) ) {
            throw new \UnexpectedValueException(\sprintf("Unable to read the file %s", $inputFile));
        }

        $returnSection = $parts['query'] ?? null;

        $input = \file_get_contents($parts['path'] ?? '');

        if ( $input === false ) {
            throw new \UnexpectedValueException(\sprintf("Unable to read the file %s", $inputFile));
        }

        //if any line starts with ; (comment) or [section] it is an ini file
        $isINI = \preg_match('/^ *[;\[]/m', $input) !== false;

        $values = [];

        if ( $isINI ) {
            \set_error_handler(static function($code, $msg): void{
               throw new \UnexpectedValueException(\sprintf("Unable to parse INI format. %s", $msg));
            });
            $values = \parse_ini_string($input, true, \INI_SCANNER_TYPED);

            if ( $values === false ) {
                throw new \UnexpectedValueException("Unable to parse INI format");
            }

            \restore_error_handler();
        } else {
            $lines = \explode("\n", $input);

            $section = null;

            foreach($lines as $line) {
                $line = \trim($line);

                if ( \substr($line, 0, 1) === '#' ) {
                    //it's a comment or a header
                    $match = [];

                    if ( \preg_match('/\[(\w+)\]/',$line, $match) !== false ) {
                        $section = $match[1];
                        $values[$section] ??= [];
                    }

                    continue;
                }

                $array = self::parseKeyValuePairs([$line]);

                if ( $section ) {
                    Assert::isArray($values[$section]);
                    $values[$section] = \array_merge($values[$section] ?? [], $array);
                } else {
                    $values = \array_merge($array, $values);
                }
            }
        }

        $array = [];

        foreach($values as $key => $value) {
            //only return keys matching  the section
            if ($returnSection !== null && \strpos($key, $returnSection) === false) {
                continue;
            }

            if ( \is_array($value) ) {
                $section = $value;

                foreach($section as $key2 => $value2) {
                    $array[$key2] = $value2;
                }
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * @param array<string> $inputs
     * @return array<string|scalar>
     */
    static public function parseKeyValuePairs(array $inputs): array
    {
        $lines = [];

        foreach($inputs as $input) {

            $lines[] = $input;
        }

        $regx = '#(?P<key>\w+)[\s]*=[\s]*(?P<value>(?:[^"\'\s]+)|\'(?:[^\']*)\'|"(?:[^"]*)")#';
        $vars=[];
        
        foreach($lines as $line) {
            $matches = [];

            if ( \preg_match($regx, $line, $matches) !== 1 ) {
                throw new \UnexpectedValueException(\sprintf("%s is not a valid key value pair format.", $line));
            }

            $vars[$matches['key']] = \trim($matches['value'],"\"\'");
        }

        return $vars;
    }

}