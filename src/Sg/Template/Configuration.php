<?php

namespace Sg\Template;

use \Symfony\Component;

/**
 * Template configuration class.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
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
     * @return null|string
     */
    public function getOption($name)
    {
        return (null !== $this->options && isset($this->options[$name])) ? $this->options[$name] : null;
    }

    /**
     * @return null|array
     */
    public function getStylesheets()
    {
        $assets = $this->getOption('assets');
        $stylesheets = null;
        if(null !== $assets)
        {
            $stylesheets = (true === array_key_exists('stylesheets', $assets)) ? $assets['stylesheets'] : null;;
        }

        return $stylesheets;
    }

    /**
     * @return null|array
     */
    public function getJavascripts()
    {
        $assets = $this->getOption('assets');
        $stylesheets = null;
        if(null !== $assets)
        {
            $stylesheets = (true === array_key_exists('javascripts', $assets)) ? $assets['javascripts'] : null;;
        }

        return $stylesheets;
    }
}
