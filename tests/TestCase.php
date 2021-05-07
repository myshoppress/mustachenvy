<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

use MyShoppress\DevOp\MustacheEnvy\TemplateEngine;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase as BaseTestCase;

// @codingStandardsIgnoreStart
class TestCase extends BaseTestCase
{

    static protected string $result = '';

    /**
     * @param array<mixed> $vars
     */
    static public function render(string $template, array $vars=[]): string
    {
        self::$result = (new TemplateEngine)->render($template, $vars);
        return self::$result;
    }

    /**
     * @param array<mixed> $structure
     * @return string
     */
    static public function vf(array $structure) : string
    {
        return vfsStream::setup('root',null,$structure)->url();
    }

}
// @codingStandardsIgnoreEnd