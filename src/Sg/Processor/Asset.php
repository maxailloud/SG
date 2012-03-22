<?php

namespace Sg\Processor;

use Symfony\Component;

/**
 * Asset processor class.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
class Asset extends \Sg\Outputter
{
    /** @var string|null */
    protected $source = null;

    /** @var string|null */
    protected $destination = null;

    /**
     * @param string $source
     * @return \Sg\Processor\Template\Asset
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }
    /**
     * @param string $destination
     * @return \Sg\Processor\Template\Asset
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     */
    public function process($sourceDirectory, $destinationDirectory)
    {
//        use Assetic\AssetWriter;
//
//        $writer = new AssetWriter('/path/to/web');
//        $writer->writeManagerAssets($am);
    }
}
