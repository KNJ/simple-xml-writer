<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Wazly\SimpleXmlWriter\Writer;

final class WriteResourceTest extends TestCase
{
    private const SITEMAP_ORIGINAL_FILE_PATH = __DIR__ . '/fixtures/sitemap.xml';
    private const SITEMAP_COPIED_FILE_PATH = __DIR__ . '/output/sitemap_copied.xml';
    private const SITEMAP_RESULT_FILE_PATH = __DIR__ . '/fixtures/sitemap_result.xml';
    private const SITEMAP_OUTPUT_FILE_PATH = __DIR__ . '/output/store.xml';

    public function setUp(): void
    {
        $this->fresh();
    }

    public function tearDown(): void
    {
        $this->fresh();
    }

    public function testInputAndOutputToNewFile()
    {
        $writer = $this->prepare(self::SITEMAP_ORIGINAL_FILE_PATH);
        $writer->store(self::SITEMAP_OUTPUT_FILE_PATH);

        $this->assertSame(
            (new Writer)->loadFromFile(self::SITEMAP_RESULT_FILE_PATH)->output(),
            $writer->output()
        );

        $this->assertSame(
            file_get_contents(self::SITEMAP_RESULT_FILE_PATH),
            file_get_contents(self::SITEMAP_OUTPUT_FILE_PATH)
        );
    }

    public function testInputAndOutputToSameFile()
    {
        copy(self::SITEMAP_ORIGINAL_FILE_PATH, self::SITEMAP_COPIED_FILE_PATH);
        $writer = $this->prepare(self::SITEMAP_COPIED_FILE_PATH);
        $writer->store();

        $this->assertSame(
            file_get_contents(self::SITEMAP_RESULT_FILE_PATH),
            file_get_contents(self::SITEMAP_COPIED_FILE_PATH)
        );
    }

    private function prepare(string $filepath)
    {
        return (new Writer)
            ->loadFromFile($filepath)
            ->addChild('url')
            ->addChild('loc', 'https://example.com/sample')
            ->addChild('lastmod', '2020-02-13')
            ->addChild('priority', '0.1')
            ->rewind()
            ->addChild('url')
            ->addChild('loc', 'https://example.com/about?item=24&amp;amp=1')
            ->addChild('image:image')
            ->addChild('image:loc', 'https://example.com/photo.jpg');
    }

    private function fresh()
    {
        $temporaryFiles = [
            self::SITEMAP_COPIED_FILE_PATH,
            self::SITEMAP_OUTPUT_FILE_PATH,
        ];

        foreach ($temporaryFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
