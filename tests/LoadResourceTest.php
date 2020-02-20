<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Wazly\SimpleXmlWriter\Writer;

final class LoadResourceTest extends TestCase
{
    public function testLoadResourceFromStringViaConstructor()
    {
        $writer = new Writer($this->getSitemapString());
        $this->assertTrue($writer instanceof Writer);
    }

    public function testLoadResourceFromString()
    {
        $writer = new Writer;
        $this->assertTrue(
            $writer->loadFromString($this->getSitemapString()) instanceof Writer
        );
    }

    public function testLoadResourceFromFile()
    {
        $writer = new Writer;
        $this->assertTrue(
            $writer->loadFromFile(__DIR__ . '/fixtures/sitemap.xml') instanceof Writer
        );
    }

    public function testFindMethodSucceedsWhenXmlNotSet()
    {
        $writer = new Writer;
        $this->assertTrue($writer->find('/*') instanceof Writer);
    }

    private function getSitemapString(): string
    {
        static $resource;

        if ($resource === null) {
            $resource = file_get_contents(__DIR__ . '/fixtures/sitemap.xml');
        }

        return $resource;
    }
}
