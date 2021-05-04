<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

class ComparisonHelpers extends AbstractHelper
{

    public function getHelper(): \Closure
    {
        return static function (...$args): bool {
            $opts = \array_pop($args);

            if ( \count($args) !== 2 ) {
                throw new \UnexpectedValueException(\sprintf("%s requires two operands", $opts['name']));
            }

            [$a, $b] = $args;

            switch ($opts['name']) {
                case 'eq' :
                    if ( !\is_numeric($a) || !\is_numeric($b) ) {
                        return \strcmp($a, $b) === 0;
                    }

                    return (int)$a === (int)$b;

                case 'neq' :
                    if ( !\is_numeric($a) || !\is_numeric($b) ) {
                        return \strcmp($a, $b) !== 0;
                    }

                    return (int)$a !== (int)$b;

                case 'lt' :
                    return $a < $b;

                case 'lte' :
                    return $a <= $b;

                case 'gt' :
                    return $a > $b;

                case 'gte' :
                    return $a >= $b;

                default:
                    throw new \UnexpectedValueException(\sprintf("%s is an invalid comparison", $opts['name']));
            }
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