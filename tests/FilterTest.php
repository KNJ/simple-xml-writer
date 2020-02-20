<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Wazly\SimpleXmlWriter\Writer;

final class FilterTest extends TestCase
{
    public function testPresetFilter()
    {
        $writer = new Writer('<?xml version="1.0" encoding="UTF-8"?><items></items>', ['filters' => 'htmlspecialchars']);
        $writer->addChild('item')->addChild('url', 'https://example.com?category=news&id=1');

        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<items><item><url>https://example.com?category=news&amp;id=1</url></item></items>' . PHP_EOL,
            $writer->output()
        );
    }

    public function testAnonymousFilter()
    {
        $writer = new Writer(
            '<?xml version="1.0" encoding="UTF-8"?><items></items>',
            [
                'filters' => function (string $value) {
                    return strtoupper($value);
                }
            ]
        );
        $writer->addChild('item')->addChild('name', 'Wazly');

        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<items><item><name>WAZLY</name></item></items>' . PHP_EOL,
            $writer->output()
        );
    }

    public function testMixFilter()
    {
        $writer = new Writer(
            '<?xml version="1.0" encoding="UTF-8"?><items></items>',
            [
                'filters' => [
                    'htmlspecialchars',
                    function (string $value) {
                        return strtoupper($value);
                    }
                ]
            ]
        );
        $writer->addChild('item')->addChild('url', 'https://example.com?category=news&id=1');

        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<items><item><url>HTTPS://EXAMPLE.COM?CATEGORY=NEWS&AMP;ID=1</url></item></items>' . PHP_EOL,
            $writer->output()
        );
    }
}
