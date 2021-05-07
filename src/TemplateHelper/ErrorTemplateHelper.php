<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

class ErrorTemplateHelper implements ProviderInterface
{

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            'error' => static function(...$args): void {
                \array_pop($args);
                [$msg, $cond] = $args;

                if ( $cond ?? true ) {
                    throw new \RuntimeException($msg);
                }
            },
        ];
    }

}