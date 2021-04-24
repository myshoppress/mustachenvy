<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate;

use LightnCandy\LightnCandy;

class Renderer
{

    /**
     * @var array<string, callable>
     */
    private array $helpers = [];
    private int $compileFlags;

    public function __construct()
    {
        $this->compileFlags =
            LightnCandy::FLAG_ERROR_EXCEPTION |
            LightnCandy::FLAG_RUNTIMEPARTIAL |
            LightnCandy::FLAG_NOESCAPE
        ;
        $this->registerHelper('default', static function(...$args){
            \array_pop($args);

            //find first truthy value
            foreach($args as $value) {
                if ( (bool)$value ) {
                    return $value;
                }
            }

            return '';
        });
        $this->registerNativeMethods();
    }

    public function registerHelper(string $name, callable $helper): void
    {
        $this->helpers[$name] = $helper;
    }

    /**
     * @param array<string,mixed> $vars
     */
    public function render(string $template, array $vars): string
    {
        $compiledCode = LightnCandy::compile($template,[
            'helpers' => $this->helpers,
            'flags' => $this->compileFlags,
        ]);
        $renderer = eval($compiledCode);

        return $renderer($vars);
    }

    private function registerNativeMethods(): void
    {
        $methods = [
            'strtolower', 'strtoupper',
        ];

        foreach($methods as $method) {
            $this->registerHelper($method, static function(...$args) {
                $opts = \array_pop($args);

                return \call_user_func_array($opts['name'], $args);
            });
        }
    }

}