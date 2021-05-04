<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

class TemplateHelper implements ProviderInterface, ResolverInterface
{

    /**
     * @var array<ResolverInterface>
     */
    private array $resolvers = [];

    /**
     * @var array<ProviderInterface>
     */
    private array $helperProviders;

    public function __construct()
    {
        $this
            ->addHelperProvider(new PHPFunctionHelpers)
            ->addHelperProvider(new ArithmeticHelpers)
            ->addHelperProvider(new LogicalHelpers)
            ->addHelperProvider(new ComparisonHelpers)
            ->addHelperProvider(new VariableHelpers)
            ->addHelperProvider(new FormatBlockHelpers)
            ->addHelperProvider(new TernaryOperatorHelper)
        ;
    }

    public function addResolver(ResolverInterface $resolver): self
    {
        $this->resolvers[] = $resolver;
        return $this;
    }

    public function addHelperProvider(ProviderInterface $provider): self
    {
        $this->helperProviders[] = $provider;
        return $this;
    }

    /**
     * @param array<mixed> $context
     */
    public function resolve(array $context, string $name): ?callable
    {
        foreach($this->resolvers as $resolver) {
            $result = $resolver->resolve($context, $name);

            if ( $result !== null ) {
                return $result;
            }
        }

        return null;
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