<?php
namespace WeAreBuilders\HarvestBundle\Command;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class HarvestDumpTasksPerProjectCommand
 *
 * @package WeAreBuilders\AutomateBundle\Command
 */
class HarvestDumpTasksPerProjectCommand extends HarvestCommandAbstract
{
    /**
     * Configures the current command.
     *
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('harvest:tasks:dump')
             ->setDescription('Dump tasks of projects of clients')
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
        $tasksPerProject = $this->getTasksPerProjectPerClient($input->getOption(self::OPTION_FORCE_RELOAD));
        $output->writeln(json_encode($tasksPerProject, JSON_PRETTY_PRINT));

        return 0;
    }

    /**
     * Retrieve day entries
     *
     * @param bool $forceReload (Optional) Defaults to false
     *
     * @return array
     */
    public function getTasksPerProjectPerClient($forceReload = false)
    {
        $cacheDataKey = 'tasks.project';
        $cache        = $this->getCache();
        $retValue     = $cache->fetch($cacheDataKey);

        if ($retValue == false || $forceReload) {
            $clients              = $this->getClients($forceReload);
            $projects             = $this->getProjects($forceReload);
            $tasks                = $this->getTasks($forceReload);
            $taskNamesIndexedById = array_column($tasks, 'name', 'id');

            $retValue = array();

            foreach ($clients as $client) {
                $retValue[$client['id']] = array(
                    'id'       => $client['id'],
                    'name'     => $client['name'],
                    'projects' => array()
                );
            }

            foreach ($projects as $project) {
                $taskAssignments = $this->getWrbHarvest()->getTaskAssignmentsByProject($project['id']);

                $tasksOfProject = array();
                foreach ($taskAssignments as $taskAssignment) {
                    $tasksOfProject[$taskAssignment->getId()]         = $taskAssignment->dump();
                    $tasksOfProject[$taskAssignment->getId()]['name'] = $taskNamesIndexedById[$taskAssignment->getTaskId()];
                }

                $retValue[$project['client-id']]['projects'][$project['id']] = array(
                    'id'    => $project['id'],
                    'name'  => $project['name'],
                    'tasks' => $tasksOfProject
                );
            }

            $cache->save($cacheDataKey, $retValue, 3600);
        }

        return $retValue;
    }
}