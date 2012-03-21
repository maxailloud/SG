<?php

namespace Sg;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * SG application.
 *
 */
class Application extends BaseApplication
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('SG', Sg::VERSION);

        $this->getDefinition()->addOption(new InputOption('--licence', '-l', InputOption::VALUE_NONE, 'Display the licence of the application.'));
    }

    /**
     * Runs the current application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->registerCommands();

        if (true === $input->hasParameterOption(array('--licence', '-l'))) {
            $output->writeln(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '../../LICENCE'));

            return 0;
        }

        return parent::doRun($input, $output);
    }

    /**
     * @return void
     */
    protected function registerCommands()
    {
        $finder = new Finder();
        $finder->files()->name('*Command.php')->in(__DIR__ . '/Command');

        foreach($finder as $file)
        {
            $r = new \ReflectionClass('Sg\\Command\\'.$file->getBasename('.php'));
            if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract()) {
                $this->add($r->newInstance());
            }
        }
    }
}
