<?php

namespace Wazly\SimpleXmlWriter;

use Closure;
use Exception;
use SimpleXMLElement;

class Writer
{
    /** @var \SimpleXMLElement|null */
    protected $xml;

    /** @var array */
    protected $namespaces = [];

    /** @var \SimpleXMLElement|null */
    protected $pointer;

    /** @var string|null */
    protected $originalFilepath;

    /**
     * Constructor.
     *
     * @param  string|null  $base
     * @param  array  $options
     * @return void
     */
    public function __construct(string $base = null, array $options = [])
    {
        $this->options = $options;

        if ($base !== null) {
            $this->xml = new SimpleXMLElement($base);
            $this->initialize();
        }
    }

    /**
     * Set SimpleXMLElement instance from string.
     *
     * @param  string  $base
     * @return \Wazly\SimpleXmlWriter\Writer
     */
    public function loadFromString(string $base): Writer
    {
        $this->xml = new SimpleXMLElement($base);

        return $this->initialize();
    }

    /**
     * Set SimpleXMLElement instance from file.
     *
     * @param  string  $filepath
     * @return \Wazly\SimpleXmlWriter\Writer
     */
    public function loadFromFile(string $filepath): Writer
    {
        $this->xml = new SimpleXMLElement($filepath, 0, true);

        return $this->initialize($filepath);
    }

    /**
     * Set initial state.
     *
     * @param  string|null  $filepath
     * @return \Wazly\SimpleXmlWriter\Writer
     */
    protected function initialize(string $filepath = null): Writer
    {
        $this->pointer = $this->xml;
        $this->namespaces = $this->xml->getDocNamespaces(true);
        $this->originalFilepath = $filepath;

        return $this;
    }

    /**
     * Shift the pointer to an element by XPath.
     *
     * @param  string  $xpath
     * @param  int  $index
     */
    public function find(string $xpath, int $index = 0): Writer
    {
        if ($this->pointer === null) {
            return $this;
        }

        $this->pointer = $this->pointer->xpath($xpath)[$index];

        return $this;
    }

    /**
     * Shift the pointer to the document root.
     *
     * @return \Wazly\SimpleXmlWriter\Writer
     */
    public function rewind(): Writer
    {
        return $this->find('/*');
    }

    /**
     * Add an element to the pointing position.
     * If $value is null, shift the pointer forward to the added element.
     * If $value is not null, the pointer stays where it is.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @return \Wazly\SimpleXmlWriter\Writer
     */
    public function addChild(string $name, string $value = null): Writer
    {
        $namespace = null;
        $colonPosition = strpos($name, ':');

        if ($colonPosition !== false) {
            $key = substr($name, 0, $colonPosition);
            $namespace = $this->namespaces[$key] ?? null;
        }

        if ($value === null) {
            $this->pointer = $this->pointer->addChild($name, null, $namespace);
        } else {
            if ($filters = $this->options['filters'] ?? false) {
                foreach ((array) $filters as $filter) {
                    if ($filter instanceof Closure) {
                        $value = $filter($value);
                    } elseif (is_string($filter)) {
                        $value = Filter::$filter($value);
                    }
                }
            }

            $this->pointer->addChild($name, $value, $namespace);
        }

        return $this;
    }

    /**
     * Output XML as string.
     *
     * @return string
     */
    public function output(): string
    {
        return $this->xml->asXML();
    }

    /**
     * Output XML as file.
     *
     * @param  string|null  $filepath
     * @return void
     */
    public function store(string $filepath = null): void
    {
        if ($filepath === null && $this->originalFilepath === null) {
            throw new Exception('Undefined destination');
        }

        $filepath = $filepath ?? $this->originalFilepath;

        if (! $this->xml->asXML($filepath)) {
            throw new Exception('File cannot be output');
        }
    }
}
