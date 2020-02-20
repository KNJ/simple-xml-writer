# Simple Xml Writer

[![Build Status](https://travis-ci.org/KNJ/simple-xml-writer.svg?branch=master)](https://travis-ci.org/KNJ/simple-xml-writer)
[![codecov](https://codecov.io/gh/KNJ/simple-xml-writer/branch/master/graph/badge.svg)](https://codecov.io/gh/KNJ/simple-xml-writer)

## Example

Script:

```php
<?php

$writer = new \Wazly\SimpleXmlWriter\Writer(
    '<?xml version="1.0" encoding="UTF-8"?>'.
    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'.
    '</urlset>',
    ['filters' => 'htmlspecialchars']
);

$writer
    ->addChild('url')
    ->addChild('loc', 'https://dev.wazly.net')
    ->addChild('lastmod', date('Y-m-d', time()))
    ->addChild('image:image')
    ->addChild('image:loc', 'https://img.wazly.net/home.jpg')
    ->rewind()
    // Shift pointer to root
    ->addChild('url')
    ->addChild('loc', 'https://dev.wazly.net/profile?cache=0&amp;rev=4')
    ->addChild('lastmod', date('Y-m-d', time()))
    ->addChild('image:image')
    ->addChild('image:loc', 'https://img.wazly.net/profile.jpg')
    ->store('/path/to/sitemap.xml');
```

Output (prettified) :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc>https://dev.wazly.net</loc>
        <lastmod>2020-02-20</lastmod>
        <image:image>
            <image:loc>https://img.wazly.net/home.jpg</image:loc>
        </image:image>
    </url>
    <url>
        <loc>https://dev.wazly.net/profile</loc>
        <lastmod>2020-02-20</lastmod>
        <image:image>
            <image:loc>https://img.wazly.net/profile.jpg</image:loc>
        </image:image>
    </url>
</urlset>
```

## API

### Create XML instance

#### From string

```php
$writer = new Writer('<items></items>');
```

or

```php
$writer = new Writer;
$writer->loadFromString('<items></items>');
```

#### From file

```php
$writer = new Writer;
$writer->loadFromFile('/path/to/src.xml');
```

### Store XML as file

```php
$writer->store('/path/to/dest.xml');
```
or

```php
// Only when loaded from file
$writer->store();
```
