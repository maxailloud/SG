<?php

namespace Sg;

use \Symfony\Component;

/**
 * Global configuration.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
class Configuration
{
    /** @var array */
    private $options = array();

    public function __construct($sourceDirectory)
    {
        $customConfigFile = $sourceDirectory . DIRECTORY_SEPARATOR . 'config.yml';

        $options = array();

        if(true === is_file($customConfigFile))
        {
            $options = Component\Yaml\Yaml::parse($customConfigFile);
        }

        $defaultOptions = Component\Yaml\Yaml::parse(__DIR__ . DIRECTORY_SEPARATOR . 'config.yml');

        $this->options = array_merge($defaultOptions, $options);
    }
    /**
     * @param string $name
     * @return string
     */
    public function getOption($name)
    {
        return (null !== $this->options && isset($this->options[$name])) ? $this->options[$name] : $this->getDefaultConfiguration($name);
    }
}
