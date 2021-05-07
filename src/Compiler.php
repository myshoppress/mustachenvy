<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy;

use LightnCandy\LightnCandy;
use MyShoppress\DevOp\MustacheEnvy\Partial\PartialResolver as DefaultParitalResolver;
use MyShoppress\DevOp\MustacheEnvy\Partial\ResolverInterface as PartialResolver;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\ProviderInterface as HelperProvider;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\TemplateHelper;

class Compiler
{

    /**
     * @var array<mixed>
     */
    private array $compileOptions;

    private PartialResolver $partialResolver;

    private HelperProvider $helperProvider;

    public function __construct()
    {
        $this->compileOptions['flags'] =
            LightnCandy::FLAG_HANDLEBARS ^ LightnCandy::FLAG_HANDLEBARSLAMBDA
            | LightnCandy::FLAG_RUNTIMEPARTIAL
            | LightnCandy::FLAG_ERROR_EXCEPTION
            | LightnCandy::FLAG_EXTHELPER
            | LightnCandy::FLAG_NOESCAPE
            ;

        $this->partialResolver = new DefaultParitalResolver;
        $this->helperProvider = new TemplateHelper;
    }

    public function addPartialSearchPath(string $name): void
    {
        $this->partialResolver->addSearchPath($name);
    }

    public function compile(string $template): \Closure
    {
        $this->compileOptions['partialresolver'] = fn ($ctx, $name) => $this->partialResolver->resolvePartial($name);
        $this->compileOptions['helpers'] = $this->helperProvider->getHelpers();

        $compiledCode = LightnCandy::compile($template,$this->compileOptions);
        return eval($compiledCode);
    }

}