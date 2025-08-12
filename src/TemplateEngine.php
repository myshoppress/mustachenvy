<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy;

use LightnCandy\Runtime;

final class TemplateEngine
{

    private Compiler $compiler;

    public function __construct(?Compiler $compiler = null)
    {
        $this->compiler = $compiler ?? new Compiler;
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