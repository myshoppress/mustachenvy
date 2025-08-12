<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use Webmozart\Assert\Assert;
use function MyShoppress\DevOp\MustacheEnvy\castCallable;

trait PHPFunctionsWrapperTrait
{

    static public function callPHPFunction(mixed ...$args): mixed
    {
        $opts = \array_pop($args);
        $name = $opts['name'];
        $negate = false;

        if (\substr($name,0,1)==='-') {
            $name = \substr($name, 1);
            $negate = true;
        }

        $result = \call_user_func_array($name, $args);

        if ( $negate ) {
            $result = !(bool)$result;
        }

        $result = (bool)$result && isset($opts['fn'])
            ? $opts['fn']()
            : $result;
        return $result;
    }

    /**
     * @return array<string, callable>
     */
    static protected function wrapPHPFunctions(string ...$functions): array
    {
        $uniqFns = \array_unique(
            \array_map(static fn ($f) => \substr($f,0,1) === '-' ? \substr($f,1) : $f, $functions),
        );
        Assert::allIsCallable($uniqFns);
        return \array_fill_keys($functions, castCallable(static::class.'::callPHPFunction'));
    }

}