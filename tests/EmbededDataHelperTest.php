<?php

declare(strict_types = 1);

namespace MyShoppress\DevOp\MustacheEnvy\Tests;

class EmbededDataHelperTest extends TestCase
{

    public function testTemplateHelperJson(): void
    {
        $template = <<<'EOF'
{{#json 'List' }}
[
 {"id":1,"name":"John"},
 {"id":2,"name":"Jody"}
]
{{/json}}
{{#each List }}
Name is {{name}}
{{/each}}
{{#each (json "[1,2]") }}
Index {{@index}}
{{/each}}
EOF;
        self::render($template);
        $assert = <<<'EOF'
Name is John
Name is Jody
Index 0
Index 1
EOF;
        self::assertStringContainsString($assert, self::$result);
    }

    public function testTemplateHelperYaml(): void
    {
        $template = <<<'EOF'
{{#yaml 'List' }}
- id: 1
  name: "John"
- id: 2
  name: "Jody"  
{{/yaml}}
{{#each List }}
Name is {{name}}
{{/each}}
{{#each (yaml "[1,2]") }}
Index {{@index}}
{{/each}}
EOF;
        self::render($template);
        $assert = <<<'EOF'
Name is John
Name is Jody
Index 0
Index 1
EOF;
        self::assertStringContainsString($assert, self::$result);
    }

}
