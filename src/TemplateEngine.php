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

    /**
     * @var array<string>
     */
    private array $templateSearchPaths = [];

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
        $this->compileOptions['partialresolver'] = fn ($ctx, $name) => $this->resolvePartial($name, $ctx);
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

    public function addTemplateSearchPath(string $path): void
    {
        \array_unshift($this->templateSearchPaths, $path);
        $this->templateSearchPaths = \array_unique($this->templateSearchPaths);
    }

    /**
     * @param array<mixed> $context
     */
    protected function resolvePartial(string $name, array $context): ?string
    {
        $result = null;

        if ( \is_file($name) ) {
            $this->addTemplateSearchPath(\dirname($name));
            $result = \file_get_contents($name);
        }

        foreach($this->templateSearchPaths as $path) {
            $path = \realpath($path.'/'.$name);

            if ( $path === false || !\is_file($path) ) {
                continue;
            }

            $this->addTemplateSearchPath(\dirname($path));
            $result = \file_get_contents($path);
        }

        if ( $result === false ) {
            $result = null;
        }

        return $result;
    }

}