<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

class ComparisonHelpers extends OperatorHelpers
{

    public function getHelper(): \Closure
    {
        return static function (...$args) {
            $opts = \array_pop($args);

            if ( \count($args) !== 2 ) {
                throw new \UnexpectedValueException(\sprintf("%s requires two operands", $opts['name']));
            }

            [$a, $b] = $args;

            switch ($opts['name']) {
                case 'eq' :
                    $result = $a === $b;
                    break;

                case 'neq' :
                    $result = $a !== $b;
                    break;

                case 'lt' :
                    $result = $a < $b;
                    break;

                case 'lte' :
                    $result = $a <= $b;
                    break;

                case 'gt' :
                    $result = $a > $b;
                    break;

                case 'gte' :
                    $result = $a >= $b;
                    break;

                default:
                    throw new \UnexpectedValueException(\sprintf("%s is an invalid comparison", $opts['name']));
            }

            $result = $result && isset($opts['fn'])
                ? $opts['fn']()
                : $result;
            return $result;
        };
    }

    /**
     * @return array<string>
     */
    public function getTokens(): array
    {
        return ['eq', 'neq', 'lt', 'lte', 'gt', 'gte'];
    }

}