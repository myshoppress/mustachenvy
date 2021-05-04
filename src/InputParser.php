<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy;

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