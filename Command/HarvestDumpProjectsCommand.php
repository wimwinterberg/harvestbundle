<?php
namespace WeAreBuilders\HarvestBundle\Command;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class HarvestDumpProjectsCommand
 *
 * @package WeAreBuilders\AutomateBundle\Command
 */
class HarvestDumpProjectsCommand extends HarvestCommandAbstract
{
    /**
     * Configures the current command.
     *
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('harvest:projects:dump')
             ->setDescription('Dump projects available in harvest')
             ->addOption(self::OPTION_FORCE_RELOAD, 'f', InputOption::VALUE_NONE, 'force reload');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int     null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     * @see    setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entries = $this->getProjects($input->getOption(self::OPTION_FORCE_RELOAD));
        $output->writeln(json_encode($entries, JSON_PRETTY_PRINT));

        return 0;
    }

    /**
     * @inheritdoc
     */
    public function getProjects($forceReload = false)
    {
        return parent::getProjects($forceReload);
    }
}