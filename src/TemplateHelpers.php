<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate;

class TemplateHelpers
{

// phpcs:disable
    /**
     * @return array<string, callable>
     */
    static public function getHelpers() : array
    {
        return [
            'default'     => self::defaultFn(),
            'required'    => self::required(),
            '?:'          => self::ternaryFn(),
            'eq'          => self::cmpFn(),
            'neq'          => self::cmpFn(),
            'lt'          => self::cmpFn(),
            'lte'         => self::cmpFn(),
            'gt'          => self::cmpFn(),
            'gte'          => self::cmpFn(),
            'add'          => self::arithmeticFn(),
            'sub'          => self::arithmeticFn(),
            'multi'         => self::arithmeticFn(),
            'divi'          => self::arithmeticFn(),
            'json'          => self::jsonFn(),
            'strtolower'  => self::nativeFn(),
            'strtoupper'  => self::nativeFn()
        ];
    }

    static private function required() : \Closure
    {
        return static function(...$args) : void {
            $opts = \array_pop($args);
            $missingVars = [];
            foreach($args as $arg) {
               if ( !isset($opts['_this'][$arg]) ) {
                   $missingVars[] = $arg;
               }
            }
            if ( count($missingVars) > 0 ) {
                throw new \ErrorException(sprintf("%s variable(s) are missing.", implode(',', $missingVars)));
            }
        };
    }

    static private function jsonFn() : \Closure
    {
        return static function(...$args) {
            $opts = \array_pop($args);
            //block - store in a variable passed
            if ( isset($opts['fn']) ) {
                if ( !isset($args[0]) ) {
                    throw new \RuntimeException(sprintf("%s requires a variable to store the result", $opts['name']));
                }
                $varName = $args[0];
                $json = json_decode($opts['fn'](), true, 512, JSON_THROW_ON_ERROR);
                $opts['_this'][$varName] = $json;
            }
            //inline - return the json object
            else {
                $json = array_shift($args);
                return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            }
        };
    }

    static private function ternaryFn():  \Closure
    {
        return static function(...$args) {
            \array_pop($args);
            if ( count($args) !== 3) {
                throw new \UnexpectedValueException("ternary operation requires 1 condition argument and 2 result arguments");
            }
            [$cond, $ifTrue, $ifFalse] = $args;
            return (bool)$cond ? $ifTrue : $ifFalse;
        };
    }
    static private function defaultFn():  \Closure
    {
        return static function(...$args) {
            \array_pop($args);

            //find first truthy value
            foreach($args as $value) {
                if ( (bool)$value ) {
                    return $value;
                }
            }

            return '';
        };
    }

    static private function arithmeticFn() : \Closure
    {
        return static function (...$args) {
            $opts = \array_pop($args);
            if ( count($args) < 2 ) {
                throw new \UnexpectedValueException(sprintf("%s requires at least two operands", $opts['name']));
            }
            $iVal = array_shift($args);
            switch ($opts['name']) {
                case 'add' :
                    return array_reduce($args, fn($c,$i):int=>$c+$i,$iVal);
                case 'sub' :
                    return array_reduce($args, fn($c,$i):int=>$c-$i,$iVal);
                case 'mult' :
                    return array_reduce($args, fn($c,$i):int=>$c*$i,$iVal);
                case 'div' :
                    return array_reduce($args, fn($c,$i):int=>$c/$i,$iVal);
                default:
                    throw new \UnexpectedValueException(sprintf("%s is an invalid operation", $opts['name']));
            }
        };
    }

    static private function cmpFn() : \Closure
    {
        return static function (...$args) : bool {
            $opts = \array_pop($args);
            if ( count($args) !== 2 ) {
                throw new \UnexpectedValueException(sprintf("%s requires two operands", $opts['name']));
            }
            [$a, $b] = $args;
            switch ($opts['name']) {
                case 'eq' :
                    return $a == $b;
                case 'neq' :
                    return $a != $b;
                case 'lt' :
                    return $a < $b;
                case 'lte' :
                    return $a <= $b;
                case 'gt' :
                    return $a > $b;
                case 'gte' :
                    return $a >= $b;
                default:
                    throw new \UnexpectedValueException(sprintf("%s is an invalid comparison", $opts['name']));
            }
        };
    }

    static private function nativeFn() : \Closure
    {
        return static function(...$args) {
            $opts = \array_pop($args);
            return \call_user_func_array($opts['name'], $args);
        };
    }
// phpcs:enable

}
