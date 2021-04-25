<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate;

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;

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
            LightnCandy::FLAG_NOESCAPE |
            LightnCandy::FLAG_ADVARNAME |
            LightnCandy::FLAG_JSOBJECT |
            LightnCandy::FLAG_SPVARS |
            LightnCandy::FLAG_ELSE |
            LightnCandy::FLAG_NAMEDARG
        ;
        $this->registerHelpers(TemplateHelpers::getHelpers());
    }

    public function registerHelper(string $name, callable $helper): void
    {
        $this->helpers[$name] = $helper;
    }

    /**
     * @param array<string,callable> $helpers
     */
    public function registerHelpers(array $helpers): void
    {
        foreach ($helpers as $name => $helper) {
            $this->registerHelper($name, $helper);
        }
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

        return $renderer($vars,[
            'debug' => Runtime::DEBUG_ERROR_EXCEPTION,
        ]);
    }

}