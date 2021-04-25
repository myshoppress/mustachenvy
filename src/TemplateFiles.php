<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\ConfTemplate;

class TemplateFiles
{

    /**
     * @param array<string> $templateFiles
     */
    static public function createTemplate(array $templateFiles): string
    {
        if ( \count($templateFiles) === 0 ) {
            $templateFiles = [\STDIN];
        }

        $templates = [];

        foreach($templateFiles as $file) {
            if ( $file === \STDIN ) {
                if ( \posix_isatty(\STDIN) !== false ) {
                    throw new \UnexpectedValueException("Unable to read template from STDIN");
                }

                $templates[]=\stream_get_contents(\STDIN);
            } else {
                if ( !\is_file($file) ) {
                    throw new \UnexpectedValueException("Template file can not be empty");
                }

                $templates[] = \file_get_contents($file);
            }
        }

        return \implode("\n", $templates);
    }

}