<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\TemplateHelper;

use Symfony\Component\Process\Process;
use function MyShoppress\DevOp\MustacheEnvy\castCallable;

class CommandHelper implements ProviderInterface
{

    /**
     * @return array<string, callable>
     */
    public function getHelpers(): array
    {
        return [
            'cmd' => castCallable(static::class.'::cmd'),
        ];
    }

    /**
     * @param mixed ...$args
     */
    public function cmd(...$args): string
    {
        \array_pop($args);
        $process = new Process($args);
        $process->run();

        if ( !$process->isSuccessful() ) {
            throw new \RuntimeException(
                \sprintf("Error executing cmdline %s. %s", $process->getCommandLine(), $process->getErrorOutput()),
            );
        }

        $result = $process->getOutput();
        return \trim($result);
    }

}