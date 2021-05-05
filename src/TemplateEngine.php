<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy;

use LightnCandy\Runtime;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\FileHelpers;

class TemplateEngine
{

    private Compiler $compiler;

    public function __construct()
    {
        $this->compiler = new Compiler;
        FileHelpers::setCompiler($this->compiler);
    }

    /**
     * @param array<string,mixed> $vars
     */
    public function render(string $template, array $vars = []): string
    {
        $render = $this->compiler->compile($template);
        return $render($vars,[
            'debug' => Runtime::DEBUG_ERROR_EXCEPTION,
        ]);
    }

    public function getCompiler(): Compiler
    {
        return $this->compiler;
    }

}