<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

use MyShoppress\DevOp\MustacheEnvy\TemplateEngine;
use MyShoppress\DevOp\MustacheEnvy\TemplateHelper\FileHelpers;

class FileHelperTest extends TestCase
{

    private TemplateEngine $template;
    private string $root;

    public function setUp(): void
    {
        $this->root =self::vf([
            'file1' => '{{ import "./sub_dir/file2" }}' ,
            'sub_dir' => [
                'file2' => 'file2 content',
            ],
        ]);
        $this->template = new TemplateEngine;
        $this->template->getCompiler()->addHelpers(new FileHelpers($this->template->getCompiler(), $this->root));
    }

    public function testPathJoin(): void
    {
        self::assertEquals("/path1/path2/path3", $this->template->render('{{path_join "/path1" "/path2/" "/path3"}}'));
    }

    public function testImportingRelativePath(): void
    {
        $result = $this->template->render('{{ import "file1" }}');
        self::assertEquals('file2 content', $result);
    }

    public function testImportingAbsPath(): void
    {
        $result = $this->template->render("{{ import '{$this->root}/file1' }}");
        self::assertEquals('file2 content', $result);
    }

    public function testImportingInvalidPathWithBlock(): void
    {
        $string=<<<'EOF'
{{#import "some_invalid_path"}}
fall back text
{{/import}}
EOF;
        $result = $this->template->render($string);
        self::assertStringContainsString("fall back text", $result);
    }

    public function testImportingInvalidPath(): void
    {
        self::expectExceptionMessage("is not a file");
        $this->template->render('{{ import "invalid_path" }}');
    }

    public function testPathNotExists(): void
    {
        self::assertEmpty($this->template->render('{{ path_exists "invalid_path" }}'));
    }

    public function testRelativeFileExists(): void
    {
        self::assertNotEmpty($this->template->render('{{ path_exists "sub_dir" "file2" }}'));
    }

    public function testRelativeDirectoryExists(): void
    {
        self::assertNotEmpty($this->template->render('{{ path_exists "sub_dir" }}'));
    }

    public function testPathExistsWithBlock(): void
    {
        $result = $this->template->render(<<<'EOF'
{{#path_exists "sub_dir"}}
sub_dir exists
{{/path_exists}}
EOF,
); self::assertStringContainsString("sub_dir exists", $result);
    }

}
