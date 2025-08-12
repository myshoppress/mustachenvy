<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

use MyShoppress\DevOp\MustacheEnvy\TemplateEngine;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\FileHelpers;

final class FileHelperTest extends TestCase
{

    private TemplateEngine $template;
    private string $root;

    public function setUp(): void
    {
        $this->root =self::vf([
            'file1' => '{{ import "./sub_dir/file2" }}' ,
            'file3' => '{{ import "file4" }}',
            'file4' => '{{ import "file3"}} ',
            'sub_dir' => [
                'file2' => 'file2 content',
            ],
        ]);
    }

    public function testPathJoin(): void
    {
        self::assertEquals(
            "/path1/path2/path3",
            $this->getTemplate()->render('{{path_join "/path1" "/path2/" "/path3"}}'),
        );
    }

    public function testImportingRelativePath(): void
    {
        $result = $this->getTemplate()->render('{{ import "file1" }}');
        self::assertEquals('file2 content', $result);
    }

    public function testImportingAbsPath(): void
    {
        $result = $this->getTemplate()->render("{{ import '{$this->root}/file1' }}");
        self::assertEquals('file2 content', $result);
    }

    public function testImportingInvalidPathWithBlock(): void
    {
        $string=<<<'EOF'
{{#import "some_invalid_path"}}
fall back text
{{/import}}
EOF;
        $result = $this->getTemplate()->render($string);
        self::assertStringContainsString("fall back text", $result);
    }

    public function testImportingInvalidPath(): void
    {
        self::expectExceptionMessage("is not a file");
        $this->getTemplate()->render('{{ import "invalid_path" }}');
    }

    public function testPathNotExists(): void
    {
        self::assertEmpty($this->getTemplate()->render('{{ path_exists "invalid_path" }}'));
    }

    public function testRelativeFileExists(): void
    {
        self::assertNotEmpty($this->getTemplate()->render('{{ path_exists "sub_dir" "file2" }}'));
    }

    /**
     * @runInSeparateProcess
     * @small
     */
    public function testCircularImport(): void
    {
        self::expectExceptionMessage("Circular importing");
        $this->getTemplate()->render("{{ import 'file3' }}");
    }

    public function testRelativeDirectoryExists(): void
    {
        self::assertNotEmpty($this->getTemplate()->render('{{ path_exists "sub_dir" }}'));
    }

    public function testPathExistsWithBlock(): void
    {
        $result = $this->getTemplate()->render(<<<'EOF'
{{#path_exists "sub_dir"}}
sub_dir exists
{{/path_exists}}
EOF,
); self::assertStringContainsString("sub_dir exists", $result);
    }

    public function getTemplate(): TemplateEngine
    {
        if ( !isset($this->template) ) {
            $this->template = new TemplateEngine;
            $this->template->getCompiler()->addHelpers(new FileHelpers($this->template->getCompiler(), $this->root));
        }

        return $this->template;
    }

}
