<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate;

use Webmozart\Assert\Assert;

class InputParser
{

    public const ORDER_FLAG_ENVVALUE = 'e';
    public const ORDER_FLAG_KVPAIR = 'k';
    public const ORDER_FLAG_FILE = 'f';

    /**
     * @var array <string,scalar>
     */
    private array $kvPairs = [];

    /**
     * @var array <string,scalar>
     */
    private array $envValues = [];

    /**
     * @var array <string,scalar>
     */
    private array $fileValues = [];

    private string $order = self::ORDER_FLAG_ENVVALUE.self::ORDER_FLAG_KVPAIR.self::ORDER_FLAG_FILE;

    /**
     * @param array<string,scalar> $values
     * @return $this
     */
    public function addEnvValues(array $values): self
    {
        $this->envValues = \array_merge($this->envValues, $values);

        return $this;
    }

    /**
     * @param array<string> $keyValuePairs
     */
    public function addKeyValuePairs(array $keyValuePairs): self
    {
        $this->kvPairs = \array_merge($this->kvPairs, self::parseKeyValuePairs($keyValuePairs));

        return $this;
    }

    public function addInputFile(string $inputFile): self
    {
        $this->fileValues = \array_merge($this->fileValues, self::parseFile($inputFile));

        return $this;
    }

    public function setOrder(string $order): void
    {
        $mask = self::ORDER_FLAG_ENVVALUE.self::ORDER_FLAG_FILE.self::ORDER_FLAG_KVPAIR;

        if ( \strspn($order, $mask) !== 3 ) {
            throw new \InvalidArgumentException(\sprintf("%s is an invalid order string", $order));
        }

        $this->order = \substr($order, 0, 3);
    }

    /**
     * @return array<string|scalar>
     */
    public function getValues(): array
    {
        $orderFlags = \str_split($this->order);
        $values = [];

        foreach($orderFlags as $flag) {
            switch ($flag) {
                case self::ORDER_FLAG_KVPAIR:
                    $values = \array_merge($values, $this->kvPairs);
                    break;

                case self::ORDER_FLAG_FILE:
                    $values = \array_merge($values, $this->fileValues);
                    break;

                case self::ORDER_FLAG_ENVVALUE:
                    $values = \array_merge($values, $this->envValues);

                    break;
            }
        }

        return $values;
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