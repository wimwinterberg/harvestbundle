<?php
namespace WeAreBuilders\HarvestBundle\Command;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class HarvestDumpDayEntriesCommand
 *
 * @package WeAreBuilders\AutomateBundle\Command
 */
class HarvestDumpDayEntriesCommand extends HarvestCommandAbstract
{
    /**
     * Cache data key
     *
     * @var string
     */
    protected static $cacheDataKeyPrefix = 'dayentries_';

    /**
     * Cache data key for running timers
     *
     * @var string
     */
    protected static $cacheDataKeyRunningTimers = 'dayentries_running';

    /**
     * When day entries are refreshed, this array is used for day entries which are still running
     * This value is cached but directly available so when getRunningDayEntries is called fresh cache might not be available yet, so this value is then used
     *
     * @var array
     */
    protected $runningTimers = null;

    /**
     * Configures the current command.
     *
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('harvest:day-entries:dump')
             ->setDescription('Dump day entries available in harvest')
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
        $dayEntries = $this->getDayEntries($input->getOption(self::OPTION_FORCE_RELOAD));
        $output->writeln(json_encode($dayEntries, JSON_PRETTY_PRINT));

        return 0;
    }

    /**
     * Retrieve day entries
     *
     * @param bool $forceReload (Optional) Defaults to false
     *
     * @return array
     */
    public function getRunningDayEntries($forceReload = false)
    {
        $cache    = $this->getCache();
        $retValue = $cache->fetch(self::$cacheDataKeyRunningTimers);

        if ($retValue == false || $forceReload) {
            $this->getDayEntries($forceReload);
            $retValue = array();

            // new cache is not available yet
            // - check if runningTimers has been set
            if ($this->runningTimers !== null) {
                $retValue = $this->runningTimers;
            }
        }

        return $retValue;
    }

    /**
     * Retrieve day entries
     *
     * @param bool $forceReload (Optional) Defaults to false
     *
     * @return array
     */
    public function getDayEntries($forceReload = false)
    {
        $retValue = array();
        $cache    = $this->getCache();
        $users    = $this->getUsers($forceReload);
        $clients  = $this->getClients($forceReload);
        $projects = $this->getProjects($forceReload);

        // set end date
        $endDate = date('Ymd', strtotime('tomorrow'));

        $configs = array(
            array(
                'startDateTime' => new \DateTime('-1 year'),
                'cacheDataKey'  => self::$cacheDataKeyPrefix . '_yearly',
                'ttl'           => 86400, // 1 day
            ),
            array(
                'startDateTime' => new \DateTime('-2 weeks'),
                'cacheDataKey'  => self::$cacheDataKeyPrefix . '_2weekly',
                'ttl'           => 3600, // 1hour
            ),
            array(
                'startDateTime' => new \DateTime(),
                'cacheDataKey'  => self::$cacheDataKeyPrefix . '_daily',
                'ttl'           => 60, // 1minute
            ),
        );

        foreach ($configs as $config) {
            $isRefreshed = false;
            $data        = $cache->fetch($config['cacheDataKey']);

            if ($data == false || $forceReload) {
                if ($this->runningTimers === null) {
                    $this->runningTimers = array();
                }

                $data          = array();
                $startDateTime = $config['startDateTime'];
                /* @var $startDateTime \DateTime */
                $startDate = $startDateTime->format('Ymd');

                foreach ($users as $user) {

                    $dayEntries = $this->getWrbHarvest()->getTrackedTimeByUser($user['id'], $startDate, $endDate);

                    foreach ($dayEntries as $dayEntry) {

                        // force projects reload when day entry is registered for unknown project
                        if (!isset($projects[$dayEntry->getProjectId()])) {
                            $projects = $this->getProjects(true);
                        }

                        $project = $projects[$dayEntry->getProjectId()];
                        $client  = $clients[$project['client-id']];

                        $data[$dayEntry->getId()] = $dayEntry->dump();

                        // add additional data
                        $data[$dayEntry->getId()]['client-id']      = $client['id'];
                        $data[$dayEntry->getId()]['client-name']    = $client['name'];
                        $data[$dayEntry->getId()]['project-name']   = $project['name'];
                        $data[$dayEntry->getId()]['user-email']     = $user['email'];
                        $data[$dayEntry->getId()]['user-full-name'] = $user['first-name'] . ' ' . $user['last-name'];

                        if ($dayEntry->isRunning()) {
                            $this->runningTimers[$dayEntry->getId()] = $data[$dayEntry->getId()];
                        }
                    }
                }

                $cache->save($config['cacheDataKey'], $data, $config['ttl']);
                $isRefreshed = true;
            }

            $retValue += $data;

            if ($isRefreshed) {

                // save running timers to cache
                $cache->save(self::$cacheDataKeyRunningTimers, $this->runningTimers, 60);

                break; // large period has already be refreshed
            }
        }

        return $retValue;
    }
}