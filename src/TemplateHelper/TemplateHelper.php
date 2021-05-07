<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

class TemplateHelper implements ProviderInterface
{

    /**
     * @var array<ProviderInterface>
     */
    private array $helperProviders;

    public function __construct()
    {
        $this
            ->addHelperProvider(new StringHelpers)
            ->addHelperProvider(new ArithmeticHelpers)
            ->addHelperProvider(new LogicalHelpers)
            ->addHelperProvider(new ComparisonHelpers)
            ->addHelperProvider(new VariableHelpers)
            ->addHelperProvider(new EmbededDataHelpers)
            ->addHelperProvider(new TernaryOperatorHelper)
            ->addHelperProvider(new FileHelpers)
        ;
    }

    public function addHelperProvider(ProviderInterface $provider): self
    {
        $this->helperProviders[] = $provider;
        return $this;
    }

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        $helpers = [];

        foreach($this->helperProviders as $provider) {
            $helpers = \array_merge($helpers, $provider->getHelpers());
        }

        return $helpers;
    }

}