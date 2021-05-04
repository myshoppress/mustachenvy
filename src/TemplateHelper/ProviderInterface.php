<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

interface ProviderInterface
{

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array;

}