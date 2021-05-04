<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy;

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\TemplateHelper;

class TemplateEngine
{

    /**
     * @var array<mixed>
     */
    private array $compileOptions;

    public function __construct()
    {
        $this->compileOptions['flags'] =
            LightnCandy::FLAG_HANDLEBARS ^ LightnCandy::FLAG_HANDLEBARSLAMBDA
            | LightnCandy::FLAG_RUNTIMEPARTIAL
            | LightnCandy::FLAG_ERROR_EXCEPTION
            | LightnCandy::FLAG_EXTHELPER
            | LightnCandy::FLAG_NOESCAPE

        ;
        $templateHelper = new TemplateHelper;
        $this->compileOptions['helpers'] = $templateHelper->getHelpers();
        $this->compileOptions['helperresolver'] = [$templateHelper, 'resolve'];
    }

    /**
     * @param array<string,mixed> $vars
     */
    public function render(string $template, array $vars = []): string
    {
        $compiledCode = LightnCandy::compile($template,$this->compileOptions);
        $render = eval($compiledCode);
        return $render($vars,[
            'debug' => Runtime::DEBUG_ERROR_EXCEPTION,
        ]);
    }

}