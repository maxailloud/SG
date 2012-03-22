<?php

namespace Sg\Phar;

/**
 * Builder of the PHAR archive class.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
class Builder extends \Sg\Outputter
{
    /**
     * @throws \Exception|\RuntimeException
     */
    public function build()
    {
        if (extension_loaded('phar') === false)
        {
            throw new \RuntimeException('Phar extension is mandatory to use this PHAR');
        }

        $pharFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sg.phar';

        if(is_file($pharFile))
        {
            unlink($pharFile);
        }

        try
        {
            $phar = new \Phar($pharFile, 0, 'sg.phar');
            $phar->startBuffering();

            $phar->setMetadata(
                array(
                    'version'       => \Sg\Sg::VERSION,
                    'author'        => 'Maxime AILLOUD',
                    'support'       => 'maxime.ailloud@gmail.com',
                    'description'   => 'Static site generator in PHP',
                    'licence'       => file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LICENCE')
                )
            );

            $phar->setStub($this->getStub());

            $baseDirectory = __DIR__ . '/../../..';

            $phar->buildFromIterator($this->getDirectoryIterator($baseDirectory), $baseDirectory);

            $phar->stopBuffering();

            $this->writeResult(self::OUTPUT_OK, 'PHAR archive generated');
        }
        catch(Exception $exception)
        {
            $this->writeResult(self::OUTPUT_FAIL, sprintf('An error occured when generating the PHAR archive %s'), $exception->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getStub()
    {
        return <<<'STUB'
<?php

Phar::mapPhar('sg.phar');

require_once 'phar://sg.phar/vendor/.composer/autoload.php';

$console = new \Sg\Application('SG', \Sg\Sg::VERSION);
$console->run();

__HALT_COMPILER();
STUB;
    }

    /**
     * @param string $directory
     * @return \Iterator
     */
    private function getDirectoryIterator($directory)
    {
        $fileFinder = new \Symfony\Component\Finder\Finder();
        $fileFinder
            ->files()
            ->ignoreVCS(true)
            ->exclude('.idea')
            ->notName('sg.phar')
            ->notName('.gitignore')
            ->notName('composer.*')
            ->notName('PharCommand.php')
            ->in($directory)
        ;

        return $fileFinder->getIterator();
    }
}
