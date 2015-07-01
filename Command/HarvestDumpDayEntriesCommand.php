<?php
namespace WeAreBuilders\HarvestBundle\Command;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use WeAreBuilders\HarvestBundle\Services\Harvest;

/**
 * Class HarvestDumpDayEntriesCommand
 *
 * @package WeAreBuilders\AutomateBundle\Command
 */
class HarvestDumpDayEntriesCommand extends ContainerAwareCommand
{
    /**
     * Options
     *
     */
    const OPTION_FORCE_RELOAD = 'force-reload';

    /**
     * Cache namespace
     *
     * @var string
     */
    protected static $cacheNamespace = 'harvest.cache';

    /**
     * Cache data key
     *
     * @var string
     */
    protected static $cacheDataKeyPrefix = 'dayentries_';

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
     * Retrieve harvest api interface
     *
     * @return Harvest
     */
    protected function getWrbHarvest()
    {
        return $this->getContainer()->get('wrb_harvest');
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
     * Retrieve projects
     *
     * @param bool $forceReload (Optional) Defaults to false
     * @return array
     * @throws \Exception
     */
    public function getProjects($forceReload = false)
    {
        $cache        = $this->getCache();
        $cacheDataKey = 'projects';

        // retrieve cached retValue
        $retValue = $cache->fetch($cacheDataKey);

        if ($retValue === false || $forceReload) {
            $retValue = array();
            foreach ($this->getWrbHarvest()->getProjects() as $project) {
                $retValue[$project->getId()] = $project->dump();
            }
            $cache->save($cacheDataKey, $retValue, 3600); // 1 day
        }

        return $retValue;
    }

    /**
     * Retrieve clients
     *
     * @param bool $forceReload (Optional) Defaults to false
     * @return array
     * @throws \Exception
     */
    public function getClients($forceReload = false)
    {
        $cache        = $this->getCache();
        $cacheDataKey = 'clients';

        // retrieve cached retValue
        $retValue = $cache->fetch($cacheDataKey);

        if ($retValue === false || $forceReload) {
            $retValue = array();
            foreach ($this->getWrbHarvest()->getClients() as $client) {
                $retValue[$client->getId()] = $client->dump();
            }
            $cache->save($cacheDataKey, $retValue, 3600); // 1 day
        }

        return $retValue;
    }

    /**
     * Retrieve users
     *
     * @param bool $forceReload (Optional) Defaults to false
     * @return array
     * @throws \Exception
     */
    public function getUsers($forceReload = false)
    {
        $cache        = $this->getCache();
        $cacheDataKey = 'users';

        // retrieve cached retValue
        $retValue = $cache->fetch($cacheDataKey);

        if ($retValue === false || $forceReload) {
            $retValue = array();
            foreach ($this->getWrbHarvest()->getActiveUsers() as $user) {
                $retValue[$user->getId()] = $user->dump();
            }
            $cache->save($cacheDataKey, $retValue, 3600); // 1 day
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
                'ttl'           => 1, // 1minute
            ),
        );

        foreach ($configs as $config) {
            $isRefreshed = false;
            $data        = $cache->fetch($config['cacheDataKey']);

            if ($data == false || $forceReload) {
                $data          = array();
                $startDateTime = $config['startDateTime'];
                /* @var $startDateTime \DateTime */
                $startDate = $startDateTime->format('Ymd');

                foreach ($users as $user) {

                    $dayEntries = $this->getWrbHarvest()->getTrackedTimeByUser($user['id'], $startDate, $endDate);

                    foreach ($dayEntries as $dayEntry) {
                        $project = $projects[$dayEntry->getProjectId()];
                        $client  = $clients[$project['client-id']];

                        $data[$dayEntry->getId()] = $dayEntry->dump();

                        // add additional data
                        $data[$dayEntry->getId()]['client-id']    = $client['id'];
                        $data[$dayEntry->getId()]['client-name']  = $client['name'];
                        $data[$dayEntry->getId()]['project-name'] = $project['name'];
                        $data[$dayEntry->getId()]['user-email']   = $user['email'];
                    }
                }

                $cache->save($config['cacheDataKey'], $data, $config['ttl']);
                $isRefreshed = true;
            }

            $retValue += $data;

            if ($isRefreshed) {
                break; // large period has already be refreshed
            }
        }

        return $retValue;
    }

    /**
     * Retrieve cache
     *
     * @return CacheProvider
     */
    protected function getCache()
    {
        $retValue = $this->getContainer()->get('cache');
        /* @var $retValue CacheProvider */
        $retValue->setNamespace(self::$cacheNamespace);

        return $retValue;
    }
}