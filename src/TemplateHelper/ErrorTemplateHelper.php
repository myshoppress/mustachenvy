<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

final class ErrorTemplateHelper implements ProviderInterface
{

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            'error' => static function(...$args): void {
                \array_pop($args);
                [$msg, $cond] = [
                    $args[0] ?? throw new \InvalidArgumentException("no error message provided"),
                    $args[1] ?? true,
                ];

                if ( $cond ) {
                    throw new \RuntimeException($msg);
                }
            },
        ];
    }

}
