<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy;

use LightnCandy\LightnCandy;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\ArithmeticHelpers;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\CommandHelper;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\ComparisonHelpers;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\EmbededDataHelpers;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\ErrorTemplateHelper;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\LogicalHelpers;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\ProviderInterface as HelperProvider;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\StringHelpers;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\TernaryOperatorHelper;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\VariableHelpers;

class Compiler
{

    /**
     * @var array<mixed>
     */
    private array $compileOptions;

    /**
     * @var array<string, callable>
     */
    private array $helpers = [];

    public function __construct()
    {
        $this->compileOptions['flags'] =
            LightnCandy::FLAG_HANDLEBARS ^ LightnCandy::FLAG_HANDLEBARSLAMBDA
            | LightnCandy::FLAG_RUNTIMEPARTIAL
            | LightnCandy::FLAG_ERROR_EXCEPTION
            | LightnCandy::FLAG_EXTHELPER
            | LightnCandy::FLAG_NOESCAPE
            ;

        $this
            ->addHelpers(new StringHelpers)
            ->addHelpers(new ArithmeticHelpers)
            ->addHelpers(new LogicalHelpers)
            ->addHelpers(new ComparisonHelpers)
            ->addHelpers(new VariableHelpers($this))
            ->addHelpers(new EmbededDataHelpers)
            ->addHelpers(new TernaryOperatorHelper)
            ->addHelpers(new ErrorTemplateHelper)
            ->addHelpers(new CommandHelper)
        ;
    }

    public function addHelpers(HelperProvider $provider): self
    {
        $this->helpers = \array_merge($this->helpers, $provider->getHelpers());
        return $this;
    }

    public function compile(string $template): \Closure
    {
        $this->compileOptions['helpers'] = $this->helpers;
        $compiledCode = LightnCandy::compile($template,$this->compileOptions);
        return eval($compiledCode);
    }

}