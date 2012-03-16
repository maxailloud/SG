<?php

namespace Sg\Template;

use \Symfony\Component;

class Configuration
{
    /** @var array */
    private $options = array();

    public function __construct($string)
    {
        $yamlParser = new Component\Yaml\Parser();
        $this->options = $yamlParser->parse($string);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getOption($name)
    {
        return (null !== $this->options && isset($this->options[$name])) ? $this->options[$name] : null;
    }
}
