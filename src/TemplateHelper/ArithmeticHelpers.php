<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

final class ArithmeticHelpers extends OperatorHelpers
{

    public function getHelper(): \Closure
    {
        return static function (...$args) {
            $opts = \array_pop($args);

            if ( \count($args) < 2 ) {
                throw new \UnexpectedValueException(\sprintf("%s requires at least two operands", $opts['name']));
            }

            $iVal = \array_shift($args);

            switch ($opts['name']) {
                case 'add' :
                    return \array_reduce($args, static fn ($c,$i): int => $c+$i,$iVal);

                case 'sub' :
                    return \array_reduce($args, static fn ($c,$i): int => $c-$i,$iVal);

                case 'mul' :
                    return \array_reduce($args, static fn ($c,$i): int => $c*$i,$iVal);

                case 'div' :
                    return \array_reduce($args, static fn ($c,$i): int => $c/$i,$iVal);

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
        return ['add', 'mul', 'div', 'sub'];
    }

}