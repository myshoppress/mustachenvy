<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

class LogicalHelpers extends AbstractHelper
{

    public function getHelper(): \Closure
    {
        return static function (...$args) {
            $opts = \array_pop($args);

            if ( $opts['name'] === 'not' ) {
                if ( \count($args) !== 1 ) {
                    throw new \UnexpectedValueException(\sprintf("%s requires exactly one operand", $opts['name']));
                }

                return !(bool)\array_shift($args);
            }

            if ( \count($args) < 2 ) {
                throw new \UnexpectedValueException(\sprintf("%s requires at least two operands", $opts['name']));
            }

            $iVal = (bool)\array_shift($args);
            /** @var array<bool> $list */
            $list = $args;

            switch ($opts['name']) {
                case 'and' :
                    return \array_reduce($list, static fn ($c,$i) => $c && $i,$iVal);

                case 'or' :
                    return \array_reduce($list, static fn ($c,$i) => $c || $i,$iVal);

                case 'xor' :
                    return \array_reduce($list, static fn ($c,$i) => $c ^ $i,$iVal);

                default:
                    throw new \UnexpectedValueException(\sprintf("%s is an invalid operation", $opts['name']));
            }
        };
    }

    /**
     * @return array<string>
     */
    public function getTokens(): array
    {
        return ['and', 'or', 'xor', 'not'];
    }

}