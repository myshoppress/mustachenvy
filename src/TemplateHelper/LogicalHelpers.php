<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

final class LogicalHelpers extends OperatorHelpers
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

            $args = \array_map(static fn ($i): bool => (bool)$i, $args);

            switch ($opts['name']) {
                case 'and' :
                    $result = \array_reduce($args,static fn (bool $c,bool $i) => $i && $c, $args[0] );
                    break;

                case 'or' :
                    $result = \array_reduce($args,static fn (bool $c,bool $i) => $i && $c, $args[0] );
                    break;

                case 'xor' :
                    $result = \array_reduce($args,static fn (bool $c,bool $i) => $i && $c, !$args[0]);
                    break;

                default:
                    throw new \UnexpectedValueException(\sprintf("%s is an invalid operation", $opts['name']));
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
        return ['and', 'or', 'xor', 'not'];
    }

}